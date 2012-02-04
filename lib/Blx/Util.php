<?php
namespace Blx;


class Util {
    protected $urlPattern = '%s';

    public function __construct( $urlPattern=null ) {
        if ( $urlPattern ) {
           $this->setUrlPattern( $urlPattern );
        }
    }

    public function setUrlPattern( $urlPattern ) {
        if ( !$urlPattern ) {
            throw new RuntimeException( 'URL pattern could not be empty' );
        }
        $this->urlPattern = $urlPattern;
    }

    public function getUrlPattern() {
        return $this->urlPattern;
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
        $url = rtrim( trim( $url, '/' ), '.html' );
        if ( !$url ) {
            $url = 'index';
        }
        return $url . '.html';
    }

    public function getCompleteUrl( $url ) {
        return sprintf( $this->urlPattern, $url );
    }

    public static function displayArray( $items, $context, $method = 'displayOne' ) {
        return array_reduce( array_map( array( $context, $method ), $items ), array( '\Blx\Util', 'reducer' ), '' );
    }

    public static function reducer( $out, $current ) {
        return $out . $current;
    }

    public static function _( $string ) {
        return dgettext( 'blx', $string );
    }
}
