<?php
namespace Blx\Plugin;

class StaticFile extends \Blx\Plugin {
    protected $mapping = array(
        'handle.get' => 'update',
    );
    protected $file;

    public function __construct( $file ) {
        $this->file = $file;
    }
    public function update( \sfEvent $event ) {
        if ( !file_exists( $this->file ) ) {
            return;
        }
        $event->setReturnValue( file_get_contents( $this->file ) );
        return true;
    }
}
