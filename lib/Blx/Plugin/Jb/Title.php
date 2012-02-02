<?php
namespace Blx\Plugin\Jb;

class Title extends \Blx\Plugin\Title {

    protected function getTitleSuffix() {
        return parent::gettitleSuffix() . ', ' . _( 'Behemoth`s Lair');
    }

}
