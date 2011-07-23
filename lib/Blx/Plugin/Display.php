<?php
namespace Blx\Plugin;

class Display extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.stop' => 'display',
    );
    
    public function display( \sfEvent $event ) {
        echo $event['output'];
    }
}
