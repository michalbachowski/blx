<?php
namespace Blx\Plugin;

class Copyright extends \Blx\Plugin {
    protected $mapping = array(
        'filter.output' => 'filter',
    );
    protected $pattern = '[copyright]';

    protected function copyright() {
        return sprintf( \Blx\Util::_( 'Copyright %u' ), date('Y') );
    }

    public function filter( \sfEvent $event, $output ) {
        return str_replace(
            $this->pattern,
            $this->copyright(),
            $output
        );
    }
}
