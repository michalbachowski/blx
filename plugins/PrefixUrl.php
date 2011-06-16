<?php
namespace Blx\Plugin;

class PrefixUrl {
    protected $prefix;

    public function __construct( $prefix ) {
        $this->prefix = $prefix;
    }
    public function filter( \sfEvent $event, $url ) {
        return $this->prefix . $url;
    }
}
