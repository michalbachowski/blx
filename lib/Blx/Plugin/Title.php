<?php
namespace Blx\Plugin;

class Title extends \Blx\Plugin {
    protected $mapping = array(
        'dispatch.start' => 'filter_url',
        'filter.output' => 'filter_output',
    );

    protected $titlePattern = '[title]';
    protected $navPattern = '[navigation]';

    protected $titleSuffix;
    protected $breadcrumbs = array();

    protected $navTag = '<li><a href="%s">%s</a></li>';
    protected $navWrap = '<ul>%s</ul>';

    public function __construct( $titleSuffix, $tag = null, $wrap = null ) {
        $this->titleSuffix = $titleSuffix;
        if ( $tag ) {
            $this->navTag = $tag;
        }
        if ( $wrap ) {
            $this->navWrap = $wrap;
        }
    }

    protected function getTitleSuffix() {
        return _( $this->titleSuffix );
    }

    public function filter_url( \sfEvent $event ) {
        // prepare bread crumbs and complete title for given url
        $parts = explode( '/', trim( $event['url'], '/' ) );
        $url = '';
        // loop throught all URL parts and fetch it`s title
        foreach( $parts as $part ) {
            $url .= '/' . $part;
            $tmpUrl = $event->getSubject()->getUtil()->fixInnerUrl( $url );
            # fetch title
            $this->breadcrumbs[] = array(
                'url' => $tmpUrl,
                'title' => $this->fetchTitle( $tmpUrl, $event )
            );
        }
    }

    protected function fetchTitle( $url, \sfEvent $event ) {
        $titleEvent = $event->getSubject()->getDispatcher()->notifyUntil(
            new \sfEvent(
                $this,
                'metadata.get',
                array(
                    'key' => 'title',
                    'url' => $url
                )
            )
        );
        # no response - set title to unknown"
        if ( !$titleEvent->isProcessed() ) {
            return _( 'Unknown' );
        }
        return $titleEvent->getReturnValue();
    }

    public function filter_output( \sfEvent $event, $content ) {
        $title = '';
        $nav = '';
        foreach( $this->breadcrumbs as $crumb ) {
            $nav .= sprintf(
                $this->navTag,
                $event->getSubject()->getUtil()->getCompleteUrl( $crumb['url'] ),
                $crumb['title']
            );
            $title = $crumb['title'] . ', ' . $title;
        }
        $title .= $this->getTitleSuffix();
        $nav = sprintf( $this->navWrap, $nav );
        $content = str_replace( $this->titlePattern, $title, $content );
        $content = str_replace( $this->navPattern, $nav, $content );
        return $content;
    }
}
