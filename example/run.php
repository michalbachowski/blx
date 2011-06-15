<?php

require 'lib/Blx.php';

# prepare arguments
list( $url, $args, $method ) = Blx\prepareArguments();

# initiate request
$request = new Blx\Request( $url, $args, $method );

# load basic output plugin (yes, request is also output plugin)
$d = $request->getDispatcher();
$d->connect( 'dispatch.stop', array( $request, 'display' ) );
$d->connect( 'handle.error', array( $request, 'handle404' ) );

# aux plugins
$d->connect(
    'handle.get',
    array(
        new Blx\Plugin\StaticFile( '/home/users/mib/blx/webroot/test.html' ),
        'update'
    )
);

# dispatch
$request->dispatch();
