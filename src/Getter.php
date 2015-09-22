<?php
namespace TypeRocket;

use TypeRocket\Fields\Field;

class Getter {

    /** @var  Field $field */
    protected $field;

    /**
     * Get value from database from typeRocket bracket syntax
     *
     * @param $field
     *
     * @return array|mixed|null|string
     */
    public function getFieldValue( $field )
    {
        $brackets = null;

        if ($field instanceof Field) {
            $brackets = $field->getBrackets();
            $this->field = $field;
        }

        $keys = $this->geBracketKeys( $brackets );
        $data = $this->getBaseFieldValue( $keys[0] );
        return $this->parseValueData( $data, $keys );
    }
    /**
     * Parse data by walking through keys
     *
     * @param $data
     * @param $keys
     *
     * @return array|mixed|null|string
     */
    private function parseValueData( $data, $keys )
    {
        $mainKey = $keys[0];
        if (isset( $mainKey ) && ! empty( $data )) {
            if (Validate::json( $data )) {
                $data = json_decode( $data, true );
            }
            // unset first key since $data is already set to it
            unset( $keys[0] );
            if ( ! empty( $keys ) && is_array( $keys )) {
                foreach ($keys as $name) {
                    $data = ( isset( $data[$name] ) && $data[$name] !== '' ) ? $data[$name] : null;
                }
            }
        }
        return $data;
    }
    /**
     * Get keys from TypeRocket brackets
     *
     * @param $str
     * @param int $set
     *
     * @return mixed
     */
    private function geBracketKeys( $str, $set = 1 )
    {
        $regex = '/\[([^]]+)\]/i';
        preg_match_all( $regex, $str, $matches, PREG_PATTERN_ORDER );
        return $matches[$set];
    }
    /**
     * Get the value of a field if it is not an empty string or null.
     * If the field is null, undefined or and empty string it will
     * return null.
     *
     * @param $value
     *
     * @return null
     */
    protected function getValueOrNull( $value )
    {
        return ( isset( $value ) && $value !== '' ) ? $value : null;
    }

    /**
     * Get base field value
     *
     * Some fields need to be saved as serialized arrays. Getting
     * the field by the base value is used by Fields to populate
     * their values.
     *
     * @param $field_name
     *
     * @return null
     */
    protected function getBaseFieldValue( $field_name )
    {
        $data = $this->field->getForm()->getModel()->{$field_name};
        return $this->getValueOrNull($data);
    }

}