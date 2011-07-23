<?php
namespace Blx\Plugin;

class Echo extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.stop' => 'display',
    );
    
    public function display( \sfEvent $event ) {
        echo $event['output'];
    }
}
