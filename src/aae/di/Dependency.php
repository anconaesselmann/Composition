<?php
/**
 *
 */
namespace aae\di {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\di
	 */
	class Dependency {
        private $_name, $_className, $_isStatic = false, $_args = [], $_isObject = true, $_nonObjectValue;

        public function __construct($depName = NULL, $assoc = NULL, $resolve = true) {
            $this->_name = $depName;
            if (!is_null($depName) && !is_null($assoc)) $this->set($depName, $assoc, $resolve);
        }

        public function set($depName, $assoc, $resolve = true) {
            if (!array_key_exists($depName, $assoc)) throw new \Exception("The dependency \"$depName\" does not exist", 212151215);
            $dep = $assoc[$depName];
            $this->_name = $depName;
            if (is_array($dep)) {
                $this->_className = $dep["class"];
                if (array_key_exists("static", $dep)) $this->_isStatic = $dep["static"];
                if (array_key_exists("args", $dep)) {
                    foreach ($dep["args"] as $arg) {
                        if (is_array($arg) && array_key_exists("dep", $arg)) {
                            $newDep = new Dependency($arg["dep"]);
                            if ($resolve) {
                                $newDep->set($arg["dep"], $assoc);
                            }
                            $arg = $newDep;
                        }
                        $this->_args[] = $arg;
                    }
                }
            } else {
                $this->_nonObjectValue = $dep;
                $this->_isObject = false;
            }

            return $this;
        }
        public function getName() {
            return $this->_name;
        }
        public function getClassName() {
            return $this->_className;
        }
        public function getArgs() {
            return $this->_args;
        }
        public function getObjectValue() {
            return $this->_nonObjectValue;
        }
        public function json() {
            if ($this->_isObject) {
                $assoc = [
                    "class"  => $this->_className,
                    "static" => $this->_isStatic
                ];
                if (count($this->_args) > 0) {
                    $args = [];
                    foreach ($this->_args as $arg) {
                        if (is_object($arg) && get_class($this) === get_class($arg)) {
                            $arg = ["dep"=>$arg->getName()];
                        }
                        $args[] = $arg;
                    }
                    $assoc["args"] = $args;
                }
                return json_encode($assoc, JSON_PRETTY_PRINT);
            } else {
                return $this->_nonObjectValue;
            }
        }
        public function __toString() {
            if ($this->_isObject) {
                // $assoc = [
                //     "class"  => $this->_className,
                //     "static" => $this->_isStatic
                // ];
                // if (count($this->_args) > 0) {
                //     $args = [];
                //     foreach ($this->_args as $arg) {
                //         if (is_object($arg) && get_class($this) === get_class($arg)) {
                //             $arg = ["dep"=>$arg->getName()];
                //         }
                //         $args[] = $arg;
                //     }
                //     $assoc["args"] = $args;
                // }
                // return json_encode($assoc);
                return "";
            } else {
                $result = $this->_nonObjectValue;
                if (is_string($result)) {
                    $result = '"'.$result.'"';
                }
                return $result;
            }
        }
    }
}