<?php 
namespace html {
	//////////////////////////////////////////////////
	/* 	MENU
	 		-	A menu has menu elements, which in turn have an element name that is displayed, an image id, 
	 			associating an image with the element name, a display position and a "publish" attribute, wich 
				indicates wheather or not a menu element should be displayed or stay hidden.
			-	A menu can be constructed manually, by asigning values to each Menu_Element, or a menu can be
				constructed from columns in a db, by reading the relevant columns with the function get_Menu_Elements_from_db,
				which returns an array of Menu_Element_Data, which can be read into a Menu_Element
	
	*/
	class Menu_Element extends \html\HTML_Object {
		public function __construct() {
			$this->indent = 0;
			$this->me_data = new Menu_Element_Data();
		}
		public function set_url_image($url) {
			$this->url_image = $url;
		}
		public function set_url_link($url) {
			$this->url_link = $url;
		}
		public function set_text_link($text) {
			$this->me_data->element_name = $text;
			//$this->txt_link = $text;
		}
		public function render_HTML() {
			$indent = (int)$this->indent;
			$output    = row('<li>'																			, $indent++)
						.row(	'<div class="menu_element">'												, $indent++)
						.row(		'<a href="'.$this->url_link.'">'.$this->me_data->element_name.'</a>'	, $indent);
			if (!empty($this->url_image)) {
				$output   .= row(		'<img alt="" src="'.$this->url_image.'" />'							, $indent);
			}
			
			$output   .= row(	'</div>'																	, --$indent)
						.row('</li>'																		, --$indent);
			return $output;     
		}
		public function toString() {
			return $this->render_HTML();
		}
		public function set_data(Menu_Element_Data $data) {
			$this->me_data = clone $data;
			$this->me_data->element_name = $data->element_name;
		}
		public $me_data;
		private $url_image;
		private $url_link;
	}
	// holds all data required to buld a menu element
	class Menu_Element_Data {
		public function __construct() {
			$this->publish = 1;
			$this->disp_pos = 0;
			$this->image_id = NULL;
			$this->element_id = NULL;
			$this->element_name = NULL;
		}
		public $publish;
		public $disp_pos;
		public $image_id;
		public $element_id;
		public $element_name;
	}
	// return an array of Menu_Element_data, which can be used to fill \html\Menu_Elements, which in turn can be inserted into \html\Menus
	// the parameter col_name holds the names of the columns that connect a menu element to a db table
	function get_Menu_Elements_from_db(\db\Settings $db_settings, Menu_Element_Data $col_names) {
		$select = new \db\Select();
		$select->set_DB_login($db_settings->DB_login);
		$filter_array = $db_settings->get_filter();
		for ($i = 0; $i < count($filter_array); $i++) {
			$select->add_val($filter_array[$i]->column, $filter_array[$i]->value);
		}
		$result = $select->query();
		if ($result !== false) {
			while ($obj = $result->fetch_object()) {
				$me = new Menu_Element_Data();
				
				$publish = $col_names->publish;
				$pos = $col_names->disp_pos;
				$name = $col_names->element_name;
				$element_id = $col_names->element_id;
				$image_id = $col_names->image_id;
				
				$me->publish = $obj->$publish;
				$me->disp_pos = $obj->$pos;
				$me->image_id = $obj->$image_id;
				$me->element_id = $obj->$element_id;
				$me->element_name = $obj->$name;
				
				$output[] = $me;
			}
			if (count($output) > 0)
				return $output;
		}
		return false;
	}
	class Menu extends \html\HTML_Object {
		public function __construct() {
			$this->indent = 0;
		}
		public function add_element(Menu_Element $element) {
			$element->indent = $this->indent + 1;
			$temp_element = clone $element;
			$temp_element->me_data = clone $element->me_data;
			$this->elements[] = $temp_element;
		}
		public function render_HTML() {
			$indent = (int)$this->indent;
			
			if (!empty($this->id)) {
				$id = ' id="'.$this->id.'"';
			} else $id = '';
			
			$output = 	 row('<div'.$id.'>'			,   $indent++)
						.row(	'<ul class="menu">'	,   $indent++);
			for ($i = 0; $i < count($this->elements); $i++) {
				$this->elements[$i]->indent = $indent;
				$output .= $this->elements[$i]->render_HTML();
			}
			$output 	.= row(  '</ul>'			, --$indent)
						.row('</div>'				, --$indent);
			return $output;
		}
		public function toString() {
			return $this->render_HTML();
		}
		public $id;
		protected $elements;
	}
	// not used any more.
	class Main_Menu extends \html\Menu {
		public function __construct() {
			parent::__construct();
			$this->id = 'main_menu';
		}
	}
	class Edit_Menu extends \html\Menu {
		public function __construct() {
			parent::__construct();
			$this->id = 'edit_menu';
		}
	}
	//////////////////////////////////////////////////////////////////
}
?>