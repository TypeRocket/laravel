<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Config;

class Editor extends Textarea implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'editor' );
    }

    public function enqueueScripts() {
        $paths = Config::getPaths();
        $v = Config::getAssetVersion();
	    \TypeRocket\Assets::addToFooter('js', 'typerocket-editor-image', $paths['urls']['js'] . '/redactor.min.js?v='.$v);
	    \TypeRocket\Assets::addToFooter('js', 'typerocket-editor', $paths['urls']['js'] . '/editorImage.js?v='.$v);
    }

    /**
     * Covert Editor to HTML string
     */
    public function getString()
    {
        $max = '';
        $generator = new Generator();
        $this->setAttribute('name', $this->getNameAttributeString());
        $value = $this->getValue();
        $this->appendStringToAttribute('class', ' typerocket-editor ');
        $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'editor');

        if ( is_callable($sanitize)) {
            $value = call_user_func($sanitize, $value );
        }

        $maxLength = $this->getAttribute('maxlength');

        if ( $maxLength != null && $maxLength > 0) {
            $left = (int) $maxLength - strlen( utf8_decode( $value ) );
            $max = new Generator();
            $max->newElement('p', array('class' => 'tr-maxlength'), 'Characters left: ')->appendInside('span', array(), $left);
            $max = $max->getString();
        }

        return $generator->newElement( 'textarea', $this->getAttributes(), $value )->getString() . $max;
    }

}
