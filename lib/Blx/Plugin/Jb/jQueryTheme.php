<?php
namespace Blx\Plugin\Jb;

class jQueryTheme extends \Blx\Plugin\Jb\Css {
    public function __construct( $themeName, $realm=null, $themeDir='themes', $media='screen, tv, projection' ) {
        parent::__construct( $themeDir . '/' . $themeName . '/theme.css', $realm, $media );
    }
}
