<?php
namespace Blx\Plugin;

class StaticFile {
    protected $file;

    public function __construct( $file, $tag = '[content]' ) {
        $this->file = $file;
        $this->tag = $tag;
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
