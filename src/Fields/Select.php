<?php
namespace TypeRocket\Fields;

use TypeRocket\Fields\Traits\DefaultSetting;
use \TypeRocket\Html\Generator;
use TypeRocket\Fields\Traits\OptionTraits;

class Select extends Field implements OptionField
{
    use OptionTraits, DefaultSetting;

    protected $nullable = false;

    /**
     * Run on construction
     */
    protected function init()
    {
        $this->setType( 'select' );
    }

    /**
     * Covert Select to HTML string
     */
    public function getString()
    {
        $default = $this->getSetting('default');
        $multi = $this->getAttribute('multiple', null) ? '[]' : '';
        $this->setAttribute('name', $this->getNameAttributeString() . $multi);
        $option = $this->getValue();
        $option = ! is_null($option) ? $option : $default;
        $generator  = new Generator();
        $generator->newElement( 'select', $this->getAttributes() );
        foreach ($this->options as $key => $value) {
            if( is_array($value) ) {
                $optgroup  = new Generator();
                $optgroup->newElement( 'optgroup', ['label' => $key] );
                foreach($value as $k => $v) {
                    $attr['value'] = $v;
                    $this->setSelected($option, $v, $attr);
                    $optgroup->appendInside( 'option', $attr, (string) $k );
                }
                $generator->appendInside( $optgroup );
            } else {
                $attr['value'] = $value;
                $this->setSelected($option, $value, $attr);
                $generator->appendInside( 'option', $attr, (string) $key );
            }
        }
        return $generator->getString();
    }

    /**
     * Set Selected.
     *
     * @param $option
     * @param $v
     * @param $attr
     *
     * @return \TypeRocket\Fields\Select
     */
    protected function setSelected($option, $v, &$attr) {
        if(is_array($option) && in_array($v, $option)) {
            $attr['selected'] = 'selected';
        } elseif ( !is_array($option) && $option == $v && isset($option) ) {
            $attr['selected'] = 'selected';
        } elseif ( !is_array($option) && $option == $v && $this->nullable ) {
            $attr['selected'] = 'selected';
        } else {
            unset( $attr['selected'] );
        }

        return $this;
    }

    /**
     * Allow null values
     *
     * @return $this
     */
    public function nullable() {
        $this->nullable = true;
        return $this;
    }

    /**
     * Make select multiple
     *
     * @return $this
     */
    public function multiple()
    {
        return $this->setAttribute('multiple', 'multiple')
                    ->appendStringToAttribute('class', 'tr-multi-select');
    }

}
