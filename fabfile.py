#!/usr/bin/env python
# -*- coding: utf-8 -*-
from fabric.api import local, settings, hide, task, abort, sudo, lcd
from fabric.colors import red, green, yellow
from fabric.utils import indent
from jbcore.object import JBObject, JBError
from jbcore.sql import JBDB

jaskinia_path = '/home/jaskinia'
realms_path = '%s/realms' % jaskinia_path
lib_path = '%s/lib' % jaskinia_path
locale_path = '%s/locale/%s/LC_MESSAGES/%s.mo' % (jaskinia_path, '%s', '%s')
config_path = '%s/config' % jaskinia_path
realms_conf_path = '%s/jbcore/include/conf.realms.ini' % lib_path
realms_db_conf_path = '%s/dbpass.ini' % config_path
lighttpd_config_path = '%s/lighttpd.inc' % config_path
source_path = '%s/blx/example' % realms_path

user = 'jaskinia'

def realm_path(realm):
    return '%(rp)s/%(r)s' % {'rp': realms_path, 'r': realm}

@task
def copy(dest_dir):
    """
    Creates new directory for realm (realms/dest_dir) with proper permissions

    @param  string  dest_dir   destination directory name
    """
    dest_path = realm_path(dest_dir)
    with settings(hide('warnings'), warn_only=True):
        if local("test -d %s" % dest_path).succeeded:
            return
    local('sudo su jaskinia -c "cp -r %s %s"' % (source_path, dest_path))
    local('sudo su jaskinia -c "chmod g+w -R %s"' % dest_path)

@task(alias='rcf')
def remove_blx_common_files(dest_dir):
    """
    Removes common Blx files that are linked from magazyn/blx

    @param  string  dest_dir    destination directory (realms/dest_dir/magazyn)
    """
    dest_path = realm_path(dest_dir)
    with lcd(dest_path):
        with lcd('magazyn/css'):
            local('rm content.css  editor.css  elements.css  form.css')
        with lcd('magazyn/js'):
            local('rm site.js  xinha  xinha_conf.js')
            local('rm -r xinha-0.96.1')

@task
def pliki(dest_dir):
    """
    Creates new directory in "pliki" (realms/pliki/dest_dir)
    with proper permissions

    @param  string  dest_dir    destination directory name
    """
    files_path = '%s/pliki/%s' % (realms_path, dest_dir)
    with settings(hide('warnings'), warn_only=True):
        if local("test -d %s" % files_path).succeeded:
            return
    local('sudo su jaskinia -c "mkdir %s"' % files_path)
    local('sudo su jaskinia -c "chmod g+w -R %s"' % files_path)

@task
def magazyn(source_dir, dest_dir):
    """
    Creates symlink from realms/source_dir/magazyn do realms/magazyn/dest_dir

    @param  string  source_dir  source directory name
    @param  string  dest_dir    destination directory name
    """
    source_path = '%s/magazyn' % realm_path(source_dir)
    dest_path = '%s/magazyn/%s' % (realms_path, dest_dir)
    local("test -d %s" % source_path)
    with settings(hide('warnings'), warn_only=True):
        if local("test -d %s" % dest_path).succeeded:
            return
    local('ln -s %s %s' % (source_path, dest_path))

def test_config(cmd, msg, hint, expect_fail=False):
    response = green('OK   ')
    hint_tmp = ''
    with settings(hide('running', 'warnings', 'stdout', 'stderr'),\
        warn_only=True):
        result = local(cmd, capture=True)
        if result.failed != expect_fail:
            response = red('ERROR')
            hint_tmp = '\n' + indent('HINT: %s' % hint, 6)
    print response, msg, hint_tmp

def test_jbcore_realm_config(realm):
    test_config('grep "\[%s\]" %s' % (realm, realms_conf_path),\
        'Checking if realm is set in jbcore configuration',\
        'Add missing configuration to %s' % realms_conf_path)

def test_jbcore_realm_db_config(realm):
    test_config('grep "\[%s\]" %s' % (realm, realms_db_conf_path),\
        'Checking if DB connection for realm is set',\
        'Add missing configuration to %s' % realms_db_conf_path)
    
