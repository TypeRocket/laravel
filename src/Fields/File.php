<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html,
    \TypeRocket\Config;

class File extends Field
{
    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'file' );
    }

    /**
     * Covert File to HTML string
     */
    function getString()
    {
        $input = new Generator();
        $name = $this->getNameAttributeString();
        $value = $this->getValue();
        return $input->newInput('file', $name, $value, $this->getAttributes() )->getString();
    }

}