<?php
$adminGroup = 5140;
$newsRealm = null; // NULL for default
# $editor = ''; // to disable Xinha
# $editor = new Blx\Plugin\Jb\Markup(); // to use markup editor (like on our board)
$appDir = dirname( __FILE__ );
require 'init.php';

# $request->addPlugin( new Blx\Plugin\Jb\FixBetaUrl() );

# dispatch
$request->dispatch();
