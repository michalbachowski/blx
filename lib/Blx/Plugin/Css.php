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

    protected function prepareTag() {
        return '<link rel="stylesheet" href="' . $this->url . '" media="'. $this->media .'" />';
    }

    public function output( \sfEvent $event, $content ) {
        return str_replace( $this->prepend, $this->prepareTag() . $this->prepend, $content );
    }
}
