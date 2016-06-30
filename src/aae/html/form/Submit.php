<?php
namespace aae\html\form {
	/* 
	to do: incoorborate indent if given
	*/
	class Submit extends \aae\html\HTMLObject {
		public function __construct($caption = NULL) {
			parent::__construct();
			$this->caption = $caption;
		}
		public function toHtml() {
			return '<input'.$this->renderParent().' type="submit" value="'.$this->caption.'" />';
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $caption;
	}
}