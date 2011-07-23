<?php
namespace Blx;


class Util {
    public static function prepareArguments() {
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
}
