<?php
namespace TypeRocket\Traits;

class Reindex {

	public function integerArray( array $builder) {

		foreach($builder as $key => $value) {
			if(is_array($value)) {
				$builder[$key] = $this->integerArray($value);
			}
		}

		if( is_integer(key($builder)) ) {
			$new = array_values($builder);
		} else {
			$new = $builder;
		}

		return $new;
	}

}