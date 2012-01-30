<?php
namespace Blx\Plugin;

class Xinha extends \Blx\Plugin {
    protected $prepend = '</body>';

    protected $mapping = array(
        'filter.output' => 'output',
    );

    protected $xinhaUrl;
    protected $xinhaConfUrl;

    public function __construct( $xinhaUrl, $xinhaConfUrl ) {
        $this->xinhaUrl = $xinhaUrl;
        $this->xinhaConfUrl = $xinhaConfUrl;
    }

    protected function prepareTag() {
        return '<script type="text/javascript" src="' . $this->xinhaConfUrl . '"></script>'
            . '<script type="text/javascript" src="'. $this->xinhaUrl . '"></script>';
    }

    public function output( \sfEvent $event, $content ) {
        return str_replace( $this->prepend, $this->prepareTag() . $this->prepend, $content );
    }
}
