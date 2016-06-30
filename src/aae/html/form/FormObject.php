<?php
namespace aae\html\form {
	class FormObject extends \aae\html\HTMLObject {
		public $label;
		public $enabled;
		public $class;
		public $group;
		public function __construct() {
			parent::__construct();
			$this->label =NULL;
			$this->enabled = true;
			$this->class = NULL;
			$this->group = false;
		}
		protected function _renderLabel() {
			if ($this->label !== NULL)
				return \aae\html\row('<label for="'.$this->name.'">'.$this->label.'</label>', $this->indent);
			else return NULL;
		}
		protected function _renderParent() {
			$out = NULL;
			$out .= parent::renderParent();
			if ($this->enabled === false) {
				$out .= ' disabled="disabled"';
			}
			return $out;
		}
	}
}