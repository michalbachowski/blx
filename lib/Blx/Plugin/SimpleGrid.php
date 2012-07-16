<?php
namespace Blx\Plugin;

class SimpleGrid extends \Blx\Plugin {
    
    protected $mapping = array(
        'handle.get' => 'get',
        'metadata.get' => 'metadata',
    );

    protected $url = '';
    protected $data;
    protected $metadata = array();

    public function __construct( $url, $title, $htmlClass, $data=null ) {
        $this->htmlClass = $htmlClass;
        $this->url = $url;
        $this->setData($data);
        $this->setMetadata('title', $title);
    }

    public function getUrl() {
        return $this->url;
    }

    public function getMetadata($key) {
        if ( !isset( $this->metadata[$key] ) ) {
            return;
        }
        return $this->metadata[$key];
    }
    public function setMetadata( $key, $data ) {
        $this->metadata[$key] = $data;
        return $this;
    }


    public function getData() {
        return $this->data;
    }
    public function setData($data) {
        $this->data = $data;
        return $data;
    }


    public function metadata( \sfEvent $event ) {
        if ( $this->getUrl() !== $event['url'] ) {
            return false;
        }
        $meta = $this->getMetadata($event['key']);
        if ( !$meta ) {
            return false;
        }
        $event->setReturnValue( $meta );
        return true;
    }
    public function get( \sfEvent $event ) {
        if ( $this->getUrl() !== $event['url'] ) {
            return false;
        }
        $event->setReturnValue( $this->generateGrid() );
        return true;
    }

    public function generateGrid() {
        return sprintf(
            '<table class="%s"><caption>%s</caption><thead>%s</thead><tfoot>%s</tfoot><tbody>%s</tbody></table>',
            $this->htmlClass,
            $this->getMetadata('title'),
            $this->generateHeader(),
            $this->generateFooter(),
            $this->generateBody()
        );
    }

    protected function generateHeader() {
        $columns = $this->getColumnHeaders();
        if ( !$columns ) {
            return '';
        }
        return $this->generateRows( '', '<th>' . implode( '</th><th>', $columns ) . '</th>' );
    }

    protected function getColumnHeaders() {
        $tmp = $this->getData();
        return array_keys( $tmp[0] );
    }

    protected function generateFooter() {
        return '';
    }

    protected function generateBody() {
        $data = $this->getData();
        if ( !$data ) {
            $colspan = count( $this->getColumnHeaders() ) ?: 1;
            return sprintf( '<td colspan="%u" class="info">%s</td>', $colspan, \Blx\Util::_('No data found') );
        }
        return array_reduce(
            array_map(
                array( $this, 'generateRow'),
                $data
            ),
            array( $this, 'generateRows'),
            ''
        );
    }
    protected function prepareRowData(array $row) {
        return array_map(
            array( '\JBSanitize', 'html' ),
            $row
        );
    }
    protected function generateRow($data) {
        return sprintf('<td>%s</td>', implode( '</td><td>', $this->prepareRowData($data) ) );
    }
    protected function generateRows($out, $row) {
        return sprintf('%s<tr>%s</tr>', $out, $row);
    }
}
