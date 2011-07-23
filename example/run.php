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
$request->addPlugin( new Blx\Plugin\DefaultUrl( 'index.html' ) )
    ->addPlugin( new Blx\Plugin\Jb\Load( 'heroes' ) )
#    ->addPlugin( new Blx\Plugin\ForbidRequest( '!^/template/!' ) )
    ->addPlugin( new Blx\Plugin\Jb\Acl( Blx\Plugin\Jb\Acl::ALLOW, array( 'test.html' => 1532 ) ) )
    ->addPlugin( new Blx\Plugin\Editable() )
#    ->addPlugin( new Blx\Plugin\Jb\DbStorage() )
    ->addPlugin( new Blx\Plugin\StaticFile( dirname( __FILE__ ) . '/pages/index.html' ) )
    ->addPlugin( new Blx\Plugin\Error404() )
    ->addPlugin( new Blx\Plugin\Layout( dirname( __FILE__ ) . '/layout/default.html' ) )
    ->addPlugin( new Blx\Plugin\Display() )
;
# dispatch
$request->dispatch();
