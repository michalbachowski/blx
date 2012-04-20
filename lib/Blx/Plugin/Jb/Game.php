<?php
namespace Blx\Plugin\Jb;

class Game extends \Blx\Plugin {

    protected $mapping = array(
        'filter.output' => 'inject'
    );

    protected $groups = array(
        'warrior' => 5148,
        'cleric' => 5147,
        'mage' => 5146,
        'bard' => 5145,
        'archer' => 5144,
        'guest' => -1,
    );

    protected $pattern = '!\[game\:(?P<player>[a-zA-Z0-9_]+)\](?P<content>.+?)\[\/game\]!mis';
    
    public function inject( \sfEvent $event, $content ) {
        return preg_replace_callback(
            $this->pattern,
            array( $this, 'injectCallback' ),
            $content
        );
    }

    protected function injectCallback( $matches ) {
        if ( \JBCore::user() < 0 ) {
            if ( $matches['player'] == 'guest' ) {
                return $matches['content'];
            }
            return '';
        }
        if ( !isset( $this->groups[$matches['player']] ) ) {
            return '';
        }
        if ( \JBGroups::isIn( $this->groups[$matches['player']] ) ) {
            return $matches['content'];
        }
        return '';
    }

}
