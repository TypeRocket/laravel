<?php
namespace TypeRocket\Fields;

trait OptionTraits {

    public $options;

    public function setOption( $key, $value )
    {
        $this->options[ $key ] = $value;

        return $this;
    }

    public function setOptions( $options )
    {
        $this->options = $options;

        return $this;
    }

    public function getOption( $key, $default = null )
    {
        if ( ! array_key_exists( $key, $this->options ) ) {
            return $default;
        }

        return $this->options[ $key ];
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function removeOption( $key )
    {
        if ( array_key_exists( $key, $this->options ) ) {
            unset( $this->options[ $key ] );
        }

        return $this;
    }

    public function setOptionsFromModelClass( $model, $key, $value )
    {
        $options = [];
        $label = '';

        if(class_exists($model)) {
            $all = $model::all();

            if($all) {
                foreach($all as $item) {

                    if(is_array($key)) {

                        foreach($key as $str) {
                            $label .= ' ' . $item->{$str};
                        }

                    } else {
                        $label = $item->{$key};
                    }

                    $options[trim($label)] = $item->{$value};
                    $label = '';
                }
            }
        }
        
        $this->options = $options;

        return $this;
    }
    
}