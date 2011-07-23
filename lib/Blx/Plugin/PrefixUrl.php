<?php
namespace Blx\Plugin;

class PrefixUrl extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url' => 'filter',
    );

    protected $prefix;

    public function __construct( $prefix ) {
        $this->prefix = $prefix;
    }
    public function filter( \sfEvent $event, $url ) {
        return $this->prefix . $url;
    }
}
