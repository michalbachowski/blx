<?php
error_reporting(E_ALL);
set_include_path( get_include_path() . ':' . realpath( dirname( __FILE__ ) . '/../lib' ) );
require 'Blx/Loader.php';
require 'sf/sfEventDispatcher.php';

spl_autoload_register( array( new Blx\Loader(), 'autoload' ) );

# prepare arguments
list( $url, $args, $method ) = Blx\Util::prepareArguments();

# initiate request
$request = new Blx\Request( $url, $args, $method );

# load basic output plugins
$request->addPlugin( new Blx\Plugin\DefaultUrl( 'index.html' ) );
$request->addPlugin( new Blx\Plugin\Jb\Load( 'heroes' ) );
$request->addPlugin( new Blx\Plugin\Jb\Acl( Blx\Plugin\Jb\Acl::ALLOW, array( 'test.html' => 1532 ) ) );
$request->addPlugin( new Blx\Plugin\Editable() );
#$request->addPlugin( new Blx\Plugin\Jb\DbStorage() );
$request->addPlugin( new Blx\Plugin\StaticFile( dirname( __FILE__ ) . '/pages/index.html' ) );
$request->addPlugin( new Blx\Plugin\Display() );
$request->addPlugin( new Blx\Plugin\Error404() );

# dispatch
$request->dispatch();
