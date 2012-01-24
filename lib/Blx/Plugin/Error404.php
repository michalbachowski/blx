<?php
namespace Blx\Plugin;

class Error404 extends \Blx\Plugin {
    protected $mapping = array(
        'handle.error' => 'handle404',
    );

    public function handle404( $event ) {
        if ( !$event['exception'] instanceof \Blx\Http404Error ) {
            return;
        }
        $event->setReturnValue( var_export(
            $event['exception'],
            true
        ) );
        return true;
    }

}
