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

# load basic output plugin (yes, request is also output plugin)
$d = $request->getDispatcher();
$d->connect( 'dispatch.stop', array( $request, 'display' ) );
$d->connect( 'handle.error', array( $request, 'handle404' ) );

$d->connect(
    'dispatch.start',
    array(
        new Blx\Plugin\Jb\Load( 'heroes' ),
        'update'
    )
);

$d->connect(
    'filter.url',
    array(
        new Blx\Plugin\DefaultUrl( 'index.html' ),
        'filter'
    )
);

/**
$d->connect(
    'filter.url',
    array(
        new Blx\Plugin\PrefixUrl( '/h6/' ),
        'filter'
    )
);
 */

$d->connect(
    'filter.url',
    array(
        new Blx\Plugin\Jb\Acl( Blx\Plugin\Jb\Acl::ALLOW, array( 'test.html' => 1532 ) ),
        'filter'
    )
);

$d->connect(
    'handle.get',
    array(
        new Blx\Plugin\FileFromDirectory( dirname( __FILE__ ) . '/webroot/' ),
        'update'
    )
);

$d->connect(
    'handle.get',
    array(
        new Blx\Plugin\StaticFile( dirname( __FILE__ ) . '/webroot/test.html' ),
        'update'
    )
);

# dispatch
$request->dispatch();
