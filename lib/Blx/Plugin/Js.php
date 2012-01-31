<?php
namespace Blx\Plugin;

class Js extends \Blx\Plugin {
    protected $prepend = '</body>';

    protected $mapping = array(
        'filter.output' => 'output',
    );

    protected $url;

    public function __construct( $url ) {
        $this->url = $url;
    }

    protected function prepareTag( $url ) {
        return '<script type="text/javascript" src="' . $url . '"></script>';
    }

    public function output( \sfEvent $event, $content ) {
        return str_replace( $this->prepend, $this->prepareTag( $this->url ) . $this->prepend, $content );
    }
}
