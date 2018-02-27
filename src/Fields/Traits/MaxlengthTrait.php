<?php

namespace TypeRocket\Fields\Traits;

use TypeRocket\Html\Generator;

trait MaxlengthTrait
{
    
    public $hideMaxlength = false;
    
    /**
     * Get the max length for text type fields
     *
     * @param $value
     * @param $maxLength
     *
     * @return string|\TypeRocket\Html\Generator
     */
    public function getMaxlength( $value, $maxLength )
    {
        if ( $maxLength != null && $maxLength > 0 && !$this->hideMaxlength) {
            $left = ( (int) $maxLength ) - mb_strlen( $value );
            $max = new Generator();
            $max->newElement('p', ['class' => 'tr-maxlength'], 'Characters left: ')->appendInside('span', [], $left);
            $max = $max->getString();
        } else {
            $max = '';
        }
        return $max;
    }
    
    public function hideMaxlength() {
        $this->hideMaxlength = true;
        
        return $this;
    }
}
