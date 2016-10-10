<?php
namespace TypeRocket\Fields;

use \TypeRocket\Html\Generator,
    \TypeRocket\Config,
    \TypeRocket\Assets;

class DropZone extends Field implements ScriptField
{

    public function enqueueScripts()
    {
        $paths = Config::getPaths();
        Assets::addToFooter('js', 'typerocket-dropzone', $paths['urls']['js'] . '/dropzone.js');
    }

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'file' );
    }

    /**
     * Covert Image to HTML string
     */
    public function getString()
    {
        $input = new Generator();
        $name = $this->getNameAttributeString();
        $value = $this->getValue();
        return $input->newInput('file', $name, $value, $this->getAttributes() )->getString();
    }


}