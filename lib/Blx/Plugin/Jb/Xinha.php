<?php
namespace Blx\Plugin\Jb;

class Xinha extends \Blx\Plugin\Js {
    protected $addXinha = false;

    protected $mapping = array(
        'filter.output' => 'output',
        'plugin.editable.filter.form' => 'init'
    );

    public function __construct() {
    }

    public function init( \sfEvent $event, $content ) {
        $this->addXinha = true;
        return $content;
    }

    public function output( \sfEvent $event, $content ) {
        if ( !$this->addXinha ) {
            return $content;
        }
        $this->url = \JBUi::magazyn( 'js/xinha_conf.js', 'blx' );
        $content = parent::output( $event, $content );
        $this->url = \JBUi::magazyn( 'js/xinha/XinhaCore.js', 'blx' );
        return parent::output( $event, $content );
    }

}
