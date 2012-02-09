<?php
namespace Blx;

abstract class Plugin {
    /**
     * Maps event to procedure to execute
     *
     * @var array
     */
    protected $mapping = array();
    protected $dispatcher;
    protected $util;
    protected $request;
    
    public function register( \sfEventDispatcher $dispatcher ) {
        foreach( $this->mapping as $event => $method ) {
            $dispatcher->connect( $event, array( $this, $method ) );
        }
    }

    public function setRequest( Request $request ) {
        $this->request = $request;
        $this->setUtil( $request->getUtil() );
        $this->setDispatcher( $request->getDispatcher() );
    }

    public function setDispatcher( \sfEventDispatcher $dispatcher ) {
        $this->dispatcher = $dispatcher;
    }

    public function setUtil( Util $util ) {
        $this->util = $util;
    }

    public function init() {
    }
}
