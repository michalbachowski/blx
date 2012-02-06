<?php
namespace Blx\Plugin;

class Moved extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'start',
        'filter.response.normal' => 'filter'
    );
    protected $pattern = '!\[moved:(.+?)\]!s';

    public function start( \sfEvent $event ) {
        // remember request instance
        $this->request = $event->getSubject();
    }

    public function filter( \sfEvent $event, $content ) {
        return preg_replace_callback(
            $this->pattern,
            array( $this, 'filterCallback' ),
            $content
        );
    }

    protected function filterCallback( $matches ) {
        $url = $this->request->getUtil()->fixInnerUrl( $matches[1] );
        if ( $url ) {
            $this->request->redirectToPage( $url );
        }
    }

}
