<?php
/*
	unit tested
 */
namespace aae\std {
	define('aae\std\PHP_MAX_NBR_BITS', \PHP_INT_SIZE*8 - 1);
	// this constant only has to exist until I allow for an arbitrary amount of bits in a BitField.
	define('aae\std\MAX_NUMBER_BITS_EXCEEDED_EXCEPTION', 'In it\'s current form, BitField only supports a maximum of '.\aae\std\PHP_MAX_NBR_BITS.' bits per BitField on this system. Caution! Maximum numbers of bits will vary from system to system.');
	
	/**
	 * BitField is an ADT that can hold up to 64 (32 on a 32 bit system) boolean values 
	 * and store them in a single integer.
	 *
	 * @package aae\std
	 */
	class BitField {
		private $_bits = 0;
		private $_maxSize = PHP_MAX_NBR_BITS;

		/**
		 * @param integer $binaryStringOrInt Either a positive integer or a binary string.
		 * @param [type]  $maxSize           The maximum number of values allowed and number 
		 *                                   of values displayed when outputting as a binary string
		 */
		public function __construct($binaryStringOrInt = 0, $maxSize = PHP_MAX_NBR_BITS) {
			if (!is_int($maxSize) || $maxSize < 0) throw new \Exception(sprintf(constant\ERROR::NOT_POSITIVE_INT, 'maxSize'));
			if ($maxSize > PHP_MAX_NBR_BITS) throw new \Exception(MAX_NUMBER_BITS_EXCEEDED_EXCEPTION);
			$this->_maxSize = $maxSize;

			if (is_string($binaryStringOrInt)) {
				$this->_bits = bindec($binaryStringOrInt);
			} else if (is_int($binaryStringOrInt) && $binaryStringOrInt >= 0) {
				$this->_bits = $binaryStringOrInt;
			} else throw new \Exception(sprintf(constant\ERROR::WRONG_TYPE, 'binaryStringOrInt', 'integer" or "binary string'));
			if (strlen(decbin($this->_bits))>$this->_maxSize) throw new \Exception(sprintf(constant\ERROR::INIT_VALUE_LARGER_THAN_MAX_SIZE, decbin($this->_bits), $this->_maxSize));
		}

		public function __toString() {
			return $this->toBinaryStr();
		}

		/**
		 * Outputs the BitField in integer representation.
		 * 
		 * @return int The BitField as an integer
		 */
		public function toDec() {
			return $this->_bits;
		}

		/**
		 * Outputs the BitField as a binary string with number of significant 
		 * bits as determined by $maxSize, which is specified during construction.
		 * 
		 * @return string The BitField in string representation
		 */
		public function toBinaryStr() {
			return str_pad(decbin($this->_bits), $this->_maxSize, 0, STR_PAD_LEFT);
		}

		/**
		 * Get the value of the bit at $offset. Offsets begin with 0.
		 * 
		 * @param  int $offset the offset of the desired bit
		 * @return bool         the desired bit
		 */
		public function get($offset) {
			$this->_errorCheckOffsetValue($offset);
			$mask = 1 << $offset; 
			return ($mask & $this->_bits) == $mask; 
		}

		/**
		 * Sets the bit at $offset to true. Offsets begin with 0.
		 * 
		 * @param int $offset the offset of the bit to be set.
		 */
		public function set($offset) { 
			$this->_errorCheckOffsetValue($offset);
			$this->_bits |= 1 << $offset;
		}

		/**
		 * Sets the bit at $offset to false. Offsets begin with 0.
		 * 
		 * @param  [type] $offset the offset of the bit to be reset.
		 */
		public function reset ($offset) { 
			$this->_errorCheckOffsetValue($offset);
			$this->_bits &= ~ (1 << $offset);
		}

		/**
		 * Toggle the bit at $offset. Offsets begin with 0.
		 * 
		 * @param  int $offset the offset of the bit to be toggled
		 * @return boolean         The value of the bit at $offset after toggling.
		 */
		public function toggle($offset) {
			$this->_errorCheckOffsetValue($offset);
			$this->_bits ^= 1 << $offset; 
			return $this->get($offset); 
		}

		/**
		 * Set the bit at $offset to $value.
		 * 
		 * @param int $offset The offset of the bit to be set to $value.
		 * @param boolean $value  The value to which the bit at $offset is set.
		 */
		public function setToValue($offset, $value) {
			$this->_errorCheckOffsetValue($offset);
			if (is_bool($value)) {
				if ($value) $this->set($offset);
				else $this->reset($offset);
			} else throw new \Exception(sprintf(constant\ERROR::WRONG_PRIMITIVE_TYPE, 'value', 'boolean'));
		}

		private function _errorCheckOffsetValue($offset) {
			if (!is_int($offset) || $offset < 0) throw new \Exception(sprintf(constant\ERROR::NOT_POSITIVE_INT, 'offset'));
			if ($offset >= $this->_maxSize) throw new \Exception(sprintf(constant\ERROR::OFFSET_LARGER_THAN_MAX_SIZE, $offset, $this->_maxSize));
		}
	}
}