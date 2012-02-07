<?php
namespace Blx\Plugin\Jb;

class RemoveDefaultLanguage extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url' => 'fix'
    );
    protected $baseUrl;
    protected $language='pl';

    public function __construct( $language = 'pl' ) {
        $this->language = $language;
    }

    public function fix( \sfEvent $event, $url ) {
        $tmp = $this->_fix( $event, $url );
        $this->baseUrl = $url;
        return $tmp;
    }

    protected function _fix( \sfEvent $event, $url ) {
        if ( !$this->language ) {
            return $url;
        }
        if ( strpos( $url, $this->language . '/' ) !== 0 ) {
            return $url;
        }
        $url = substr( $url, strlen( $this->language) + 1 );
        if ( null === $this->baseUrl ) {
            $event->getSubject()->redirectToPage( $url );
        }
        return $url;
    }
}
