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
        'filter.response.normal' => 'replace',
        'handle.get' => 'get',
        'handle.post' => 'post',
    );

    protected $forms = array();
    protected $callbacks = array();
    protected $view;
    protected $pattern = '!\[form\:(?P<url>[a-zA-Z0-9\\/.\-_]+)\]!';

    public function __construct( $appDir, $url, $callback, \Jpl_Form_Factory $obj = null ) {
        \Jpl_Form_Factory::setConfigDirectory( 'forms/' );
        \Jpl_Form_Factory::setAppPath( $appDir );
        $this->setForm( $url, $callback, $obj );
    }

    public function setForm( $url, $callback, \Jpl_Form_Factory $obj = null ) { 
        if ( null === $obj || !$obj instanceof \Jpl_Form_Factory ) {
            $obj = new DummyForm( $url );
        }
        $this->forms[$url] = $obj;
        $this->callbacks[$url] = $callback;
        return $this;
    }

    public function replace( \sfEvent $event, $content ) {
        return preg_replace_callback(
            $this->pattern,
            array( $this, 'replaceCallback' ),
            $content
        );
    }

    protected function replaceCallback( $matches ) {
        $url = $matches['url'];
        if ( !isset( $this->forms[$url] ) ) {
            return '';
        }
        return $this->displayForm( $url );
    }

    public function displayForm( $url ) {
        return $this->forms[$url]->getForm()->render( $this->view() );
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
