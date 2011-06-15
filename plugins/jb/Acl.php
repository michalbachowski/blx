<?php
namespace Blx\Plugin\Jb;

class Acl {
    const ALLOW = true;
    const DENY = false;
    protected $default;
    protected $groups;
    protected $users;

    public function __construct( $default = self::DENY, array $groups = array(), array $users = array() ) {
        $this->default = $default;
        $this->groups = $groups;
        $this->users = $users;
    }
    public function filter( \sfEvent $event, $url ) {
        $allowGroup = null;
        $allowUser = null;
        $args = $event->getSubject()->getArgs();
        $user = $args['_user'];
        if ( isset( $this->groups[$url] ) ) {
            $allowGroup = \JBGroups::isIn( $this->groups[$url], $user );
        }
        if ( isset( $this->users[$url] ) ) {
            $allowUser = in_array( $user, $this->users[$url] );
        }
        if ( null === $allowGroup && null === $allowUser ) {
            $allowGroup = $this->default;
            $allowUser = $this->default;
        }
        if ( self::ALLOW === $allowGroup || self::ALLOW === $allowUser ) {
            return $url;
        }
        throw new \Blx\ForbiddenError( _(
            'You are not allowed to acces this page.'
        ) );
    }
}
