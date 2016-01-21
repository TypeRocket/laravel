<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Config;

class Image extends Field implements ScriptField
{

    private $mediaProviderClass = null;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'image' );
    }

    public function enqueueScripts() {
        $paths = Config::getPaths();
        Assets::addToFooter('js', 'typerocket-vue', $paths['urls']['js'] . '/vue.min.js');
        Assets::addToFooter('js', 'typerocket-image', $paths['urls']['js'] . '/image.js');
    }

    /**
     * Covert Image to HTML string
     */
    public function getString()
    {
        $name = $this->getNameAttributeString();
        $this->setAttribute( 'class', 'photo-picker' );
        $value = $this->getValue();
        $this->removeAttribute( 'name' );
        $generator = new Generator();

        if ( ! $this->getSetting( 'button' )) {
            $this->setSetting( 'button', 'Insert Image' );
        }

        if ($value != "") {
            $img = new {$this->mediaProviderClass}();
            $img->findById($value);
            $src = $img->getThumbsrc();
            $image = "<img src=\"{$src}\" />";
        } else {
            $image = '';
        }

        if (empty( $image )) {
            $value = '';
        }

        $html = $generator->newInput( 'hidden', $name, $value, $this->getAttributes() )->getString();
        $html .= '<div class="btn-group">';
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'image-picker-button btn btn-default',
            'value' => $this->getSetting( 'button' )
        ) )->getString();
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'image-picker-clear btn btn-default',
            'value' => 'Clear'
        ) )->getString();
        $html .= '</div>';
        $html .= $generator->newElement( 'div', array(
            'class' => 'image-picker-placeholder'
        ), $image )->getString();

        return $html;
    }

    public function setMediaProviderClass($class) {
        $this->mediaProviderClass = $class;
    }

}