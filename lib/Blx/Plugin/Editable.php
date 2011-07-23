<?php
namespace Blx\Plugin;

class Editable extends \Blx\Plugin {
    protected $mapping = array(
        'handle.get' => 'get',
        'filter.output' => 'filter',
    );
    protected $insideLoop = false;
    protected $content = '';
    protected $editor = '<textarea id="content">[raw_content]</textarea>';


    public function get( \sfEvent $event ) {
        if ( $this->insideLoop ) {
            return false;
        }
        if ( !isset( $event['arguments']['edit'] ) ) {
            return false;
        }
        $this->insideLoop = true;
        $event = $event->getSubject()->getDispatcher()->notifyUntil( $event );
        # no response - error
        if ( $event->isProcessed() ) {
            $this->content = $event->getReturnValue();
        }
        $event->setReturnValue( $this->editor );
        return true;
    }

    public function filter( \sfEvent $event, $out ) {
        return str_replace( '[raw_content]', $this->content, $out );
    }
}
