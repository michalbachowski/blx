<?php
$adminGroup = 1532;
$newsRealm = null; // NULL for default
$appDir = dirname( __FILE__ );
require 'init.php';

# $request->addPlugin( new Blx\Plugin\Jb\FixBetaUrl() );

# dispatch
$request->dispatch();
