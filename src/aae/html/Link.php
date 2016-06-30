<?php 
namespace aae\html {
	class Link extends \aae\html\HTMLObject {
		public function __construct($caption = NULL, $url = NULL) {
			parent::__construct();
			$this->caption = $caption;
			$this->url = $url;
		}
		public function toHtml() {
			if (!empty($this->class_name))
				$this->class = $this->class_name; // historic stupidity
			
			if (!empty($this->class_name)) {
				return '<span'.$this->render_parent().'">'.'<a href="'.$this->url.'">'.$this->caption.'</a>'.'</span>';
			} else return '<a'.$this->render_parent().' href="'.$this->url.'">'.$this->caption.'</a>';
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $url;
		public $class_name;
	}
}