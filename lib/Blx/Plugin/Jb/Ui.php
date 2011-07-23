<?php
namespace Blx\Plugin\Jb;

class Ui extends \Blx\Plugin {
    protected $mapping = array(
        'filter.output' => 'output',
    );

    public function output( \sfEvent $event, $content ) {
        $content = strtr(
            $content,
            array(
                '[favicon]' => \JBUi::faviconMarkup(),
                '[jb_js]' => \JBUi::jbJsMarkup() . \JBUi::jbAnalyticsMarkup(),
                '[jb_css]' => \JBUi::resetStyleMarkup() . \JBUi::jbCssMarkup(),
                '[jb_menu]' => \JBUi::jbMenuMarkup(),
            )
        );
        return $content;
    }
}
