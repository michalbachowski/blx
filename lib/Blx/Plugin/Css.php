<?php
namespace Blx\Plugin;

class Css extends \Blx\Plugin {
    protected $prepend = '</head>';

    protected $mapping = array(
        'filter.output' => 'output',
    );

    public function __construct( $url, $media='screen, tv, projection' ) {
        $this->url = $url;
        $this->media = $media;
    }

    protected function prepareTag( $url, $media ) {
        return '<link rel="stylesheet" href="' . $url . '" media="'. $media .'" />';
    }

    public function output( \sfEvent $event, $content ) {
        return str_replace( $this->prepend, $this->prepareTag( $this->url, $this->media ) . $this->prepend, $content );
    }
}
