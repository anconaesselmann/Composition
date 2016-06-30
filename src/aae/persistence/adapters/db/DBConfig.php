<?php
/**
 *
 */
namespace aae\persistence\adapters\db {
	/**
	 * @author Axel Ancona Esselmann
	 * @package aae\persistence\adapters\db
	 */
	class DBConfig implements \arrayaccess {
		use \aae\abstr\Configurable;

		public function __construct($configFileDir) {
			$this->initConfigurable($configFileDir);
		}

		private function _initializeWithArray($configArray) {
			$this->configs = $configArray;
			if (!array_key_exists('host', $this->configs)) {
				$this->configs['host'] = null;
			}
			if (!array_key_exists('user', $this->configs)) {
				$this->configs['user'] = null;
			}
			if (!array_key_exists('password', $this->configs)) {
				$this->configs['password'] = null;
			}
			if (!array_key_exists('dbName', $this->configs)) {
				$this->configs['dbName'] = null;
			}
			if (!array_key_exists('tableName', $this->configs)) {
				$this->configs['tableName'] = null;
			}
			if (!array_key_exists('port', $this->configs)) {
				$this->configs['port'] = null;
			}
			if (!array_key_exists('socket', $this->configs)) {
				$this->configs['socket'] = null;
			}
		}
		
		// arrayaccess interface implementation

	    public function offsetSet($offset, $value) {
	        if (is_null($offset)) {
	            $this->configs[] = $value;
	        } else {
	            $this->configs[$offset] = $value;
	        }
	    }
	    public function offsetExists($offset) {
	        return isset($this->configs[$offset]);
	    }
	    public function offsetUnset($offset) {
	        unset($this->configs[$offset]);
	    }
	    public function offsetGet($offset) {
	        return isset($this->configs[$offset]) ? $this->configs[$offset] : null;
	    }
	}
}