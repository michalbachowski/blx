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
        # no language - skip
        if ( !$this->language ) {
            return $url;
        }
        # check whether URL contains default language
        # "xx/foo/bar.html"
        if ( strpos( $url, $this->language . '/' ) === 0 ) {
            $url = substr( $url, strlen( $this->language) + 1 );
        # "xx.html"
        } elseif (strpos( $url, $this->language . '.' ) === 0) {
            $url = substr( $url, strlen( $this->language) );
        # no language in URL - skip
        } else {
            return $url;
        }
        # we have URL of current page - redirect
        if ( null === $this->baseUrl ) {
            $event->getSubject()->redirectToPage( $url );
        }
        return $url;
    }
}
