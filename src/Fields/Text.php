<?php
namespace TypeRocket\Fields;

use \TypeRocket\Fields\Traits\MaxlengthTrait;
use \TypeRocket\Html\Generator;
use \TypeRocket\Sanitize;

class Text extends Field
{
    use MaxlengthTrait;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'text' );
    }

    /**
     * Covert Test to HTML string
     */
    public function getString()
    {
        $input = new Generator();
        $name = $this->getNameAttributeString();
        $value = $this->getValue();
        $value = $this->sanitize($value, 'raw');
        $max = $this->getMaxlength( $value, $this->getAttribute('maxlength'));
        return $input->newInput($this->getType(), $name, Sanitize::attribute($value), $this->getAttributes() )->getString() . $max;
    }

}