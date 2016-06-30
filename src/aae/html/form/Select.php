<?php
namespace aae\html\form {
	class Select extends \aae\html\form\FormObject {
		public function __construct($name = NULL, $disabled=false) {
			parent::__construct();
			$this->name 	= $name;
			$this->disabled = $disabled;
			$this->onchange = NULL;
			$this->selected = NULL;
			$this->size = NULL;
		}
		public function add(\aae\html\form\SelectOption $option) {
			$this->_options[] = clone $option;
		}
		public function option($variable, $caption) {
			$option = new \aae\html\form\SelectOption($variable, $caption);
			$this->_options[] = clone $option;
		}
		public function toHtml() {
			$output = NULL;
			$open = NULL;
			
			$indent = $this->indent;
			
			// added, possibly remove
			if (!empty($this->caption)) {
				$output .=  $this->row(	'<tr>', $indent++)
						.$this->row(		'<td>', $indent++)
						.$this->row(			$this->caption, $indent++)
						.$this->row(		'</td>', --$indent)
						.$this->row(		'<td>', $indent++);
			}
			
			// end
			
			
			$open .= '<select name="'.$this->name.'" ';
			if ($this->disabled) {
				$open .= ' disabled';	
			}
			if ($this->size !== NULL) {
				$open .= ' size="'.$this->size.'"';
			}
			if ($this->onchange !== NULL) {
				$open .= ' onchange="'.$this->onchange.'"';
			}
			$open .= '>';
			$close = '</select>';
			
			
			
			
			
			
			
			
			if ($this->group === true) $output .= $this->row('<div class="group">', $indent++);
			$output .= $this->_renderLabel();
			$output .= $this->row($open, $indent++);
			for ($i = 0; $i < count($this->_options); $i++) {
				$this->_options[$i]->indent = $indent;
				if ($this->selected == $this->_options[$i]->value) {
					$this->_options[$i]->selected = true;
				}
				$output .= $this->_options[$i];
			}
			$output .= $this->row($close, --$indent);
			if ($this->group === true) $output .= $this->row('</div>', --$indent);
			
			
			
			// added, possibly remove
			if (!empty($this->caption)) {
				$output .=  $this->row(		'</td>', --$indent)
						.$this->row(	'</tr>', --$indent);
			}
			
			// end
			
			
			
			return $output;	
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $disabled;
		public $onchange;
		public $selected;
		public $size;
		protected $_options;
	}
}