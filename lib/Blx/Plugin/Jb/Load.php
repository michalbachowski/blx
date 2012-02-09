<?php
namespace Blx\Plugin\Jb;

class Load extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'update',
        'filter.output' => 'filter'
    );
    protected $realm;
    protected $groups;

    public function __construct( $group=null, $realm=null ) {
        $this->group = $group ?: 1532;
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

        // add REALM_PATH/app to include path
        set_include_path( get_include_path() . PATH_SEPARATOR . JB_REALM_PATH . 'app' );
        
        // fix url pattern
        $util = $event->getSubject()->getUtil();
        $util->setUrlPattern( \Url::make( $util->getUrlPattern() ) );

        // permissions
        \JBPerm::set( 'perm.' . JB_REALM . '.post', new \JBPolicyGroup( $this->group ) );
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
