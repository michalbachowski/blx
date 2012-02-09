<?php
namespace Blx\Plugin;

class Error403 extends \Blx\Plugin {
    protected $mapping = array(
        'handle.error' => 'handle403',
    );

    public function handle403( $event ) {
        if ( !$event['exception'] instanceof \Blx\Http403Error ) {
            return;
        }
        $event->setReturnValue(
            sprintf(
                '<p class="error ui-state-error ui-corner-all">%s</p>',
                \Blx\Util::_( 'You are not allowed to acces this page' )
            )
        );
        return true;
    }

}
