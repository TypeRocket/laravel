<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Config;

class Items extends Field implements ScriptField
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'items' );
    }

    public function enqueueScripts() {
        $paths = Config::getPaths();
        \TypeRocket\Assets::addToFooter('js', 'items-list', $paths['urls']['js'] . '/items.js');
    }

    /**
     * Covert Items to HTML string
     */
    public function getString()
    {
        $name = $this->getNameAttributeString();
        $this->appendStringToAttribute( 'class', ' items-list' );
        // $this->attr['class'] = 'items-list';
        $items = $this->getValue();
        $this->removeAttribute('name');
        $generator = new Generator();

        if (! $this->getSetting('button') ) {
            $this->setSetting('button', 'Insert Item');
        }

        $list = '';

        if (is_array( $items )) {
            foreach ($items as $value) {

                $sanitize = "\\TypeRocket\\Sanitize::" . $this->getSetting('sanitize', 'attribute');

                if ( is_callable($sanitize)) {
                    $value = call_user_func($sanitize, $value );
                }

                $input = $generator->newInput( 'text', $name . '[]',  $value )->getString();
                $remove = '#remove';
                $list .= $generator->newElement( 'li', array( 'class' => 'item' ),
                    '<div class="move glyphicon glyphicon-menu-hamburger"></div><a href="'.$remove.'" class="glyphicon glyphicon-remove remove" title="Remove Item"></a>' . $input )->getString();

            }
        }

        $this->removeAttribute('id');
        $html = $generator->newInput( 'hidden', $name, '0', $this->getAttributes() )->getString();
        $html .= '<div class="btn-group">';
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'items-list-button btn btn-default',
            'value' => $this->getSetting('button')
        ) )->getString();
        $html .= $generator->newElement( 'input', array(
            'type'  => 'button',
            'class' => 'items-list-clear btn btn-default',
            'value' => 'Clear'
        ) )->getString();
        $html .= '</div>';

        if (is_null( $name ) && is_string( $this->getAttribute('data-name') )) {
            $name = $this->getAttribute('data-name');
        }

        $html .= $generator->newElement( 'ul', array(
            'data-name' => $name,
            'class'     => 'tr-items-list cf'
        ), $list )->getString();

        return $html;
    }

}
