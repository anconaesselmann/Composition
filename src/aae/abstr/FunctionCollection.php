<?php
/*
	unit tested
 */
namespace aae\abstr {
	/**
	 *
	 * @package aae\abstr
	 */
	abstract class FunctionCollection /*extends \aae\abstr\Uninstantiable*/ {
		protected static $_mockFunctionReturns = array();


		public static function __callStatic($method, $args) {
			if (count(static::$_mockFunctionReturns) > 0) {
				if (array_key_exists($method, static::$_mockFunctionReturns)) {
					$results = static::$_mockFunctionReturns[$method];
					if (count($args) < 1) {
						return $results[0]->result;
					}
					foreach ($results as $rslt) {
						$rsltArguments = $rslt->arguments;
						$rsltArgumentsSize = count($rsltArguments);
						for ($i=0; $i < count($args); $i++) {
							if ($i < $rsltArgumentsSize &&
								$args[$i] == $rsltArguments[$i]) {
								return $rslt->result;
							}
						}
					}
				}
			}
			return call_user_func_array(get_called_class().'::'.$method, $args);
		}

		public static function mockFunctionReturn($functionName, $result, $arguments = array()) {
			if (!is_array($arguments)) {
				$arguments = array($arguments);
			}
			$obj = new \stdClass();
			$obj->result = $result;
			$obj->arguments = $arguments;

			if (!array_key_exists($functionName, static::$_mockFunctionReturns)) {
				static::$_mockFunctionReturns[$functionName] = array();
			}
			static::$_mockFunctionReturns[$functionName][] = $obj;
		}

		public static function clearMockReturns() {
			static::$_mockFunctionReturns = array();
		}
	}
}