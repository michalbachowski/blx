<?php
namespace Blx\Plugin\Jb;

class RemovePlLanguage extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url' => 'fix'
    );
    protected $baseUrl;

    public function fix( \sfEvent $event, $url ) {
        $tmp = $this->_fix( $event, $url );
        $this->baseUrl = $url;
        return $tmp;
    }

    protected function _fix( \sfEvent $event, $url ) {
        if ( strpos( $url, 'pl/' ) !== 0 ) {
            return $url;
        }
        $url = substr( $url, 3 );
        if ( null === $this->baseUrl ) {
            $event->getSubject()->redirectToPage( $url );
        }
        return $url;
    }
}
