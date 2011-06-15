<?php
namespace Blx\Plugin\Jb;

class User {
    public function update( \sfEvent $event ) {
        $event->getSubject()->setArg( '_user', \JBCore::user() );
    }
}
