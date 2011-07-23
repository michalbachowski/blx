<?php
namespace Blx\Plugin;

class Title {
    protected $titlePattern = '[title]';
    protected $navPattern = '[navigation]';

    protected $breadcrumbs = array();

    protected $navTag = '<li><a href="%s">%s</a></li>';
    protected $navWrap = '<ul>%s</ul>';

    public function __construct( $tag = null, $wrap = null ) {
        if ( $tag ) {
            $this->navTag = $tag;
        }
        if ( $wrap ) {
            $this->navWrap = $wrap;
        }
    }

    public function filter( \sfEvent $event, $baseUrl ) {
        $parts = explode( '/', trim( $baseUrl, '/' ) );
        $url = '';
        $unknown = __( 'Unknown' );

        foreach( $parts as $part ) {
            $url .= '/' . $part;
            # fetch title
            $event = $this->getDispatcher()->notifyUntil(
                new \sfEvent(
                    $this,
                    'metadata.get',
                    array(
                        'key' => 'title',
                        'url' => $url
                    ),
                )
            );
            # no response - set title to unknown"
            if ( !$event->isProcessed() ) {
                $title = $unknown;
            } else {
                $title = $event->getReturnValue();
            }
            $this->breadcrumbs[] = array(
                'url' => $url,
                'title' => $title
            );
        }
        return $baseUrl;
    }
    public function filter( \sfEvent $event, $content ) {
        $title = '';
        $nav = '';
        foreach( $this->breadcrumbs as $crumb ) {
            $nav .= sprintf( $this->navTag, $crumb['url'], $crumb['title'] );
            $title .= $crumb['title'] . ', ';
        }
        $nav = sprintf( $this->navWrap, $nav );
        $content = str_replace( $this->titleTag, $title, $content );
        $content = str_replace( $this->navTag, $nav, $content );
        return $content;
    }
}
