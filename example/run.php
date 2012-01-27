<?php
error_reporting(E_ALL);
set_include_path( get_include_path() . ':' . realpath( dirname( __FILE__ ) . '/../lib' ) );
require 'Blx/Loader.php';
require 'sf/sfEventDispatcher.php';

spl_autoload_register( array( new Blx\Loader(), 'autoload' ) );

# prepare utility class
$util = new Blx\Util( 'http://poligon.heroes.net.pl/mib/blx/example/run.php?url=%s' );

# initiate request
$request = new Blx\Request( $util );

# load basic output plugins
$request->addPlugin( new Blx\Plugin\DefaultUrl( 'index.html' ) )
    ->addPlugin( new Blx\Plugin\Jb\Load( 'h6' ) )
    ->addPlugin( new Blx\Plugin\ForbidRequest( '!^/template/!' ) )
    ->addPlugin( new Blx\Plugin\Jb\Acl( Blx\Plugin\Jb\Acl::ALLOW ) )
    ->addPlugin( new Blx\Plugin\Editable() )
    ->addPlugin( new Blx\Plugin\Redirect( '/nowiny', '/' ) )
    ->addPlugin( new Blx\Plugin\Jb\Comments() )
    ->addPlugin( new Blx\Plugin\Jb\News() )
    ->addPlugin( new Blx\Plugin\Jb\DbStorage() )
//    ->addPlugin( new Blx\Plugin\StaticFile( dirname( __FILE__ ) . '/pages/index.html' ) )
    ->addPlugin( new Blx\Plugin\Error404() )
    ->addPlugin( new Blx\Plugin\Layout( dirname( __FILE__ ) . '/layout/default.html' ) )
    ->addPlugin( new Blx\Plugin\Title( 'Heroes VI, Behemot`s Lair' ) )
    ->addPlugin( new Blx\Plugin\Jb\Ui() )
    ->addPlugin( new Blx\Plugin\Display() );
# dispatch
$request->dispatch();
