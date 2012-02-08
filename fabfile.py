#!/usr/bin/env python
# -*- coding: utf-8 -*-
from fabric.api import local, settings, hide, task, abort, sudo
from fabric.colors import red, green, yellow
from fabric.utils import indent
from jbcore.object import JBObject
from jbcore.sql import JBDB

jaskinia_path = '/home/jaskinia'
realms_path = '%s/realms' % jaskinia_path
lib_path = '%s/lib' % jaskinia_path
config_path = '%s/config' % jaskinia_path
realms_conf_path = '%s/jbcore/include/conf.realms.ini' % lib_path
realms_db_conf_path = '%s/dbpass.ini' % config_path
lighttpd_config_path = '%s/lighttpd.inc' % config_path
source_path = '%s/blx/example' % realms_path

user = 'jaskinia'

def realm_path(realm):
    return '%(rp)s/%(r)s' % {'rp': realms_path, 'r': realm}

@task
def copy(realm):
    dest_path = realm_path(realm)
    with settings(hide('warnings'), warn_only=True):
        if local("test -d %s" % dest_path).succeeded:
            return
    local('sudo su jaskinia -c "cp -r %s %s"' % (source_path, dest_path))
    local('sudo su jaskinia -c "chmod g+w -R %s"' % dest_path)

@task
def magazyn(realm):
    source_path = '%s/magazyn' % realm_path(realm)
    dest_path = '%s/magazyn/%s' % (realms_path, realm)
    local("test -d %s" % source_path)
    with settings(hide('warnings'), warn_only=True):
        if local("test -d %s" % dest_path).succeeded:
            return
    local('ln -s %s %s' % (source_path, dest_path))

def test_config(cmd, msg, hint):
    response = green('OK   ')
    hint_tmp = ''
    with settings(hide('running', 'warnings', 'stdout', 'stderr'),\
        warn_only=True):
        result = local(cmd, capture=True)
        if result.failed:
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

def test_generate_board_list(realm):
    path = '%s/pylib/pyapps/queue_proxy/handlers/generate_board_list.php' % \
        jaskinia_path
    print yellow('INFO '), 'Checking if latest discissions are generated for '\
        + 'used board'
    print indent('HINT: check manually in %s' % path, 6);

@task
def db(realm):
    print 'Creating default pages'
    JBObject.setRealm(realm)
    db = JBDB.instance()
    cur = db.cursor()
    query = 'select blx.set_page(%s, %s, %s, %s, %s)'
    params = [('index.html', realm, u'Strona główna', 108, u'[news]'),\
        ('_special/menu.html', realm, u'Menu (PL)', 108, \
            u'<ul><li>Forum</li><li>O stronie</li></ul>')]
    cur.executemany(query, params)
    db.connect().commit()

@task
def test(realm):
    test_jbcore_realm_config(realm)
    test_jbcore_realm_db_config(realm)
    test_lighttpd_config(realm)
    test_generate_board_list(realm)

@task(default=True)
def deploy(realm):
    copy(realm)
    magazyn(realm)
    db(realm)
    test(realm)
