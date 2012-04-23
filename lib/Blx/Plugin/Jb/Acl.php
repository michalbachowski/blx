<?php
namespace Blx\Plugin\Jb;

class Acl extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url' => 'filter',
        'acl.check.editable.form' => 'verify',
    );
    const ALLOW = true;
    const DENY = false;
    protected $defaultResponse;

    public function __construct( $default = self::DENY ) {
        $this->defaultResponse = $default;
        $this->mode = '.' . strtolower( $_SERVER['REQUEST_METHOD'] ?: 'get' );

    }

    public function filter( \sfEvent $event, $url ) {
        $response = $this->check( $event, $url );
        // all following check will be always GET
        $this->mode = '.get';
        if ( $response ) {
            return $url;
        }
        // access denied
        throw new \Blx\ForbiddenError(
            _( 'You are not allowed to acces this page.' )
        );
    }

    public function verify( \sfEvent $event ) {
        $action = substr( $event->getName(), 10 );
        $event->setReturnValue(
            \JBPerm::verify( $this, $action, array( 'event' => $event['event'] ) ) );
        return true;
    }

    protected function check( \sfEvent $event, $url ) {
        $args = array( 'event' => $event, 'url' => $url );
        $allowGlobal = \JBPerm::verify( $this, 'perm.' . JB_REALM . $this->mode, $args );
        $allowPage = \JBPerm::verify( $this, 'perm.page.' . $url . $this->mode, $args );
        // an event was verified positively
        if ( ( $allowGlobal && $allowPage !== false ) || $allowPage ) {
            return true;
        }
        // no event was processed (no policies defined) - use default value
        if ( null === $allowGlobal && null === $allowPage && null !== $this->defaultResponse ) {
            return true;
        }
    }
}