def test_lighttpd_config(realm):
    test_config('grep "var\.realm = \\"%s\\"" %s' % \
        (realm,lighttpd_config_path),\
        'Checking if realm is set in lighttpd configuration',\
        'Add missing configuration to %s' % lighttpd_config_path)

def test_admin_group(dest_dir):
    source_dir = '%s/run.php' % realm_path(dest_dir)
    test_config('grep "$adminGroup = 1532;" %s' % source_dir,\
        'Checking if admin group is set',\
        'Update site`s administration group in %s' % source_dir,
        expect_fail=True)
    print indent('HINT: check if proper group is set in %s' % source_dir, 6)

def test_generate_board_list(realm):
    path = '%s/pylib/pyapps/queue_proxy/handlers/generate_board_list.php' % \
        jaskinia_path
    print yellow('INFO '), 'Checking if latest discissions are generated for '\
        + 'used board'
    print indent('HINT: check manually in %s' % path, 6);

@task
def mine(id, name, path, group):
    """
    Creates public chamber with given id, name and path for given group.

    @param  string  id      chamber ID
    @param  string  name    chamber name
    @param  string  path    path inside /home/jaskinia/realms/pliki -without it!
    @param  int     group   group that will manage new chamber
    """
    print 'Creating mine chamber'
    try:
        JBObject.setRealm('kopalnia')
    except JBError:
        abort('Couldn`t set realm')

    db = JBDB.instance()
    cur = db.cursor()
    query='insert into chambers values (%s, %s, %s, %s::int[], %s::int[], ' \
        + '%s::int[], %s::int[], %s::int[], %s::int[], %s)'
    params = (id, name, path, None, [group], [group], \
        None, None, None, False)
    cur.execute(query, params)
    db.connect().commit()

@task
def lang(source_dir, dest_realm, lang='pl_PL'):
    """
    Compiles locale file (lang.po) for given realm (realms/source_dir/locale/)
    and saves result (.mo) into given directory
    (locale/lang/LC_MESSAGES/dest_realm.mo)

    @param  string  source_dir  source realm dir (realms/source_dir/locale/)
    @param  string  dest_realm  destination realm name (dest_realm.mo)
    @param  string  lang        language file to compile (pl_PL by default)
    """
    lang_path = '%s/locale/%s.po' % (realm_path(source_dir), lang)
    dest_path = locale_path % (lang, dest_realm)
    local('test -e %s' % lang_path)
    local('sudo su jaskinia -c "msgfmt %s -o %s"' % (lang_path, dest_path))
    local('sudo /etc/rc.d/init.d/fcgi restart jaskinia')

@task
def db(realm):
    """
    Creates default pages/articles in blx.pages

    @param  string  realm   realm that will own pages
    """
    print 'Creating default pages'
    try:
        JBObject.setRealm(realm)
    except JBError:
        pass
    db = JBDB.instance()
    cur = db.cursor()
    query = 'select blx.set_page(%s, %s, %s, %s, %s)'
    params = [('index.html', realm, u'Strona główna', 108, u'[news]'),\
        ('_special.html', realm, u'Strony specjalne', 108, \
            u'<ul><li><a href="/_special/menu.html">Menu (PL)</li></ul>'),
        ('_special/menu.html', realm, u'Menu (PL)', 108, \
            u'<ul><li>Forum</li><li>O stronie</li></ul>')]
    cur.executemany(query, params)
    db.connect().commit()

@task
def test(realm, dest_dir):
    """
    Tests verious configuration files (realms, dbpass, lighttpd)

    @param  string  realm   realm to check
    """
    test_jbcore_realm_config(realm)
    test_jbcore_realm_db_config(realm)
    test_lighttpd_config(realm)
    test_generate_board_list(realm)
    test_admin_group(dest_dir)

@task(default=True)
def deploy(realm, group):
    copy(realm)
    remove_blx_common_files(realm)
    lang(realm, realm)
    pliki(realm)
    magazyn(realm, realm)
    db(realm)
    print yellow('INFO '), 'Run command  mine:%s,"%s",%s,%s' % \
        (realm, realm.capitalize(), realm, group)
    # following step requres re-setting JB_REALM
    # which is impossible and script fails
    #mine(realm, realm.capitalize(), realm, group)
    test(realm, realm)
