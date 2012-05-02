<?php
namespace Blx\Plugin\Jb;

class Permission extends \Blx\Plugin {

    protected $mapping = array(
        'filter.response.normal' => 'inject'
    );

    protected $pattern = '!\[permission\:(?P<type>[a-zA-Z]+?)(\:(?P<params>[a-zA-Z0-9_\-,]+?))?\](?P<content>.+?)(?:\[\/permission\]|$)!mis';
    
    public function inject( \sfEvent $event, $content ) {
        return preg_replace_callback(
            $this->pattern,
            array( $this, 'injectCallback' ),
            $content
        );
    }

    protected function injectCallback( $matches ) {
        $status = false;
        switch( strtolower( $matches['type'] ) ) {
            case 'set':
                $status = \JBPerm::verify($this, 'permission.set.' . $matches['params'], array());
                break;
            default:
                $cls = '\JBPolicy' . $matches['type'];
                if ( !class_exists( $cls ) ) {
                    break;
                }
                $policy = new $cls( explode(',', $matches['params'] ) );
                $status = $policy->check(array());
                break;
        }
        if ( $status ) {
            return $matches['content'];
        }
        return '';
    }
}
