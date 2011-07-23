<?php
namespace Blx\Plugin;

class FileFromDirectory extends \Blx\Plugin {
    protected $mapping = array(
        'handle.get' => 'update',
    );
    protected $directory;

    public function __construct( $dir ) {
        $this->directory = $dir;
    }
    public function update( \sfEvent $event ) {
        if ( !is_dir( $this->directory ) ) {
            return;
        }
        $path = realpath(
            $this->directory .
            DIRECTORY_SEPARATOR .
            ltrim(
                $event['url'],
                DIRECTORY_SEPARATOR
            )
        );
        if ( strpos( $path, $this->directory ) !== 0 ) {
            return;
        }
        if ( !file_exists( $path ) ) {
            return;
        }
        $event->setReturnValue( file_get_contents( $path ) );
        return true;
    }
}
