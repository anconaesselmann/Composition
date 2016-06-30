<?php
namespace aae\html {
	class HTMLObject {
		public $indent;
		public $caption;
		public $name;
		public $title;
		public $class;
		public $id;
		public function __construct() {
			$this->id = NULL;
			$this->class = NULL;
		}
		protected function renderParent() {
			$out = NULL;
			if ($this->name !== NULL) {
				$out .= ' name="'.$this->name.'"';
			}
			if ($this->class !== NULL) {
				$out .= ' class="'.$this->class.'"';
			}
			if ($this->id !== NULL) {
				$out .= ' id="'.$this->id.'"';
			}
			if (!empty($this->title)) {
				$out .= ' title="'.$this->title.'"';
			}
			return $out;
		}

		public static function row($output, $indent = 0) {
			if ($indent >= 0) {
				return self::getIndent($indent).$output."\n";
			} else {
				return $output."\n";
			}
		}
		
		public static function getIndent($indent) {
			$space = '  ';
			for ($i = 0; $i < $indent; $i++) {
				if ($i == 0) {
					$output = $space;
				} else {
					$output .= $space;
				}
			}
			if (!empty($output))
				return $output;
			else return false;
		}
		public static function bold($text_string) {
			return '<strong>'.$text_string.'</strong>';
		}
		
		public static function urlVarStr($value_array) { // make url safe!!!!!!!!!!!!!!!
			$i = 0;
			foreach ($value_array as $key => $value) {
				if ($i < 1) {
					$output = '?'.$key.'='.$value;	
				} else {
					$output .= '&'.$key.'='.$value;
				}
				$i++;
			}
			return $output;
		}
	}
}