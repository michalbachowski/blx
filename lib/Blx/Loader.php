<?php
namespace Blx;

class Loader {
    protected $namespaceSeparator = '\\';
    protected $extension = '.php';

    public function __construct( ) {
    }

    public function autoload( $className ) {
        $className = ltrim($className, $this->namespaceSeparator);

        if ( !strstr($className, $this->namespaceSeparator, true) ) {
            return false;
        }
        $rest = strrchr($className, $this->namespaceSeparator);
        $replacement = str_replace(
            $this->namespaceSeparator,
            DIRECTORY_SEPARATOR,
            substr(
                $className,
                0,
                strlen($className) - strlen($rest)
            )
        );
        $replacement .= str_replace(
            array( '_', $this->namespaceSeparator ),
            '/',
            $rest
        );

        require $replacement . $this->extension;
        return true;
    }
}
