<?php
require 'Blx/Loader.php';
require 'sf/sfEventDispatcher.php';

spl_autoload_register( array( new Blx\Loader(), 'autoload' ) );

# prepare utility class
$util = new Blx\Util();

# initiate request
$request = new Blx\Request( $util );

# load basic output plugins
$request->addPlugin( new Blx\Plugin\DefaultUrl( 'index.html' ) )
    ->addPlugin( new Blx\Plugin\Jb\Load() )
//    ->addPlugin( new Blx\Plugin\Jb\FixBetaUrl() )
    ->addPlugin( new Blx\Plugin\DhtmlToHtml() )
    ->addPlugin( new Blx\Plugin\ForbidRequest( '!^/template/!' ) )
    ->addPlugin( new Blx\Plugin\Jb\Acl( Blx\Plugin\Jb\Acl::ALLOW ) )
    ->addPlugin( new Blx\Plugin\Editable() )
    ->addPlugin( new Blx\Plugin\Redirect( '/nowiny', '/' ) )
    ->addPlugin( new Blx\Plugin\Jb\Comments() )
    ->addPlugin( new Blx\Plugin\Jb\Board() )
    ->addPlugin( new Blx\Plugin\Jb\News() )
    ->addPlugin( new Blx\Plugin\Jb\Gallery() )
    ->addPlugin( new Blx\Plugin\Jb\DbStorage() )
    ->addPlugin( new Blx\Plugin\Moved() )
    ->addPlugin( new Blx\Plugin\Jb\RemoveEmptyParagraphs() )
//    ->addPlugin( new Blx\Plugin\StaticFile( dirname( __FILE__ ) . '/pages/index.html' ) )
    ->addPlugin( new Blx\Plugin\Error404() )
    ->addPlugin( new Blx\Plugin\Layout( dirname( __FILE__ ) . '/template/default.html' ) )
    ->addPlugin( new Blx\Plugin\IncludeContent() )
    ->addPlugin( new Blx\Plugin\Jb\Title( 'Example' ) )
    ->addPlugin( new Blx\Plugin\Jb\Ui() )
    ->addPlugin( new Blx\Plugin\Jb\Xinha() )
    ->addPlugin( new Blx\Plugin\Jb\Js( 'js/site.js', 'blx' ) )
    ->addPlugin( new Blx\Plugin\Jb\Css( 'css/editor.css', 'blx' ) )
    ->addPlugin( new Blx\Plugin\Jb\Css( 'css/content.css', 'blx' ) )
    ->addPlugin( new Blx\Plugin\Display() );
# dispatch
$request->dispatch();
