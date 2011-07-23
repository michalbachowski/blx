<?php
namespace Blx\Plugin\Jb;

class Load {
    protected $realm;

    public function __construct( $realm ) {
        $this->realm = $realm;
    }

    public function update( \sfEvent $event ) {
        define( 'JB_REALM', $this->realm );
        define( 'JB_DATABASE', true );
        define( 'JB_DEBUG', 1 );
        require 'jbcore/jbcore.php';
    }
}
