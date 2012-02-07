<?php
namespace Blx\Plugin\Jb;

class RemovePlLanguage extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url' => 'fix'
    );

    public function fix( \sfEvent $event, $url ) {
        if ( strpos( $url, 'pl/' ) === 0 ) {
            return substr( $url, 3 );
        }
        return $url;
    }
}
