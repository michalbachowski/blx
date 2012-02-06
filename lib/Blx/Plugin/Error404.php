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
        $event->setReturnValue(
            sprintf(
                '<p class="error ui-state-error ui-corner-all">%s</p>',
                \Blx\Util::_( 'Page not found' )
            )
        );
        return true;
    }

}
