<?php
namespace Blx;

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

    public function addPlugin( Plugin $plugin ) {
        $plugin->register( $this->getDispatcher() );
    }

    public function dispatch() {
        # start dispatch
        $this->getDispatcher()->notify(
            new \sfEvent(
                $this,
                'dispatch.start',
                array(
                    'method' => $this->getMethod(),
                    'url' => $this->getUrl(),
                    'args' => $this->getArgs()
                )
            )
        );
        # load content
        $out = $this->loadContent( $this->getMethod(), $this->getUrl(), $this->getArgs() );
        # filter content
        $out = $this->getDispatcher()->filter(
            new \sfEvent( $this, 'filter.output'),
            $out
        );
        # display
        $this->getDispatcher()->notify(
            new \sfEvent( $this, 'dispatch.stop', array( 'output' => $out ) ) );
    }

    public function loadContent( $method, $url, array $args = array() ) {
        try {
            # prepare url and arguments
            list( $url, $args ) = $this->filterUrlAndArgs( $url, $args );
            if ( !$url ) {
                throw new Http404Error();
            }
            # handle request
            $event = $this->getDispatcher()->notifyUntil(
                new \sfEvent(
                    $this,
                    sprintf( 'handle.%s', $method ),
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
        return $out;
    }

    protected function filterUrlAndArgs( $url, array $args ) {
        $url = $this->getDispatcher()->filter(
            new \sfEvent( $this, 'filter.url' ),
            $url
        )->getReturnValue();
        $args = $this->getDispatcher()->filter(
            new \sfEvent( $this, 'filter.args' ),
            $args
        )->getReturnValue();
        return array( $url, $args );
    }

}
