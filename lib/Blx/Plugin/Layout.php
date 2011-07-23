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
        $layout = file_get_contents( $this->file );
        return str_replace( $this->tag, $output, $layout );
    }
}
