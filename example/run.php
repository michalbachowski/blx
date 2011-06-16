<?php
require '../lib/Blx.php';

# prepare arguments
list( $url, $args, $method ) = Blx\prepareArguments();

# initiate request
$request = new Blx\Request( $url, $args, $method );

# load basic output plugin (yes, request is also output plugin)
$d = $request->getDispatcher();
$d->connect( 'dispatch.stop', array( $request, 'display' ) );
$d->connect( 'handle.error', array( $request, 'handle404' ) );

# aux plugins
require '../plugins/StaticFile.php';
require '../plugins/FileFromDirectory.php';
require '../plugins/DefaultUrl.php';
require '../plugins/PrefixUrl.php';
require '../plugins/jb/Acl.php';
require '../plugins/jb/Load.php';

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
