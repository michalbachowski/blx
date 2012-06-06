<?php
namespace Blx\Plugin;

class Editable extends \Blx\Plugin {
    protected $mapping = array(
        'handle.get' => 'get',
        'filter.output' => 'filter',
    );
    protected $insideLoop = false;
    protected $content = '';

    private function getForm( $event ) {
        $form = <<<EOF
<form method="post" action="%s" id="form-edit">
    <div class="text" id="box-title">
        <label for="title">%s</label>
        <input type="text" name="title" id="title" value="%s" />
    </div>
    <div class="textarea" id="box-content">
        <label for="content">%s</label>
        <textarea id="content" name="content">[raw_content]</textarea>
    </div>
    <div class="submit" id="box-submit">
        <input type="submit" value="%s" />
    </div>
</form>
EOF;
        return sprintf(
            $form,
            $event->getSubject()->getUtil()->getCompleteUrl( $event['url'] ),
            _( 'Title' ),
            $this->getTitle( $event ),
            _( 'Content' ),
            _( 'Save' )
        );
    }

    protected function getEditor( \sfEvent $event ) {
        return $event->getSubject()->getDispatcher()->filter(
            new \sfEvent(
                $this,
                'plugin.editable.filter.form'
            ),
            $this->getForm( $event )
        )->getReturnValue();
    }

    protected function getTitle( \sfEvent $event ) {
        $titleEvent = $event->getSubject()->getDispatcher()->notifyUntil(
            new \sfEvent(
                $this,
                'metadata.get',
                array(
                    'key' => 'title',
                    'url' => $event['url']
                )
            )
        );
        if ( !$titleEvent->isProcessed() ) {
            return '';
        }
        return $titleEvent->getReturnValue();
    }

    public function get( \sfEvent $event ) {
        if ( $this->insideLoop ) {
            return false;
        }
        if ( !isset( $event['arguments']['edit'] ) ) {
            return false;
        }
        // verify permissions
        $permEvent = $event->getSubject()->getDispatcher()->notifyUntil(
            new \sfEvent( $this, 'acl.check.editable.form',
                array( 'event' => $event ) ) );
        if ( $permEvent->isProcessed() && !$permEvent->getReturnValue() ) {
            throw new \Blx\ForbiddenError(
                \Blx\Util::_( 'You are not allowed to acces this page.' )
            );
        }
        $this->insideLoop = true;
        $event = $event->getSubject()->getDispatcher()->notifyUntil( $event );
        # no response - error
        if ( $event->isProcessed() ) {
            $this->content = $event->getReturnValue();
        }
        $event->setReturnValue( $this->getEditor( $event ) );
        return true;
    }

    public function filter( \sfEvent $event, $out ) {
        return str_replace( '[raw_content]', $this->content, $out );
    }
}
