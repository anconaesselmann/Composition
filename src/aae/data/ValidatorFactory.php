<?php
/**
 *
 */
namespace aae\data {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\data
	 */
	class ValidatorFactory extends \aae\std\SimpleFactory {
		public function build() {
			$args = func_get_args();
			if (count($args) < 2) {
				throw new \Exception(sprintf("The build function for '%s' requires the class to be constructed and an instance of the object to be compared as arguments.", get_class($this)), 211141637);
			}
			$args[0] = $args[0]."Val";
			return call_user_func_array(array($this, 'parent::build'), $args);
		}
	}
}