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
        require 'jbcore/jbcore.php';
    }
}
