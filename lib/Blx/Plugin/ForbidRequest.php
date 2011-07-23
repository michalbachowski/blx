<?php
namespace Blx\Plugin;

class ForbidRequest extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'filter',
    );

    protected $regexp;

    public function __construct( $regexp ) {
        $this->regexp = $regexp;
    }
    public function filter( \sfEvent $event ) {
        if ( isset( $event['args']['edit'] ) ) {
            return;
        }
        if ( preg_match( $this->regexp, $event['url'] ) ) {
            throw new \Blx\Http404Error();
        }
    }
}
