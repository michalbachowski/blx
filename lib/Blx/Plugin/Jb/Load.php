<?php
namespace Blx\Plugin\Jb;

class Load extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'update',
        'filter.output' => 'filter'
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
    	bind_textdomain_codeset( 'blx', JB_ENCODING );
	    bindtextdomain( 'blx', JB_LOCALE_DIR );

        // fix url pattern
        $util = $event->getSubject()->getUtil();
        $util->setUrlPattern( \Url::make( $util->getUrlPattern() ) );
    }

    public function filter( \sfEvent $event, $content ) {
        $replacements = array(
            '[jb_url]' => \Url::make( '', '', 'heroes' ),
            '[jb_title]' => \_c( 'Go to Behemoth`s Lair main page' ),
            '[jb_name]' => \_c( 'Behemoth`s Lair' ),
            '[jb_lang]' => \JBCore::lang()
        );
        return strtr( $content, $replacements ); 
    }
}
