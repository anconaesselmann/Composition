<?php
/**
 *
 */
namespace aae\draw {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\draw
	 */
    use aae\std\std;
	class Parameters {
        protected $_settings = [];

        public function set($setting) {
            $this->_settings[get_class($setting)] = $setting;
        }
        public function format($namespace, $seperator, $assignment) {
            $keyValues = [];
            foreach ($this->_settings as $value) {
                $castValue   = Parameters::_cast($value, $namespace);
                $keyValues[] = $castValue->getName().$assignment.$castValue->getStringValue();
            }
            return implode($seperator, $keyValues);
        }

        public function toDom($document, $parent, $namespace) {
            foreach ($this->_settings as $value) {
                $castValue   = Parameters::_cast($value, $namespace);
                $node = $document->createAttribute($castValue->getName());
                $node->value = $castValue->get();
                $parent->appendChild($node);
                // var_dump($parent);
            }
        }


        private static function _cast($object, $namespace) {
            $className = $namespace."parameters\\".std::classFromNSClassName(get_class($object));
            $value     = Parameters::_castValue($object->getValue(), $namespace);

            return new $className($value);
        }
        private static function _castValue($value, $namespace) {
            if ($value instanceof \aae\draw\Color) {
                $convertedColorName = $namespace."Color";
                list($r, $g, $b)    = $value->getRgb();
                $value              = new $convertedColorName($r, $g, $b);
            }
            return $value;
        }
    }
}