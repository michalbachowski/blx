<?php
namespace Blx\Plugin\Jb;

class Xinha extends \Blx\Plugin\Xinha {

    public function __construct() {
    }

    protected function prepareTag() {
        $this->xinhaUrl = \JBUi::magazyn( 'js/xinha/XinhaCore.js', 'blx' );
        $this->xinhaConfUrl = \JBUi::magazyn( 'js/xinha_conf.js', 'blx' );
        return parent::prepareTag();
    }

}
