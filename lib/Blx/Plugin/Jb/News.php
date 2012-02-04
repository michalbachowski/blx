<?php
namespace Blx\Plugin\Jb;

class News extends \Blx\Plugin {
    const TYPE_LIST = 'list';
    const TYPE_ARCHIVE = 'archive';
    const TYPE_NEWS = 'news';

    protected $mapping = array(
        'handle.get' => 'display',
        'handle.post' => 'forbid',
        'metadata.get' => 'metadata',
        'dispatch.start' => 'start',
        'filter.response.normal' => 'inject'
    );

    protected $pattern = '!\[news\]!';
    protected $news = array();
    protected $months = array();
    protected $latestNewsNumber = 5;
    protected $util;

    public function __construct( $latestNewsNumber = 5 ) {
        $this->latestNewsNumber = (int) $latestNewsNumber;
        $this->months = array( \Blx\Util::_('january'), \Blx\Util::_('february'),
            \Blx\Util::_('march'), \Blx\Util::_('april'), \Blx\Util::_('may'),
            \Blx\Util::_('june'), \Blx\Util::_('july'), \Blx\Util::_('august'),
            \Blx\Util::_('september'), \Blx\Util::_('october'),
            \Blx\Util::_('november'), \Blx\Util::_('december') );
    }

    public function start( \sfEvent $event ) {
        // remember utility instance
        $this->util = $event->getSubject()->getUtil();
        // check whether we have enything to do
        $type = $this->checkUrl( $event['url'] );
        if ( !$type ) {
            return;
        }
        if ( self::TYPE_NEWS !== $type ) {
            return;
        }
        list( $newsDate, $newsId ) = $this->fetchItems( $event['url'] );
        if ( !$newsId || !$newsDate ) {
            return;
        }
        // check whether news exists
        $news = $this->fetch( $newsId );
        if ( !$news ) {
            return;
        }
        // validate date
        if ( date( 'Y-m', $news['news_date'] ) != $newsDate ) {
            $event->getSubject()->redirectToPage( $this->makeUrl( $news ) );
        }
    }

    protected function injectCallback( $matches ) {
        return $this->fetchList( $event );
    }

    public function inject( \sfEvent $event, $content ) {
        return preg_replace_callback(
            $this->pattern,
            array( $this, 'injectCallback' ),
            $content
        );
    }

    public function display( \sfEvent $event ) {
        list( $newsDate, $newsId ) = $this->fetchItems( $event['url'] );
        switch( $this->checkUrl( $event['url'] ) ) {
            case self::TYPE_LIST:
                $out = $this->fetchList();
                break;
            case self::TYPE_ARCHIVE:
                $out = $this->fetchArchive( $newsDate );
                break;
            case self::TYPE_NEWS:
                $out = $this->fetchNews( $newsId );
                break;
            default:
                return false;
        }
        if ( !$out ) {
            return;
        }
        if ( isset( $event['arguments']['edit'] ) ) {
            throw new \Blx\ForbiddenError( 'Editing news is forbidden' );
        }
        $event->setReturnValue( $out );
        return true;
    }

    protected function fetchList() {
        return $this->displayList( \JBNews::getLatest( $this->latestNewsNumber ) )
            . sprintf(
                '<p class="news-archive-button"><a href="%s" title="%s">%s</a></p>',
                $this->util->getCompleteUrl( $this->makeArchiveUrl( date( 'Y-m' ) ) ),
                \Blx\Util::_( 'View news archive' ),
                \Blx\Util::_( 'Archive' )
            );
    }

    protected function fetchArchive( $date ) {
        list( $year, $month ) = explode( '-', $date );
        return $this->displayList( \JBNews::getFromMonth( (int) $month, $year ), $date )
            . $this->displayArchive( $date );
    }

    protected function fetchNews( $id ) {
        $news = $this->fetch( $id );
        if ( !$news ) {
            return;
        }
        return $this->displayOne( $news, 'news_text', true, 2, false );
    }

    protected function displayList( $newsList, $currentMonth = null ) {
        if ( !$newsList ) {
            return;
        }
        return sprintf(
            '<h2>%s</h2>%s',
            \Blx\Util::_( 'News' ),
            \Blx\Util::displayArray( $newsList, $this )
        );
    }

