<?php
namespace Blx\Plugin\Jb;

class Css extends \Blx\Plugin\Css {
    protected $realm;

    public function __construct( $url, $realm=null, $media='screen, tv, projection' ) {
        parent::__construct( $url, $media );
        $this->realm = $realm;
    }

    protected function prepareTag( $url, $media ) {
        return parent::prepareTag( \JBUi::magazyn( $url, $this->realm ), $media );
    }

}
