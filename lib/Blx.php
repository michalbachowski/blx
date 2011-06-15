<?php
namespace Blx;

require dirname( __FILE__ ) . '/sfEventDispatcher.php';

class Error extends \Exception {}

class UnsupportedMethodError extends \RuntimeException {
    public function __construct( $method ) {
        parent::__construct( sprintf(
            _( 'Method %s is unsupported' ),
            $method
        ) );
    }
}

class HttpError extends Error {}

class Http404Error extends HttpError {}

class PageNotFoundError extends Http404Error {}

class Http403Error extends HttpError {}

class ForbiddenError extends Http403Error {}


function prepareArguments() {
    if ( isset( $_SERVER['argc'] ) && $_SERVER['argc'] > 0 ) {
        $args = $_SERVER['argv'];
        $url = isset( $args['url'] ) ? $args['url'] : '';
        $method = Request::CLI;
    } else {
        $args = array_merge( $_GET, (array) $_POST );
        $url = isset( $_GET['url'] ) ? $_GET['url'] : '';
        $method = $_POST ? Request::POST : Request::GET;
    }
    return array( $url, $args, $method );
}

class Request {
    const CLI = 'cli';
    const POST = 'post';
    const GET = 'get';

    protected $url;
    protected $args;
    protected $method;
    protected $dispatcher;

    public function __construct( $url, $args, $method = self::GET ) {
        if ( self::CLI != $method && self::GET != $method && self::POST != $method ) {
            throw new UnsupportedMethodError( $method );
        }
        $this->url = $url;
        $this->args = $args;
        $this->method = $method;
    }

    public function getDispatcher() {
        if ( null === $this->dispatcher ) {
            $this->dispatcher = new \sfEventDispatcher();
        }
        return $this->dispatcher;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getArgs() {
        return $this->args;
    }

    public function setArg( $key, $value ) {
        $this->args[$key] = $value;
        return $this;
    }

    public function dispatch() {
        # start dispatch
        $this->getDispatcher()->notify( new \sfEvent( $this, 'dispatch.start' ) );
        try {
            # prepare url and arguments
            $url = $this->getDispatcher()->filter(
                new \sfEvent( $this, 'filter.url' ),
                $this->getUrl()
            )->getReturnValue();
            $args = $this->getDispatcher()->filter(
                new \sfEvent( $this, 'filter.args' ),
                $this->getArgs()
            )->getReturnValue();

            # handle request
            $event = $this->getDispatcher()->notifyUntil(
                new \sfEvent(
                    $this,
                    'handle.' . $this->getMethod(),
                    array( 'url' => $url, 'arguments' => $args )
                )
            );
            # no response - error
            if ( !$event->isProcessed() ) {
                throw new Http404Error();
            }
            # filter response
            $out = $this->getDispatcher()->filter(
                new \sfEvent( $this, 'filter.response.normal'),
                $event->getReturnValue()
            )->getReturnValue();
        } catch( Error $e ) {
            # handle error
            $event = $this->getDispatcher()->notifyUntil(
                new \sfEvent( $this, 'handle.error', array( 'exception' => $e ) )
            );
            # no response - propagate error
            if ( !$event->isProcessed() ) {
                throw $e;
            }
            # filter error response
            $out = $this->getDispatcher()->filter(
                new \sfEvent( $this, 'filter.response.error'),
                $event->getReturnValue()
            )->getReturnValue();
        }
        # display
        $this->getDispatcher()->notify(
            new \sfEvent( $this, 'dispatch.stop', array( 'output' => $out ) ) );
    }

    public function display( $event ) {
        echo $event['output'];
    }

    public function handle404( $event ) {
        $event->setReturnValue( var_export(
            $event['exception']
        ) );
        return true;
    }
}
