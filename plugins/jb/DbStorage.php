<?php
namespace Blx\Plugin\Jb;

class DbStorage {

    public function get( \sfEvent $event ) {
        $query = 'select get_page(:url);'
        $params = array( ':url' => $event['url'] );
        JBDB::instance()->queryParams( $query, $params );
        $row = JBDB::instance()->getNextRow();
        $row['metadata'] = $row['metadata'] ? json_decode( $row['metadata'] ) : '';
        return $row;
    }

    public function post( \sfEvent $event ) {
        $query = 'select set_page(:url, :title, :content, :metadata);';
        $metadata = $this->prepareMetadata( $event['arguments'] );
        $params = array(
            ':url'      => $event['url'],
            ':title'    => $event['arguments']['title'],
            ':content'  => $event['arguments']['content'],
            ':metadata' => json_encode( $metadata )
        );
        JBDB::instance()->queryParams( $query, $params );
    }

    protected function prepareMetadata( $metadata ) {
        foreach( $metadata as $key => $value ) {
            if ( 'title' === $key || 'content' === $key ) {
                unset( $metadata );
                continue;
            }
            if ( '_' === $key{0} ) {
                unset( $key );
            }
        }
        return $metadata;
    }
}
