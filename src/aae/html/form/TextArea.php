<?php
namespace aae\html\form {
	class TextArea extends \aae\html\form\FormObject {
		public function __construct() {
			$this->class = NULL;
		}
		public function toHtml() {
			$output = NULL;
			$ta = NULL;
			$indent = $this->indent;
			if (!empty($this->caption)) {
				$output .= \aae\html\row('<tr>', ++$indent);
				$output .= \aae\html\row(  '<td valign="top">'.$this->caption.'</td>', ++$indent);
				$ta .= '<td>';	
			}
			
			$ta .= '<textarea name="'.$this->name.'"';
			if ($this->class !== NULL) {
				$ta .= ' class="'.$this->class.'"';
			}
			if ($this->cols) {
				$ta .= ' cols="'
					.  (int)$this->cols
					.  '"';
			}
			if ($this->rows) {
				$ta .= ' rows="'
					.  (int)$this->rows
					.  '"';
			}
			
			$ta .= '>'.$this->default_value.'</textarea>';
			$output .=  \aae\html\row($ta, $indent);
			if (!empty($this->caption)) {
				$output .= \aae\html\row( '</tr>', --$indent);
			}	
			
			return $output;
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $cols;
		public $rows;
		public $default_value;	
	}
}