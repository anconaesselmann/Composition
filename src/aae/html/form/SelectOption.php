<?php
namespace aae\html\form {
	class SelectOption extends \aae\html\HTMLObject {
		public function __construct($value = NULL, $caption = NULL) {
			$this->value = $value;
			$this->caption = $caption;
			$this->selected = false;
		}
		public function toHtml() {
			$out = NULL;
			if ($this->selected === false) {
				$selected = NULL;
			} else $selected = ' selected="selected"';
			
			$out .= $this->row('<option value="'.$this->value.'"'.$selected.'>'.$this->caption.'</option>', $this->indent);
			return $out;
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $value;
		public $caption;
		public $selected;
	}
}