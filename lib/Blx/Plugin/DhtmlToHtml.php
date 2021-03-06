<?php
namespace Blx\Plugin;

class DhtmlToHtml extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url'    => 'filter_url',
        'filter.args'   => 'filter_args',
    );
    protected $isDhtml = false;
    protected $ext = '.dhtml.html';

    public function filter_url( \sfEvent $event, $value ) {
        $value = $event->getSubject()->getUtil()->fixInnerUrl( $value );
        if ( substr( $value, -1 * strlen( $this->ext ) ) == $this->ext ) {
            $this->isDhtml = true;
            return substr( $value, 0, -1 * strlen( $this->ext ) ) . '.html';
        }
        return $value;
    }

    public function filter_args( \sfEvent $event, $args ) {
        if ( $this->isDhtml ) {
            $args['is_dynamic'] = true;
        } else {
            $args['is_dynamic'] = false;
        }
        
        $event->getSubject()->getDispatcher()->notify(
            new \sfEvent(
                $this,
                'plugin.yeld.dynamic_request'
            )
        );
        return $args;
    }
}
