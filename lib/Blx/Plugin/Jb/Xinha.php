<?php
namespace Blx\Plugin\Jb;

class Xinha extends \Blx\Plugin\Jb\Js {
    protected $addXinha = false;

    protected $mapping = array(
        'filter.output' => 'output',
        'plugin.editable.filter.form' => 'init'
    );

    public function __construct() {
        $this->realm = JB_REALM;
    }

    public function init( \sfEvent $event, $content ) {
        $this->addXinha = true;
        return $content;
    }

    public function output( \sfEvent $event, $content ) {
        if ( !$this->addXinha ) {
            return $content;
        }
        $this->url = 'js/xinha_conf.js';
        $content = parent::output( $event, $content );
        $this->url = 'js/xinha/XinhaCore.js';
        return parent::output( $event, $content );
    }

}
