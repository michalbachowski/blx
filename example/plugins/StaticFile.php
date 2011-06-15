<?php
namespace Blx\Plugin;

class StaticFile {
    protected $file;

    public function __construct( $file ) {
        $this->file = $file;
    }
    public function update( sfEvent $event ) {
        if ( !file_exists( $this->file ) ) {
            return;
        }
        $event->setReturnValue( file_get_contents( $this->file ) );
        return true;
    }
}
