<?php
namespace Blx\Plugin;

class Layout extends \Blx\Plugin {
    protected $mapping = array(
        'filter.output' => 'filter',
    );
    protected $file;
    protected $tag = '[content]';

    public function __construct( $file ) {
        $this->file = $file;
    }
    public function filter( \sfEvent $event, $output ) {
        if ( !file_exists( $this->file ) ) {
            return $output;
        }
        $layout = $this->dispatcher->filter(
            new \sfEvent( $this, 'filter.response.normal'),
            file_get_contents( $this->file )
        )->getReturnValue();
        return str_replace( $this->tag, $output, $layout );
    }
}
