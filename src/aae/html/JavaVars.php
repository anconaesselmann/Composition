<?php 
namespace aae\html {
	class JavaVars extends \aae\html\HTMLObject {
		protected $_var_array;
		public function __construct() {
			
		}
		// var type can be 'numeric', 'bool'
		public function set($var_name, $var_value) {
			$this->_var_array[$var_name] = $var_value;
		}
		public function toHtml() {
			$indent = $this->indent;
			$out = NULL;
			if (count($this->_var_array) > 0) {
				$out .= \aae\html\row('<script language="javascript" type="text/javascript">'	, $indent++);
				foreach ($this->_var_array as $key => $value) {
					switch (gettype($value)) {
						case 'integer': // untested
							break;
						case 'boolean':
							if ($value === true) $value = 'true';
							else if ($value === false) $value = 'false';
							break;
						case 'double': // untested
							break;
						case 'string':
							$value = '"'.$value.'"';
							break;
						case 'array': // write this!
							break;
						case 'object': // write this!
							break;
						case 'resource': // write this!
							break;
						case 'NULL':
							$value = 'NULL'; // untested
							break;
						
							
						default:
							$value = '"'.$value.'"';
					}
					$out.=\aae\html\row(	'var '.$key.' = '.$value.';'						 ,   $indent);
				}
				$out .=	 \aae\html\row('</script>',     --$indent);
			}
			return $out;
		}
		public function __toString() {
			return $this->toHtml();
		}
	}
}