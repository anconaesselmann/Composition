<?php
namespace aae\html\form {
	class Radiobutton extends \aae\html\form\FormObject {
		public function __construct($name = NULL, $value=false, $disabled=false) {
			parent::__construct();
			$this->name 	= $name;
			$this->value 	= $value;
			$this->disabled = $disabled;
			$this->onchange = NULL;
			$this->checked 	= false;
		}
		public function toHtml() {
			$output = NULL;
			$output .= $this->_renderLabel();
			$output .= '<input name="'.$this->name.'" type="radio"';
			if ($this->value) {
				$output .= ' value="'.$this->value.'"';	
			}
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
		public $value;
		public $checked;
		public $disabled;
		public $onchange;
	}
}