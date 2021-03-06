<?php
namespace Blx\Plugin;

class IncludeContent extends \Blx\Plugin  {
    protected $mapping = array(
        'filter.response.normal' => 'filter',   // filter content
        'filter.output' => 'filter'             // filter layout
    );
    protected $pattern = '!\[include\:(.+?)\]!';

    public function filter( \sfEvent $event, $content ) {
        $request = $this->request;
        $callback = function( $matches ) use ( $request ) {
            return $request->loadContent( \Blx\Request::GET, $matches[1] );
        };
        return preg_replace_callback(
            $this->pattern,
            $callback,
            $content
        );
    }
}
