<?php
namespace Blx\Plugin\Jb;

class Board extends \Blx\Plugin {

    protected $mapping = array(
        'filter.output' => 'inject'
    );

    protected $pattern = '!\[board\:(?P<board>[0-9]+)(?:\:(?P<entries>[0-9]+))?\]!';
    
    public function inject( \sfEvent $event, $content ) {
        return preg_replace_callback(
            $this->pattern,
            array( $this, 'injectCallback' ),
            $content
        );
    }

    protected function injectCallback( $matches ) {
        return $this->displayList( $this->fetch( $matches['board'], (int) $matches['entries'] ), $matches['board'] );
    }

    protected function fetch( $board, $entries = null ) {
        if ( !$board ) {
            return array();
        }
        $entries = $entries ?: $this->entries;

        if( !file_exists( \JBCache::path( 'topics_forum_' . $board, 'imperium' ) ) ) {
            return array();
        }
        return array_slice( require_once \JBCache::path( 'topics_forum_' . $board,'imperium' ), 0, $entries );
    }

    protected function displayList( $topics, $board ) {
        return sprintf(
            '<aside class="board-topics"><h4><a href="%s" title="%s">%s</a></h4><dl>%s</dl></aside>',
            \Url::make( 'forum/' . $board, null, 'imperium' ),
            \Blx\Util::_( 'View all discussions from board' ),
            \Blx\Util::_( 'Latest discussions' ),
            \Blx\Util::displayArray( $topics, $this )
        );
    }

    public function displayOne( $topic ) {
        return sprintf(
            '<dt><a href="%s" title="%s">%s</a></dt><dd>%s / <time datetime="%5$s">%5$s</time></dd>',
            $topic['url'],
            sprintf( _( 'View topic &quot;%s&quot;' ), \JBSanitize::html( $topic['label']  ) ),
            \JBSanitize::html( $topic['label']  ),
            (string) \JBUser::fromArray( $topic, 'id', 'author_' ),
            date( 'Y-m-d H:i:s', $topic['date'] )
        );
    }

}
