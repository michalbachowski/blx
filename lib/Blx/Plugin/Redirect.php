<?php
namespace Blx\Plugin;

class Redirect extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url'    => 'filter'
    );
    protected $from;
    protected $to;

    public function __construct( $from, $to ) {
        $this->from = $from;
        $this->to = $to;
    }
    public function filter( \sfEvent $event, $value ) {
        if ( $value == $event->getSubject()->getUtil()->fixInnerUrl( $this->from ) ) {
            $event->getSubject()->redirectToPage( $to );
        }    
        return $value;
    }
}
