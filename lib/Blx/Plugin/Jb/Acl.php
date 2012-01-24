<?php
namespace Blx\Plugin\Jb;

class Acl extends \Blx\Plugin {
    protected $mapping = array(
        'filter.url' => 'filter',
    );
    const ALLOW = true;
    const DENY = false;
    protected $default;

    public function __construct( $default = self::DENY ) {
        $this->default = $default;
    }

    public function filter( \sfEvent $event, $url ) {
        $args = array( 'event' => $event, 'url' => $url );
        $allowGlobal = \JBPerm::verify( $this, 'perm.' . JB_REALM, $args );
        $allowPage = \JBPerm::verify( $this, 'perm.page.' . $url, $args );
        // an event was verified positively
        if ( $allowGlobal || $allowPage ) {
            return $url;
        }
        // no event was processed (no policies defined) - use default value
        if ( null === $allowGlobal && null === $allowPage && $this->default ) {
            return $url;
        }
        // access denied
        throw new \Blx\ForbiddenError(
            _( 'You are not allowed to acces this page.' )
        );
    }
}
