<?php
$adminGroup = 1532;
$newsRealm = null; // NULL for default
$appDir = dirname( __FILE__ );
require 'init.php';

# dispatch
$request->dispatch();
