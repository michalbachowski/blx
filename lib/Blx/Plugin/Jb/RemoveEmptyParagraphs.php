<?php
namespace Blx\Plugin\Jb;

class RemoveEmptyParagraphs extends \Blx\Plugin {
    protected $mapping = array(
        'handle.post' => 'post',
        'filter.response.normal' => 'filterOutput'
    );

    protected function filter( $content ) {
        return \JBFormatter::correctBlockLevelTags( $content, false );
    }

    public function post( \sfEvent $event ) {
        if ( !isset( $event['arguments']['content'] ) ) {
            return;
        }
        $event['arguments']['content'] = $this->filter( $event['arguments']['content'] );
    }

    public function filterOutput( \sfEvent $event, $content ) {
        return $this->filter( $content );
    }
}
