<?php
namespace Blx\Plugin\Jb;

class Comments extends \Blx\Plugin {

    protected $mapping = array(
        'filter.output' => 'inject'
    );

    protected $pattern = '!\[comments\:(?P<item>[a-zA-Z0-9_]+)\:(?P<id>[0-9]+)(?:\:(?P<realm>[a-zA-Z0-9_]+))?\]!';
    
    public function inject( \sfEvent $event, $content ) {
        return preg_replace_callback(
            $this->pattern,
            array( $this, 'injectCallback' ),
            $content
        );
    }

    protected function injectCallback( $matches ) {
        return $this->displayList( $this->fetch( $matches['realm'] ?: JB_REALM, $matches['item'], (int) $matches['id'] ) );
    }

    protected function fetch( $realm, $item, $id ) {
        return \JBComments::fetchTree( $item, $id, 1, 150, 'asc', $realm );
    }

    protected function displayList( $comments ) {
        if ( !$comments ) {
            return;
        }
        return sprintf(
            '<section class="comments"><h3>%s</h3>%s</section>',
            _( 'Comments' ),
            \Blx\Util::displayArray( $comments, $this )
        );
    }

    public function displayOne( $comment ) {
        $menu = sprintf(
            '<div class="comment-menu">
                <a href="%4$s" title="%1$s">
                    <span class="ui-icon ui-icon-link"></span>
                    <span class="ui-widget-content ui-corner-all">%1$s</span>
                </a>
                <a href="%5$s" title="%2$s">
                     <span class="ui-icon ui-icon-pencil"></span>
                     <span class="ui-widget-content ui-corner-all">%2$s</span>
                </a>
                <a href="%6$s" title="%3$s">
                    <span class="ui-icon ui-icon-trash"></span>
                    <span class="ui-widget-content ui-corner-all">%3$s</span>
                </a>
            </div>',
            _( 'Permalink' ), _( 'Edit comment' ), _( 'Remove comment' ),
            'permalink', 'editlink', 'removelink'
        );
        return sprintf(
            '<article id="comment%1$u" class="ui-widget-content">
                <div class="comment-author">
                    <h4>%2$s <small>/ <time datetime="%3$s">%3$s</time></small></h4>
                    <div class="comment-author-data">%4$s</div>
                </div>
                <div class="comment-text">%5$s</div>
            </article>',
            $comment['id'],
            \JBUi::userLink( $comment['author'] ),
            date( 'Y-m-d H:i:s', $comment['date'] ),
            \JBUi::userAvatar( $comment['author'] ),
            \JBFormatter::format( $comment['text'] ) );
    }

    public function manage( \sfEvent $event ) {
        return;
    }
}
