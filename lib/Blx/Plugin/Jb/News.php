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
    );

    protected $news = array();

    public function __construct() {
    }

    public function start( \sfEvent $event ) {
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
        $news = $this->fetch( $newsId );
        if ( date( 'Y-m', $news['news_date'] ) != $newsDate ) {
            $event->getSubject()->redirectToPage( $this->makeUrl( $news ) );
        }
    }

    public function display( \sfEvent $event ) {
        if ( isset( $event['arguments']['edit'] ) ) {
            return;
        }
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
        $event->setReturnValue( $out );
        return true;
    }

    protected function fetchList() {
        return 'lista nowin';
    }

    protected function fetchArchive( $date ) {
        return 'archiwum dla ' . $date;
    }

    protected function fetchNews( $id ) {
        $news = $this->fetch( $id );
        $time = sprintf( '<time datetime="%1$s">%1$s</time>', date( 'Y-m-d H:i:s', $news['news_date'] ) );
        $author = \JBUser::fromArray( $news, 'id', 'news_author_' );
        $title = \JBSanitize::html( $news['news_title'] );
        $meta = sprintf( _( 'author %s / %s, comments %u' ), (string) $author, $time, $news['news_comments_number'] );
        if ( $news['news_allow_comments'] ) {
            $comments =sprintf(  '[comments:%s:news:%u]', JB_REALM, $id );
        } else {
            $comments = '';
        }
        return sprintf(
            '<article class="news"><h2>%s</h2><p class="news-metadata">%s</p><div class="news-text">%s</div></article>%s',
            $title, $meta, \JBCore::getInstance()->Formatter->format( $news['news_text'] ), $comments
        );
    }

    protected function fetch( $id ) {
        if ( !isset( $this->news[$id] ) ) {
            $this->news[$id] = \JBNews::getById( $id );
        }
        return $this->news[$id];
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
                return _( 'News' );
            case self::TYPE_ARCHIVE:
                return sprintf( _( 'News archive %s' ), $newsDate );
            case self::TYPE_NEWS:
                $news = $this->fetch( $newsId );
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
