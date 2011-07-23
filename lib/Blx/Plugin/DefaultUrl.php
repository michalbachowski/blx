<?php
namespace Blx\Plugin;

class DefaultUrl extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url'    => 'filter'
    );
    protected $url;

    public function __construct( $url ) {
        $this->url = $url;
    }
    public function filter( \sfEvent $event, $value ) {
        if ( empty( $value ) ) {
            $value = $this->url;
        }
        return $value;
    }
}
