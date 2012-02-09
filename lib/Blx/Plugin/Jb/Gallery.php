<?php
namespace Blx\Plugin\Jb;

class Gallery extends \Blx\Plugin {

    protected $mapping = array(
        'dispatch.start' => 'start',
        'filter.response.normal' => 'inject'
    );

    protected $pattern = '!\[gallery\](.+?)\[\/gallery\]!s';
    protected $mine;

    public function start() {
        require_once \Realms::data( 'kopalnia', 'realm_path' ) . 'app/class.mine.php';
        $this->mine = new \Mine();
    }

    public function inject( \sfEvent $event, $content ) {
        return preg_replace_callback(
            $this->pattern,
            array( $this, 'injectCallback' ),
            $content
        );
    }

    protected function injectCallback( $matches ) {
        return sprintf(
            '</p><aside class="gallery">%s</aside><p>',
            \Blx\Util::displayArray( explode( "\n", $matches[1] ), $this )
        );
    }

    public function displayOne( $row ) {
        $row = trim( $row );
        if ( !$row ) {
            return;
        }
        list( $url, $desc ) = explode( ' ', strip_tags( $row ), 2 );
        $thumbUrl = $this->mine->file_resized_uri( $url, 'thumb' );
        if ( !$thumbUrl ) {
            return;
        }
        if ( !$desc ) {
            $title = sprintf( \Blx\Util::_( 'Zoom image %s' ), $url );
            $caption = '';
            $desc = '';
        } else {
            $title = $desc = \JBSanitize::html( $desc );
            $caption = sprintf( '<figcaption>%s</figcaption>', $title );
        }
        $params = $this->mine->getResizeParams( 'thumb' );
        return sprintf(
            '<figure><a href="%s" title="%s"><img src="%s" alt="%s" '
            . 'width="%u" height="%u" /></a>%s</figure>',
            $url, $title, $thumbUrl, $desc, $params['width'], $params['height'], $caption
        );
    }

}
