<?php
namespace Blx\Plugin\Jb;

class FixBetaUrl extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'fix'
    );
    protected $prefix = 'nowe.';

    public function __construct( $prefix=null ) {
        if ( $prefix ) {
            $this->prefix = $prefix;
        }
    }

    public function fix( \sfEvent $event ) {
        $util = $event->getSubject()->getUtil();
        $util->setUrlPattern( str_replace( 'http://', 'http://' . $this->prefix, $util->getUrlPattern() ) );
    }
}
