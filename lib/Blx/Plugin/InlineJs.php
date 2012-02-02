<?php
namespace Blx\Plugin;

class InlineJs extends \Blx\Plugin {
    protected $prepend = '</body>';

    protected $mapping = array(
        'filter.output' => 'output',
    );

    protected $code;

    public function __construct( $code ) {
        $this->code = $code;
    }

    protected function prepareTag( $code ) {
        return '<script type="text/javascript">' . $code . '</script>';
    }

    public function output( \sfEvent $event, $content ) {
        return str_replace( $this->prepend, $this->prepareTag( $this->code ) . $this->prepend, $content );
    }
}
