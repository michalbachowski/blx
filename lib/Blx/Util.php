<?php
namespace Blx;


class Util {
    protected $urlPattern;

    public function __construct( $urlPattern ) {
        $this->urlPattern = $urlPattern;
    }

    public function prepareArguments() {
        if ( isset( $_SERVER['argc'] ) && $_SERVER['argc'] > 0 ) {
            $args = $_SERVER['argv'];
            $url = isset( $args['url'] ) ? $args['url'] : '';
            $method = Request::CLI;
        } else {
            $args = array_merge( $_GET, (array) $_POST );
            $url = isset( $_GET['url'] ) ? $_GET['url'] : '';
            $method = $_POST ? Request::POST : Request::GET;
        }
        return array( $url, $args, $method );
    }

    public function getCompleteUrl( $url ) {
        return sprintf( $this->urlPattern, $url );
    }
}
