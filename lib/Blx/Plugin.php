<?php
namespace Blx;

abstract class Plugin {
    /**
     * Maps event to procedure to execute
     *
     * @var array
     */
    protected $mapping = array();

    protected function register( \sfEventDispatcher $dispatcher ) {
        foreach( $this->mapping as $event => $method ) {
            $dispatcher->connect( $event, array( $this, $method ) );
        }
    }
}
