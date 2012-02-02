<?php
namespace Blx\Plugin\Jb;

class DbStorage extends \Blx\Plugin {
    protected $mapping = array(
        'handle.get' => 'get',
        'handle.post' => 'post',
        'metadata.get' => 'metadata',
    );
    protected $cache = array();

    public function get( \sfEvent $event ) {
        if ( !isset( $this->cache[$event['url']] ) ) {
            $this->cache[$event['url']] = $this->fetchFromDb( $event['url'] );
        }
        $data =  $this->fetch( $event['url'] );
        if ( !$data ) {
            return false;
        }
        $event->setReturnValue( $data['content'] );
        return true;
    }
    protected function fetch( $url ) {
        if ( !isset( $this->cache[$url] ) ) {
            $this->cache[$url] = $this->fetchFromDb( $url );
        }
        return $this->cache[$url];
    }

    protected function fetchFromDb( $url ) {
        $query = 'select * from blx.get_page(:url, :realm);';
        $params = array(
            ':url' => $url,
            ':realm' => JB_REALM
        );
        \JBDB::getInstance()->queryParams( $query, $params );
        $row = \JBDB::getInstance()->getNextRow();
        if ( !$row ) {
            return;
        }
        $row['metadata'] = $row['metadata'] ? json_decode( $row['metadata'] ) : '';
        return $row;
    }

    public function post( \sfEvent $event ) {
        $this->storeIntoDb( $event );
        $event->getSubject()->redirectToPage( $event['url'] );
    }
    protected function storeIntoDb( \sfEvent $event ) {
        $query = 'select blx.set_page(:url, :realm, :title, :content, :metadata);';
        $metadata = $this->prepareMetadata( $event['arguments'] );
        $params = array(
            ':url'      => $event['url'],
            ':realm'    => JB_REALM,
            ':title'    => $event['arguments']['title'],
            ':content'  => $event['arguments']['content'],
            ':metadata' => json_encode( $metadata )
        );
        \JBDB::getInstance()->queryParams( $query, $params );
    }

    protected function prepareMetadata( $metadata ) {
        foreach( $metadata as $key => $value ) {
            if ( 'title' === $key || 'content' === $key || 'realm' === $key) {
                unset( $metadata[$key] );
                continue;
            }
            if ( '_' === $key{0} ) {
                unset( $metadata[$key] );
            }
        }
        return $metadata;
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
        $data = $this->fetch( $url );
        if ( !$data ) {
            return;
        }
        if ( isset( $data[$key] ) ) {
            return $data[$key];
        }

        if ( !is_array( $data['metadata'] ) ) {
            return;
        }
        if ( isset( $data['metadata'][$key] ) ) {
            return $data['metadata'][$key];
        }
    }
}
