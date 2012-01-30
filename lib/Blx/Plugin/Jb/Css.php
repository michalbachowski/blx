<?php
namespace Blx\Plugin\Jb;

class Css extends \Blx\Plugin\Css {
    protected $realm;

    public function __construct( $url, $realm = JB_REALM, $media='screen, tv, projection' ) {
        parent::__construct( $url, $media );
        $this->realm = $realm;
    }

    protected function prepareTag() {
        return '<link rel="stylesheet" href="' . \JBUi::magazyn( $this->url, $this->realm ) . '" media="'. $this->media .'" />';
    }

}
