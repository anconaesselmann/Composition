<?php
namespace aae\html\form {
	class TextField extends \aae\html\form\FormObject {
		// to do: create a Text_Format class and create a member varibale of that type. It formats the caption
		const TYPE_TEXT = 0;
		const TYPE_PASSWORD = 1;
		public function __construct($type = NULL, $name = NULL, $size = NULL, $maxlength = NULL, $default_value = NULL) {
			parent::__construct();
			TextField::$tf_count++;
			if ($name !== NULL) {
				$this->name = $name;
			} else $this->name = 'TextField'.TextField::$tf_count;
			$this->size = $size;
			$this->maxlength = $maxlength;
			$this->default_value = $default_value;
			$this->indent = 0;
			if ($type == 'text') {
				$this->tf_type = TextField::TYPE_TEXT;
			} else if ($type == 'password') {
				$this->tf_type = TextField::TYPE_PASSWORD;
			} else $this->tf_type = TextField::TYPE_TEXT;
		}
		public function toHtml() {
			$tf = NULL;
			$out = NULL;
			switch ($this->tf_type) {
				case TextField::TYPE_TEXT:
					$type_string = 'text';
					break;
				case TextField::TYPE_PASSWORD:
					$type_string = 'password';
					break;
			}
			$indent = $this->indent;
			
			// added, possibly remove
			if (!empty($this->caption)) {
				$out .=  $this->row(	'<tr>', $indent++)
						.$this->row(		'<td>', $indent++)
						.$this->row(			$this->caption, $indent++)
						.$this->row(		'</td>', --$indent)
						.$this->row(		'<td>', $indent++);
			}
			
			// end
			
			$tf .= '<input type="'.$type_string.'"';
			
			$tf .= $this->_renderParent();
			
			if ($this->size !== NULL) {
				$tf .= ' size="'.$this->size.'"';	
			}
			if ($this->maxlength !== NULL) {
				$tf .= ' maxlength="'.$this->maxlength.'"';	
			}
			if ($this->default_value !== NULL) {
				$tf .= ' value="'.$this->default_value.'"';
			}
			
			$tf .= ' />';
			
			if ($this->group === true) $out .= $this->row('<div class="group">', $indent++);
			$out .= 	$this->_renderLabel()
						.$this->row( $tf, $indent);
			if ($this->group === true) $out .= $this->row('</div>', --$indent);
			
			// added, possibly remove
			if (!empty($this->caption)) {
				$out .=  $this->row(		'</td>', --$indent)
						.$this->row(	'</tr>', --$indent);
			}
			
			// end
			
			return $out;
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $tf_type;
		public $type;
		public $size;
		public $maxlength;
		public $default_value;
		
		private static $tf_count;
	}
}