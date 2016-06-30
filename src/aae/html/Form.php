<?php
namespace aae\html {
	class Form extends \aae\html\HTMLObject {
		public function __construct($submit_url = NULL) {
			$this->submit_url = $submit_url;
			$this->indent = 0;
			$this->submit_caption = 'submit';
		}
		public function addElement($html_element) { 
			//$test = clone $html_element;
			$this->elements[] = $html_element;
		}
		public function toHtml() {
			$indent = $this->indent;
			$output = "\n";
			$output .= $this->row('<form method="post" action="'.$this->submit_url.'" enctype="multipart/form-data">', $indent);	////////!!!!!!! see if this causes trouble!!!!!!!!!!!!
			$output .= $this->row(	'<table border="0">', ++$indent);	
			//++$indent;
			for ($i = 0; $i < count($this->elements); $i++) {
				$this->elements[$i]->indent = $indent;
				$output .= $this->elements[$i];
			}
			
			$output .= $this->row(    '<tr>', ++$indent);
			$output .= $this->row(      '<td></td>', ++$indent);
			$output .= $this->row(      '<td>'.new \aae\html\form\Submit($this->submit_caption).'</td>', $indent);
			$output .= $this->row(    '</tr>', --$indent);
			
			$output .= $this->row(  '</table>', --$indent);				
			
			$output .= $this->row('</form>',--$indent);
			return $output;
		}
		public function __toString() {
			return $this->toHtml();
		}
		private $elements;
		public $submit_url;
		public $submit_caption;
	}
}