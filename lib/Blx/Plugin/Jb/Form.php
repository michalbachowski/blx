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
        'filter.response.normal' => 'replace'
    );

    protected $forms = array();
    protected $view;
    protected $pattern = '!\[form\:(?P<name>[a-zA-Z0-9]+)\]!';

    public function __construct( $appDir, $name, \Jpl_Form_Factory $obj = null ) {
        \Jpl_Form_Factory::setConfigDirectory( 'forms/' );
        \Jpl_Form_Factory::setAppPath( $appDir );
        $this->setForm( $name, $obj );
    }

    public function setForm( $name, \Jpl_Form_Factory $obj = null ) { 
        if ( null === $obj || !$obj instanceof \Jpl_Form_Factory ) {
            $obj = new DummyForm( $name );
        }
        $this->forms[$name] = $obj;
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
        $name = $matches['name'];
        if ( !isset( $this->forms[$name] ) ) {
            return '';
        }
        return $this->displayForm( $name );
    }

    public function displayForm( $name ) {
        return $this->forms[$name]->getForm()->render( $this->view() );
    }

    protected function view() {
        if ( null === $this->view ) {
            $this->view = new \Zend_View();
        }
        return $this->view;
    }
}
