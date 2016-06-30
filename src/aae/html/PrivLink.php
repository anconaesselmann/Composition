<?php 
namespace aae\html {
	// has undesireable hardcoded link to is_privileged
	class PrivLink extends \aae\html\Link {
		public function __construct() {
			
		}
		protected function buildObject() {
			if (\ordit\is_privileged($this->privileges_have, $this->privileges_req)) {
				$this->html_string = parent::toHtml();
				
				
			} else {
				$this->html_string = false;
			}
		}
		public function toHtml() {
			$this->buildObject();
			return $this->html_string;
		}
		public function __toString() {
			return $this->toHtml();
		}
		public $privileges_req;
		public $privileges_have;
		private $html_string;
	}
}