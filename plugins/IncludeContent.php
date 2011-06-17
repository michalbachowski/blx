<?php
namespace Blx\Plugin;

class IncludeContent {
    protected $pattern = '!\{\{include\:(.+?)\}\}!';

    public function filter( \sfEvent $event, $content ) {
        $callback = function( $matches ) use ( $event ) {
            return $event->getSubject()->loadContent( \Blx\Request::GET, $matches[1] );
        }
        return preg_replace_callback(
            $this->pattern,
            $callback,
            $content
        );
    }
}
