<?php
namespace Blx\Plugin;

class StaticFile {
    protected $file;
    protected $tag = '[content]';

    public function __construct( $file ) {
        $this->file = $file;
    }
    public function update( \sfEvent $event ) {
        if ( !file_exists( $this->file ) ) {
            return;
        }
        $layout = file_get_contents( $this->file );
        $event->setReturnVAlue( str_replace( $this->tag, $event['content'], $layout ) );
        return true;
    }
}
