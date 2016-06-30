<?php
namespace aae\html\form {
	class Upload extends \aae\html\HTMLObject {
		public function toHtml() {
			$indent = $this->indent;
			if (!empty($this->caption)) {
				$output = \aae\html\row('<tr>', ++$indent);
				$output .= \aae\html\row(  '<td>'.$this->caption.'</td>', ++$indent);
				$ul = '<td>';	
			}
			if (!empty($this->max_file_size)) {
				$ul .= \aae\html\row('<div class="uld"><input type="hidden" max="MAX_FILE_SIZE" value="'.$this->max_file_size.'" />', $indent);
			}
			$ul		.= \aae\html\row('<input type="file" name="'.$this->name.'" /></div>', $indent);
			
			
			if (!empty($this->caption)) {
				$ul .= '</td>';
			}
			
			$output .= \aae\html\row( $ul, $indent);
			if (!empty($this->caption)) {
				$output .= \aae\html\row( '</tr>', --$indent);
			}
			
			return $output;
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $max_file_size;
	}
}