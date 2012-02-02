<?php
namespace Blx\Plugin\Jb;

class Js extends \Blx\Plugin\Js {
    protected $realm;

    public function __construct( $url, $realm=null ) {
        parent::__construct( $url );
        $this->realm = $realm;
    }

    protected function prepareTag( $url ) {

        return parent::prepareTag( \JBUi::magazyn( $url, $this->realm ) );
    }

}
