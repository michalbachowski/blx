<?php
namespace Blx\Plugin;

class Metadata {
    protected $path;
    protected $data;

    public function __construct( $path ) {
        $this->file = $path;
    }
    protected function load() {
        if ( !file_exists( $this->path ) ) {
            throw new MetadataFileNotFoundError();
        }
        if ( !is_readable( $this->path ) ) {
            throw new MetadataFileIsNotReadableError();
        }
        $this->data = parse_ini_file( $this->path, true );
    }
    public function update( \sfEvent $event ) {
        if ( null === $this->data ) {
            $this->load();
        }
        if ( !isset( $this->data[$event['key']] ) ) {
            return false;
        }
        $event->setReturnValue( $this->cache[$event['key']] );
        return true;
    }
}
