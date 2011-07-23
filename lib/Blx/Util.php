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
            $method = Request::CLI;
        } else {
            $args = array_merge( $_GET, (array) $_POST );
            $method = $_POST ? Request::POST : Request::GET;
        }

        if (  isset( $args['url'] ) ) {
            $url = $this->fixInnerUrl( $args['url'] );
            unset( $args['url'] );
        } else {
            $url = '';
        }
        return array( $url, $args, $method );
    }

    public function fixInnerUrl( $url ) {
        return rtrim( trim( $url, '/' ), '.html' ) . '.html';
    }

    public function getCompleteUrl( $url ) {
        return sprintf( $this->urlPattern, $url );
    }
}
