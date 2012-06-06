<?php
namespace Blx\Plugin\Jb;

class DummyForm extends \Jpl_Form_Factory {
    protected $_module = '';
    protected $_configFile = 'forms.ini';
    protected $_configNamespace;

    public function __construct( $namespace ) {
        $this->_configNamespace = $namespace;
    }

    protected function init() {
        /**
        $translate = new ZendTranslate();
        $this->_form->setTranslator( $translate );
        //*/
    }
}

class Form extends \Blx\Plugin {

    protected $mapping = array(
        'handle.get' => 'get',
        'handle.post' => 'post',
        'metadata.get' => 'metadata',
    );

    protected $forms = array();
    protected $callbacks = array();
    protected $metadata = array();
    protected $view;

    public function __construct( $appDir, $url, $title, $callback, \Jpl_Form_Factory $obj = null ) {
        \Jpl_Form_Factory::setConfigDirectory( 'forms/' );
        \Jpl_Form_Factory::setAppPath( $appDir );
        $this->setForm( $url, $title, $callback, $obj );
    }

    public function getForm( $url ) {
        return $this->forms[$url];
    }

    public function setForm( $url, $title, $callback, \Jpl_Form_Factory $obj = null ) { 
        if ( null === $obj || !$obj instanceof \Jpl_Form_Factory ) {
            $obj = new DummyForm( $url );
        }
        $this->forms[$url] = $obj;
        $this->callbacks[$url] = $callback;
        $this->metadata[$url] = $title;
        return $this;
    }

    public function displayForm( $url ) {
        return $this->forms[$url]->getForm()->render( $this->view() );
    }
    
    public function metadata( \sfEvent $event ) {
        if ( !isset( $this->metadata[$event['url']] ) ) {
            return false;
        }
        $event->setReturnValue( $this->metadata[$event['url']] );
        return true;
    }
    public function get( \sfEvent $event ) {
        if ( !isset( $this->forms[$event['url']] ) ) {
            return false;
        }
        $event->setReturnValue( $this->displayForm( $event['url'] ) );
        return true;
    }

    public function post( \sfEvent $event ) {
        if ( !isset( $this->forms[$event['url']] ) ) {
            return false;
        }
        $form = $this->forms[$event['url']]->getForm();

        if ( $form->isValid( $event['arguments'] ) ) {
            $event->setReturnValue( $this->executeCallback( $event ) );
        } else {
            $event->setReturnValue( $this->displayForm( $event['url'] ) );
        }
        return true;
    }

    public function executeCallback( \sfEvent $event ) {
        $func = $this->callbacks[$event['url']];
        return $func( $event['arguments'], $event );
    }

    protected function view() {
        if ( null === $this->view ) {
            $this->view = new \Zend_View();
        }
        return $this->view;
    }
}
