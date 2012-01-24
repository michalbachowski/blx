<?php
namespace Blx\Plugin\Jb;

class Load extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'update'
    );
    protected $realm;

    public function __construct( $realm ) {
        $this->realm = $realm;
    }

    public function update( \sfEvent $event ) {
        define( 'JB_REALM', $this->realm );
        define( 'JB_DEBUG', 1 );
        require 'jbcore/jbcore.php';
    }
}
