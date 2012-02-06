<?php
namespace Blx\Plugin\Jb;

class Copyright extends \Blx\Plugin\Copyright {
    protected function copyright() {
        return sprintf(
            \_c( 'This page is part of %s. Copyright %u' ),
            '<a href="[jb_url]" title="[jb_title]">[jb_name]</a>',
            date('Y')
        );
    }
}
