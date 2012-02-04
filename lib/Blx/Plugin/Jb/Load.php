<?php
namespace Blx\Plugin\Jb;

class Load extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'update'
    );
    protected $realm;

    public function __construct( $realm=null ) {
        $this->realm = $realm ?:  $_SERVER['X_REALM'];
    }

    public function update( \sfEvent $event ) {
        // load core
        define( 'JB_REALM', $this->realm );
        define( 'JB_DEBUG', 1 );
        require 'jbcore/jbcore.php';

        // load Blx translations
    	bind_textdomain_codeset( JB_REALM, JB_ENCODING );
	    bindtextdomain( JB_REALM, JB_LOCALE_DIR );

        // fix url pattern
        $util = $event->getSubject()->getUtil();
        $util->setUrlPattern( \Url::make( $util->getUrlPattern() ) );
    }
}