    protected function displayArchive( $currentMonth = null ) {
        if ( !$currentMonth ) {
            $currentMonth = date( 'Y-m' );
        }
        $dates = \JBNews::fetchArchiveDates();
        $monthActivePattern = '<li class="ui-state-default"><a href="%s" title="%s">%s</a></li>';
        $monthInactivePattern = '<li class="ui-state-disabled"><span>%3$s</span></li>';
        $monthCurrentPattern = '<li class="ui-state-highlight"><strong>%3$s</strong></li>';
        $title = \Blx\Util::_( 'News archive %s' );
        $tmp = array();
        // prepare months
        foreach( $dates as $date => $newsNumber ) {
            list( $year, $month ) = explode( '-', $date );
            if ( !isset( $tmp[$year] ) ) {
                $tmp[$year] = '';
            }
            $tmp[$year] .= sprintf( $currentMonth == $date ? $monthCurrentPattern : ( $newsNumber ? $monthActivePattern : $monthInactivePattern ),
                $this->util->getCompleteUrl( $this->makeArchiveUrl( $date ) ),
                sprintf( $title, $date ), $this->months[(int)$month - 1]
            );
        }
        krsort($tmp);
        // prepare years
        $yearPattern = '<li><strong>%u</strong><ul>%s</ul></li>';
        foreach( $tmp as $year => $archive ) {
            $out .= sprintf( $yearPattern, $year, $archive );
        }
        // generate output
        return sprintf( '<aside><strong>%s</strong><ul>%s</ul></aside>', \Blx\Util::_( 'News archive' ), $out );
    }
    public function displayOne( $news, $textKey = 'news_short', $allowComments = false, $header = 3, $linkHeader = true ) {
        $time = sprintf( '<time datetime="%1$s">%1$s</time>', date( 'Y-m-d H:i:s', $news['news_date'] ) );
        $author = \JBUser::fromArray( $news, 'id', 'news_author_' );
        $title = \JBSanitize::html( $news['news_title'] );
        if ( $linkHeader ) {
            $title = sprintf( '<a href="%s" title="%s">%s</a>',
                $this->util->getCompleteUrl( $this->makeUrl( $news ) ),
                sprintf( \Blx\Util::_( 'Read complete news &quot;%s&quot;' ), $title ),
                $title
            );
        }
        $meta = sprintf( \Blx\Util::_( 'author %s / %s, comments %u' ), (string) $author, $time, $news['news_comments'] );
        if ( $allowComments && $news['news_allow_comments'] ) {
            $comments =sprintf(  '[comments:news:%u:%s]', $news['news_id'], $news['news_realm'] );
        } else {
            $comments = '';
        }
        $content = \JBFormatter::format( $news[$textKey] );
        return sprintf(
            '<article class="news"><h%5$u>%s</h%5$u><p class="news-metadata">%s</p><div class="news-text">%s</div></article>%s',
            $title, $meta, $content, $comments, $header );
    }

    protected function fetch( $id ) {
        if ( !isset( $this->news[$id] ) ) {
            try {
                $this->news[$id] = \JBNews::getById( $id );
                // remove news from other realm
                if ( $this->news[$id]['news_realm'] != JB_REALM ) {
                    return;
                }
            } catch( \JBNewsNotFoundException $e ) {
                $this->news[$id] = null;
            }
        }
        if ( $this->news[$id]['news_realm'] != JB_REALM ) {
            return;
        }
        return $this->news[$id];
    }

    protected function makeArchiveUrl( $date ) {
        return sprintf( 'nowiny/%s.html',$date );
    }

    protected function makeUrl( $news ) {
        return sprintf( 'nowiny/%s/%u.html', date( 'Y-m', $news['news_date'] ), $news['news_id'] );
    }

    public function metadata( \sfEvent $event ) {
        $value = $this->fetchMetadataValue(
            $event['url'],
            $event['key']
        );
        if ( !$value ) {
            return false;
        }
        $event->setReturnValue( $value );
        return true;
    }

    protected function fetchMetadataValue( $url, $key ) {
        list( $newsDate, $newsId ) = $this->fetchItems( $url );
        switch( $this->checkUrl( $url ) ) {
            case self::TYPE_LIST:
                return \Blx\Util::_( 'News' );
            case self::TYPE_ARCHIVE:
                return sprintf( \Blx\Util::_( 'News archive %s' ), $newsDate );
            case self::TYPE_NEWS:
                $news = $this->fetch( $newsId );
                if ( !$news ) {
                    return;
                }
                return $news['news_title'];
        }
        return;
    }

    protected function checkUrl( $url ) {
        if ( $url == 'nowiny.html' ) {
            return self::TYPE_LIST;
        }
        if ( 0 !== strpos( $url, 'nowiny/' ) ) {
            return;
        }
        list( $newsDate, $newsId ) = $this->fetchItems( $url );
        if ( $newsId ) {
            return self::TYPE_NEWS;
        }
        if ( $newsDate ) {
            return self::TYPE_ARCHIVE;
        }
    }

    protected function fetchItems( $url ) {
        $tmp = preg_replace( '#^nowiny/([0-9]{4}-[0-9]{2})(?:/([0-9]+))?\.html$#', '$1 $2', $url );
        if ( $tmp == $url ) {
            return array('', 0);
        }
        return explode( ' ', $tmp );
    }

    public function forbid( \sfEvent $event ) {
        return;
    }
}
