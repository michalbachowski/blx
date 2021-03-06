<?php
namespace Blx\Plugin\Jb;

class Xinha extends \Blx\Plugin\Jb\Js {
    protected $addXinha = false;
    protected $request;

    protected $mapping = array(
        'dispatch.start' => 'prepare',
        'filter.output' => 'output',
        'plugin.editable.filter.form' => 'initXinha'
    );

    public function __construct() {}

    public function prepare( \sfEvent $event ) {
        $this->request = $event->getSubject();
    }

    public function initXinha( \sfEvent $event, $content ) {
        $this->addXinha = true;
        $this->realm = 'blx';
        // append inline JS Script
        $this->request->addPlugin(
            new \Blx\Plugin\InlineJs( sprintf( 'var siteRealm = "%s";', JB_REALM ) )
        );
        return $content;
    }

    public function output( \sfEvent $event, $content ) {
        if ( !$this->addXinha ) {
            return $content;
        }
        // append external scripts
        $this->url = 'js/xinha_conf.js';
        $content = parent::output( $event, $content );
        $this->url = 'js/xinha/XinhaCore.js';
        return parent::output( $event, $content );
    }

}
