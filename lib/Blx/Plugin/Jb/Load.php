<?php
namespace Blx\Plugin\Jb;

class Load extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'update',
        'filter.output' => 'filter',
        'filter.response.normal' => 'filter'
    );
    protected $realm;
    protected $groups;

    public function __construct( $group=null, $realm=null ) {
        $this->group = $group;
        $this->realm = $realm ?:  $_SERVER['X_REALM'];
    }

    public function init() {
        // load core
        define( 'JB_REALM', $this->realm );
        require 'jbcore/jbcore.php';

        // load Blx translations
    	bind_textdomain_codeset( 'blx', JB_ENCODING );
	    bindtextdomain( 'blx', JB_LOCALE_DIR );

        // add REALM_PATH/app to include path
        set_include_path( get_include_path() . PATH_SEPARATOR . JB_REALM_PATH . 'app' );
        
        // fix url pattern
        $this->util->setUrlPattern( \Url::make( $this->util->getUrlPattern() ) );

        // permissions
        if ( $this->group ) {
            $group = new \JBPolicyGroup( $this->group );
            \JBPerm::set( 'perm.' . JB_REALM . '.post', $group );
            \JBPerm::set( 'editable.form', $group );
        }
        $editors = new \JBPolicyAchievement( 4 );
        \JBPerm::set( 'perm.' . JB_REALM . '.post', $editors );
        \JBPerm::set( 'editable.form', $editors );
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
