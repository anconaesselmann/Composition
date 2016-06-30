<?php
namespace aae\html\form {
	class Checkbox extends \aae\html\form\FormObject {
		public function __construct($name = NULL, $checked=false, $disabled=false) {
			parent::__construct();
			$this->name 	= $name;
			$this->checked 	= $checked;
			$this->disabled = $disabled;
			$this->onchange = NULL;
		}
		public function toHtml() {
			$output = NULL;
			$output .= $this->_renderLabel();
			$output .= '<input name="'.$this->name.'" type="checkbox"';
			if ($this->checked) {
				$output .= ' checked';	
			}
			if ($this->disabled) {
				$output .= ' disabled';	
			}
			if ($this->onchange !== NULL) {
				$output .= ' onchange="'.$this->onchange.'"';
			}
			$output .= ' />';
			return $output;	
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $checked;
		public $disabled;
		public $onchange;
	}
}