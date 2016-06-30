<?php
namespace lib {
	require_once($_SERVER['DOCUMENT_ROOT'].'/../code/library.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/../code/classes/constants/descriptive_text.php');
	
	class US_States {
		public function __construct() {
			$this->_states_name[] = 'Alabama';
			$this->_states_name[] = 'Alaska';
			$this->_states_name[] = 'Arizona';
			$this->_states_name[] = 'Arkansas';
			$this->_states_name[] = 'California';
			$this->_states_name[] = 'Colorado';
			$this->_states_name[] = 'Connecticut';
			$this->_states_name[] = 'Delaware';
			$this->_states_name[] = 'Florida';
			$this->_states_name[] = 'Georgia';
			$this->_states_name[] = 'Hawaii';
			$this->_states_name[] = 'Idaho';
			$this->_states_name[] = 'Illinois';
			$this->_states_name[] = 'Indiana';
			$this->_states_name[] = 'Iowa';
			$this->_states_name[] = 'Kansas';
			$this->_states_name[] = 'Kentucky';
			$this->_states_name[] = 'Louisiana';
			$this->_states_name[] = 'Maine';
			$this->_states_name[] = 'Maryland';
			$this->_states_name[] = 'Massachusetts';
			$this->_states_name[] = 'Michigan';
			$this->_states_name[] = 'Minnesota';
			$this->_states_name[] = 'Mississippi';
			$this->_states_name[] = 'Missouri';
			$this->_states_name[] = 'Montana';
			$this->_states_name[] = 'Nebraska';
			$this->_states_name[] = 'Nevada';
			$this->_states_name[] = 'New Hampshire';
			$this->_states_name[] = 'New Jersey';
			$this->_states_name[] = 'New Mexico';
			$this->_states_name[] = 'New York';
			$this->_states_name[] = 'North Carolina';
			$this->_states_name[] = 'North Dakota';
			$this->_states_name[] = 'Ohio';
			$this->_states_name[] = 'Oklahoma';
			$this->_states_name[] = 'Oregon';
			$this->_states_name[] = 'Pennsylvania';
			$this->_states_name[] = 'Rhode Island';
			$this->_states_name[] = 'South Carolina';
			$this->_states_name[] = 'South Dakota';
			$this->_states_name[] = 'Tennessee';
			$this->_states_name[] = 'Texas';
			$this->_states_name[] = 'Utah';
			$this->_states_name[] = 'Vermont';
			$this->_states_name[] = 'Virginia';
			$this->_states_name[] = 'Washington';
			$this->_states_name[] = 'West Virginia';
			$this->_states_name[] = 'Wisconsin';
			$this->_states_name[] = 'Wyoming';
			
			
			
			$this->_states_abbr[] = 'AL';
			$this->_states_abbr[] = 'AK';
			$this->_states_abbr[] = 'AZ';
			$this->_states_abbr[] = 'AR';
			$this->_states_abbr[] = 'CA';
			$this->_states_abbr[] = 'CO';
			$this->_states_abbr[] = 'CT';
			$this->_states_abbr[] = 'DE';
			$this->_states_abbr[] = 'FL';
			$this->_states_abbr[] = 'GA';
			$this->_states_abbr[] = 'HI';
			$this->_states_abbr[] = 'ID';
			$this->_states_abbr[] = 'IL';
			$this->_states_abbr[] = 'IN';
			$this->_states_abbr[] = 'IA';
			$this->_states_abbr[] = 'KS';
			$this->_states_abbr[] = 'KY';
			$this->_states_abbr[] = 'LA';
			$this->_states_abbr[] = 'ME';
			$this->_states_abbr[] = 'MD';
			$this->_states_abbr[] = 'MA';
			$this->_states_abbr[] = 'MI';
			$this->_states_abbr[] = 'MN';
			$this->_states_abbr[] = 'MS';
			$this->_states_abbr[] = 'MO';
			$this->_states_abbr[] = 'MT';
			$this->_states_abbr[] = 'NE';
			$this->_states_abbr[] = 'NV';
			$this->_states_abbr[] = 'NH';
			$this->_states_abbr[] = 'NJ';
			$this->_states_abbr[] = 'NM';
			$this->_states_abbr[] = 'NY';
			$this->_states_abbr[] = 'NC';
			$this->_states_abbr[] = 'ND';
			$this->_states_abbr[] = 'OH';
			$this->_states_abbr[] = 'OK';
			$this->_states_abbr[] = 'OR';
			$this->_states_abbr[] = 'PA';
			$this->_states_abbr[] = 'RI';
			$this->_states_abbr[] = 'SC';
			$this->_states_abbr[] = 'SD';
			$this->_states_abbr[] = 'TN';
			$this->_states_abbr[] = 'TX';
			$this->_states_abbr[] = 'UT';
			$this->_states_abbr[] = 'VT';
			$this->_states_abbr[] = 'VA';
			$this->_states_abbr[] = 'WA';
			$this->_states_abbr[] = 'WV';
			$this->_states_abbr[] = 'WI';
			$this->_states_abbr[] = 'WY';
		}
		public function abbr($index) {
			if (($index >= 0) && ($index < $this->state_count()))
				return $this->_states_abbr[$index];
			else return false;
		}
		public function name($index) {
			if (($index >= 0) && ($index < $this->state_count()))
				return $this->_states_name[$index];
			else return false;
		}
		public function state_count() {
			return count($this->_states_abbr);
		}
		protected $_states_abbr;
		protected $_states_name;
	}
	class Countries {
		protected $_countries_name;
		public function __construct() {
			$this->_countries_name[] = 'Afghanistan';
			$this->_countries_name[] = 'Albania';
			$this->_countries_name[] = 'Algeria';
			$this->_countries_name[] = 'Andorra';
			$this->_countries_name[] = 'Angola';
			$this->_countries_name[] = 'Antigua & Deps';
			$this->_countries_name[] = 'Argentina';
			$this->_countries_name[] = 'Armenia';
			$this->_countries_name[] = 'Australia';
			$this->_countries_name[] = 'Austria';
			$this->_countries_name[] = 'Azerbaijan';
			$this->_countries_name[] = 'Bahamas';
			$this->_countries_name[] = 'Bahrain';
			$this->_countries_name[] = 'Bangladesh';
			$this->_countries_name[] = 'Barbados';
			$this->_countries_name[] = 'Belarus';
			$this->_countries_name[] = 'Belgium';
			$this->_countries_name[] = 'Belize';
			$this->_countries_name[] = 'Benin';
			$this->_countries_name[] = 'Bhutan';
			$this->_countries_name[] = 'Bolivia';
			$this->_countries_name[] = 'Bosnia Herzegovina';
			$this->_countries_name[] = 'Botswana';
			$this->_countries_name[] = 'Brazil';
			$this->_countries_name[] = 'Brunei';
			$this->_countries_name[] = 'Bulgaria';
			$this->_countries_name[] = 'Burkina';
			$this->_countries_name[] = 'Burundi';
			$this->_countries_name[] = 'Cambodia';
			$this->_countries_name[] = 'Cameroon';
			$this->_countries_name[] = 'Canada';
			$this->_countries_name[] = 'Cape Verde';
			$this->_countries_name[] = 'Central African Rep';
			$this->_countries_name[] = 'Chad';
			$this->_countries_name[] = 'Chile';
			$this->_countries_name[] = 'China';
			$this->_countries_name[] = 'Colombia';
			$this->_countries_name[] = 'Comoros';
			$this->_countries_name[] = 'Congo';
			$this->_countries_name[] = 'Congo {Democratic Rep}';
			$this->_countries_name[] = 'Costa Rica';
			$this->_countries_name[] = 'Croatia';
			$this->_countries_name[] = 'Cuba';
			$this->_countries_name[] = 'Cyprus';
			$this->_countries_name[] = 'Czech Republic';
			$this->_countries_name[] = 'Denmark';
			$this->_countries_name[] = 'Djibouti';
			$this->_countries_name[] = 'Dominica';
			$this->_countries_name[] = 'Dominican Republic';
			$this->_countries_name[] = 'East Timor';
			$this->_countries_name[] = 'Ecuador';
			$this->_countries_name[] = 'Egypt';
			$this->_countries_name[] = 'El Salvador';
			$this->_countries_name[] = 'Equatorial Guinea';
			$this->_countries_name[] = 'Eritrea';
			$this->_countries_name[] = 'Estonia';
			$this->_countries_name[] = 'Ethiopia';
			$this->_countries_name[] = 'Fiji';
			$this->_countries_name[] = 'Finland';
			$this->_countries_name[] = 'France';
			$this->_countries_name[] = 'Gabon';
			$this->_countries_name[] = 'Gambia';
			$this->_countries_name[] = 'Georgia';
			$this->_countries_name[] = 'Germany';
			$this->_countries_name[] = 'Ghana';
			$this->_countries_name[] = 'Greece';
			$this->_countries_name[] = 'Grenada';
			$this->_countries_name[] = 'Guatemala';
			$this->_countries_name[] = 'Guinea';
			$this->_countries_name[] = 'Guinea-Bissau';
			$this->_countries_name[] = 'Guyana';
			$this->_countries_name[] = 'Haiti';
			$this->_countries_name[] = 'Honduras';
			$this->_countries_name[] = 'Hungary';
			$this->_countries_name[] = 'Iceland';
			$this->_countries_name[] = 'India';
			$this->_countries_name[] = 'Indonesia';
			$this->_countries_name[] = 'Iran';
			$this->_countries_name[] = 'Iraq';
			$this->_countries_name[] = 'Ireland {Republic}';
			$this->_countries_name[] = 'Israel';
			$this->_countries_name[] = 'Italy';
			$this->_countries_name[] = 'Ivory Coast';
			$this->_countries_name[] = 'Jamaica';
			$this->_countries_name[] = 'Japan';
			$this->_countries_name[] = 'Jordan';
			$this->_countries_name[] = 'Kazakhstan';
			$this->_countries_name[] = 'Kenya';
			$this->_countries_name[] = 'Kiribati';
			$this->_countries_name[] = 'Korea North';
			$this->_countries_name[] = 'Korea South';
			$this->_countries_name[] = 'Kosovo';
			$this->_countries_name[] = 'Kuwait';
			$this->_countries_name[] = 'Kyrgyzstan';
			$this->_countries_name[] = 'Laos';
			$this->_countries_name[] = 'Latvia';
			$this->_countries_name[] = 'Lebanon';
			$this->_countries_name[] = 'Lesotho';
			$this->_countries_name[] = 'Liberia';
			$this->_countries_name[] = 'Libya';
			$this->_countries_name[] = 'Liechtenstein';
			$this->_countries_name[] = 'Lithuania';
			$this->_countries_name[] = 'Luxembourg';
			$this->_countries_name[] = 'Macedonia';
			$this->_countries_name[] = 'Madagascar';
			$this->_countries_name[] = 'Malawi';
			$this->_countries_name[] = 'Malaysia';
			$this->_countries_name[] = 'Maldives';
			$this->_countries_name[] = 'Mali';
			$this->_countries_name[] = 'Malta';
			$this->_countries_name[] = 'Montenegro';
			$this->_countries_name[] = 'Marshall Islands';
			$this->_countries_name[] = 'Mauritania';
			$this->_countries_name[] = 'Mauritius';
			$this->_countries_name[] = 'Mexico';
			$this->_countries_name[] = 'Micronesia';
			$this->_countries_name[] = 'Moldova';
			$this->_countries_name[] = 'Monaco';
			$this->_countries_name[] = 'Mongolia';
			$this->_countries_name[] = 'Morocco';
			$this->_countries_name[] = 'Mozambique';
			$this->_countries_name[] = 'Myanmar, {Burma}';
			$this->_countries_name[] = 'Namibia';
			$this->_countries_name[] = 'Nauru';
			$this->_countries_name[] = 'Nepal';
			$this->_countries_name[] = 'Netherlands';
			$this->_countries_name[] = 'New Zealand';
			$this->_countries_name[] = 'Nicaragua';
			$this->_countries_name[] = 'Niger';
			$this->_countries_name[] = 'Nigeria';
			$this->_countries_name[] = 'Norway';
			$this->_countries_name[] = 'Oman';
			$this->_countries_name[] = 'Pakistan';
			$this->_countries_name[] = 'Palau';
			$this->_countries_name[] = 'Panama';
			$this->_countries_name[] = 'Papua New Guinea';
			$this->_countries_name[] = 'Paraguay';
			$this->_countries_name[] = 'Peru';
			$this->_countries_name[] = 'Philippines';
			$this->_countries_name[] = 'Poland';
			$this->_countries_name[] = 'Portugal';
			$this->_countries_name[] = 'Qatar';
			$this->_countries_name[] = 'Romania';
			$this->_countries_name[] = 'Russian Federation';
			$this->_countries_name[] = 'Rwanda';
			$this->_countries_name[] = 'St Kitts & Nevis';
			$this->_countries_name[] = 'St Lucia';
			$this->_countries_name[] = 'St Vincent & the Grenadines';
			$this->_countries_name[] = 'Samoa';
			$this->_countries_name[] = 'San Marino';
			$this->_countries_name[] = 'Sao Tome & Principe';
			$this->_countries_name[] = 'Saudi Arabia';
			$this->_countries_name[] = 'Senegal';
			$this->_countries_name[] = 'Serbia';
			$this->_countries_name[] = 'Seychelles';
			$this->_countries_name[] = 'Sierra Leone';
			$this->_countries_name[] = 'Singapore';
			$this->_countries_name[] = 'Slovakia';
			$this->_countries_name[] = 'Slovenia';
			$this->_countries_name[] = 'Solomon Islands';
			$this->_countries_name[] = 'Somalia';
			$this->_countries_name[] = 'South Africa';
			$this->_countries_name[] = 'Spain';
			$this->_countries_name[] = 'Sri Lanka';
			$this->_countries_name[] = 'Sudan';
			$this->_countries_name[] = 'Suriname';
			$this->_countries_name[] = 'Swaziland';
			$this->_countries_name[] = 'Sweden';
			$this->_countries_name[] = 'Switzerland';
			$this->_countries_name[] = 'Syria';
			$this->_countries_name[] = 'Taiwan';
			$this->_countries_name[] = 'Tajikistan';
			$this->_countries_name[] = 'Tanzania';
			$this->_countries_name[] = 'Thailand';
			$this->_countries_name[] = 'Togo';
			$this->_countries_name[] = 'Tonga';
			$this->_countries_name[] = 'Trinidad & Tobago';
			$this->_countries_name[] = 'Tunisia';
			$this->_countries_name[] = 'Turkey';
			$this->_countries_name[] = 'Turkmenistan';
			$this->_countries_name[] = 'Tuvalu';
			$this->_countries_name[] = 'Uganda';
			$this->_countries_name[] = 'Ukraine';
			$this->_countries_name[] = 'United Arab Emirates';
			$this->_countries_name[] = 'United Kingdom';
			$this->_countries_name[] = 'United States';
			$this->_countries_name[] = 'Uruguay';
			$this->_countries_name[] = 'Uzbekistan';
			$this->_countries_name[] = 'Vanuatu';
			$this->_countries_name[] = 'Vatican City';
			$this->_countries_name[] = 'Venezuela';
			$this->_countries_name[] = 'Vietnam';
			$this->_countries_name[] = 'Yemen';
			$this->_countries_name[] = 'Zambia';
			$this->_countries_name[] = 'Zimbabwe';
		}
		public function name($index) {
			if (($index >= 0) && ($index < $this->country_count()))
				return $this->_countries_name[$index];
			else return false;
		}
		public function country_count() {
			return count($this->_countries_name);
		}
	}
	class Address_Messages {
		const ERROR_INCOMPLETE = 1;
		
		public static function toString($code) {
			$string = NULL;
			for ($i = 1; $i <= (Address_Messages::ERROR_INCOMPLETE); $i *= 2) {
				if ($i & $code) {
					if ($string !== NULL) {
						$string .= '<br />';
					}
					$string .= '- ';
					switch ($i) {
						case Address_Messages::ERROR_INCOMPLETE:
							$string .= 'You did not fill in all the required information';//constants\descriptive\TEXT_CUST_SETTINGS_ERROR_NO_OLD_PW;
							break;
					}
				}
			}
			return $string;
		}
	}
	class Address {
		public $id;
		public $street_number;
		public $street;
		public $suite_number;
		public $city;
		public $state;
		public $zip;
		public $country;
		public $error;
		
		public $indent;
		
		// CLASS CONSTANTS
		const TF_STREET_NUMBER 	= 'TF_STREET_NUMBER';
		const TF_STREET 		= 'TF_STREET';
		const TF_SUITE_NUMBER 	= 'TF_SUITE_NUMBER';
		const TF_CITY 			= 'TF_CITY';
		const TF_STATE 			= 'TF_STATE';
		const TF_ZIP 			= 'TF_ZIP';
		const TF_COUNTRY 		= 'TF_COUNTRY';
		//const ERROR_INCOMPLETE 	= 1;
		public function __construct() {
			$this->id 				= NULL;
			$this->street_number 	= NULL;
			$this->street 			= NULL;
			$this->suite_number 	= NULL;
			$this->city 			= NULL;
			$this->state 			= NULL;
			$this->zip				= NULL;
			$this->country 		= NULL;
			$this->_from_post();
			$this->error = false;
		}
		public function from_db(\db\Login $db_login, $address_id) {
			$select = new \db\Select();
			$select->set_DB_login($db_login);
			$select->add_val($db_login->get_primary_key(), $address_id);
			$result = $select->query();
			if ($result !== false) {
				while ($row = $result->fetch_row() ) {
					$this->id 				= $row[0];
					$this->street_number 	= $row[1];
					$this->street 			= $row[2];
					$this->suite_number 	= $row[3];
					$this->city 			= $row[4];
					$this->state 			= $row[5];
					$this->zip 				= $row[6];
					$this->country 			= $row[7];
				}
				if ($this->suite_number < 1) $this->suite_number = NULL;
				return true;
			} else return false;
		}
		protected function _from_post() {
			if (!empty($_POST[$this::TF_STREET_NUMBER]))
				$this->street_number = $_POST[$this::TF_STREET_NUMBER];
			if (!empty($_POST[$this::TF_STREET]))
				$this->street = $_POST[$this::TF_STREET];
			if (!empty($_POST[$this::TF_SUITE_NUMBER]))
				$this->suite_number = $_POST[$this::TF_SUITE_NUMBER];
			if (!empty($_POST[$this::TF_CITY]))
				$this->city = $_POST[$this::TF_CITY];
			if (!empty($_POST[$this::TF_STATE]))
				$this->state = $_POST[$this::TF_STATE];
			if (!empty($_POST[$this::TF_ZIP]))
				$this->zip	= $_POST[$this::TF_ZIP];
			if (!empty($_POST[$this::TF_COUNTRY]))
				$this->country	= $_POST[$this::TF_COUNTRY];
		}
		protected function errors() {
			if (($this->street_number !== NULL) &&
				($this->street !== NULL) &&
				($this->city !== NULL) &&
				($this->state !== NULL) &&
				($this->zip !== NULL) &&
				($this->country !== NULL) ) {
					// to do: write more error checks
				$this->error = false;
				return false;
			} else {
				$this->error = Address_Messages::ERROR_INCOMPLETE;
			} return $this->error; // return an error code here
		}
		/*public function display_error_message() {
			switch($this->error) {
				case Address_Messages::ERROR_INCOMPLETE:
					return 'You did not fill in all the required information';
					break;
			}
		}*/
		// commits the data associated with the instance of the Address class to the database defined by db_login and returns the primary key associated with the insert
		public function to_db(\db\Login $db_login) {
			$errors = $this->errors();
			if ( $errors === false ) {
				$col_names = $db_login->get_column_names();
				$insert = new \db\Insert();
				$insert->set_db_close(false);
				$insert->set_DB_login($db_login);
				$insert->add_val($col_names[0], 0);
				$insert->add_val($col_names[1], $this->street_number);
				$insert->add_val($col_names[2], $this->street);
				$insert->add_val($col_names[3], $this->suite_number);
				$insert->add_val($col_names[4], $this->city);
				$insert->add_val($col_names[5], $this->state);
				$insert->add_val($col_names[6], $this->zip);
				$insert->add_val($col_names[7], $this->country);
				
				$result = $insert->query();
				if ($result !== false) {
					$this->id = $insert->get_primary_key_last_insert();
					
				} else return false;
				$insert->close();
				return $this->id;
			} else return false;
		}
		public function delete_from_db(\db\Login $db_login) {
			$delete = new \db\Delete();
			$delete->set_DB_login($db_login);
			$delete->add_val($db_login->get_primary_key(), $this->id);
			return $delete->query();
		}
		protected function _render_open_tag() {
			if (!empty($this->title)) {
				$title = ' title="'.$this->title.'"';
			} else $title = NULL;
			return '<div'.$title.' class="address">';
		}
		protected function _render_german_address() {
			$indent = $this->indent;
			if ($this->suite_number !== NULL) {
				$suite = ' // W '.$this->suite_number;
			} else $suite = NULL;
			$out = NULL;
			$address_1 = $this->street.' '.$this->street_number.$suite.'<br />';
			$address_2 = $this->zip.' '.$this->city.'<br />';
			$address_3 = $this->country;
			
			if ( ($address_1 !== NULL ) || ($address_2 !== NULL ) || ($address_3 !== NULL ) ) {
				$out .= \html\row($this->_render_open_tag(), $indent++);
				if ($address_1 !== NULL ) $out .= \html\row($address_1, $indent);
				if ($address_2 !== NULL ) $out .= \html\row($address_2, $indent);
				if ($address_3 !== NULL ) $out .= \html\row($address_3, $indent);
				$out .= \html\row('</div>', --$indent);
			}
			return $out;
		}
		protected function _render_us_address() {
			$indent = $this->indent;
			if ($this->suite_number !== NULL) {
				$suite = ' suite '.$this->suite_number;
			} else $suite = NULL;
			$out = NULL;
			$address_1 = $this->street_number.' '.$this->street.$suite.'<br />';
			$address_2 = $this->city.' '.$this->state.', '.$this->zip.'<br />';
			$address_3 = $this->country;
			
			if ( ($address_1 !== NULL ) || ($address_2 !== NULL ) || ($address_3 !== NULL ) ) {
				$out .= \html\row($this->_render_open_tag(), $indent++);
				if ($address_1 !== NULL ) $out .= \html\row($address_1, $indent);
				if ($address_2 !== NULL ) $out .= \html\row($address_2, $indent);
				if ($address_3 !== NULL ) $out .= \html\row($address_3, $indent);
				$out .= \html\row('</div>', --$indent);
			}
			return $out;
		}
		public function render_HTML() {
			switch ($this->country) {
				case 'Germany':
					return $this->_render_german_address();
				
				case 'United States':
					return $this->_render_us_address();
					
				default:
					return $this->_render_us_address();
			}
		}
		/*
		public function render_input_form() {
			$form = new \html\Form();
			$form->indent = $this->indent;
			$form->submit_caption = constants\descriptive\TEXT_SUBMIT_CAPTION;
		
			// adding text fields to the form	
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_STREET_NUMBER;
			$tf->class			= 'txtf';
			$tf->size			= 6;
			$tf->maxlength		= 10;
			$tf->group = true;
			$tf->label			= constants\descriptive\TEXT_STREET_NUMBER;
			if ($this->error) $tf->default_value 	= $this->street_number;
			$form->add_element($tf);
			
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_STREET;
			$tf->class			= 'txtf';
			$tf->size			= 36;
			$tf->maxlength		= 255;
			$tf->group = true;
			$tf->label 		= constants\descriptive\TEXT_STREET;
			if ($this->error) $tf->default_value 	= $this->street;
			$form->add_element($tf);
			
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_SUITE_NUMBER;
			$tf->class			= 'txtf';
			$tf->size			= 6;
			$tf->maxlength		= 10;
			$tf->group = true;
			$tf->label 		= constants\descriptive\TEXT_SUITE_NUMBER;
			if ($this->error) $tf->default_value 	= $this->suite_number;
			$form->add_element($tf);
			
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_CITY;
			$tf->class			= 'txtf';
			$tf->size			= 20;
			$tf->maxlength		= 255;
			$tf->group = true;
			$tf->label 		= constants\descriptive\TEXT_CITY;
			if ($this->error) $tf->default_value 	= $this->city;
			$form->add_element($tf);
			
			$select = new \html\form\Select();
			$select->name = $this::TF_STATE;
			$select->label = constants\descriptive\TEXT_STATE;
			$select->group = true;
			if ($this->error) $select->selected = $this->state;
			else $select->selected = constants\site\ADDRESS_STATE_DEFAULT;
			
			$states = new \lib\US_States();
			$select->add(new \html\form\Select_Option('--', '--'));
			for ($i = 0; $i < $states->state_count(); $i++) {
				$select->add(new \html\form\Select_Option($states->abbr($i), $states->name($i)));
			}
			$form->add_element($select);
			
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_ZIP;
			$tf->class			= 'txtf';
			$tf->size			= 10;
			$tf->maxlength		= 10;
			$tf->group = true;
			$tf->label 		= constants\descriptive\TEXT_ZIP;
			if ($this->error) $tf->default_value 	= $this->zip;
			$form->add_element($tf);
			
			$select = new \html\form\Select();
			$select->name = $this::TF_COUNTRY;
			$select->group = true;
			$select->label = constants\descriptive\TEXT_COUNTRY;
			if ($this->error) $select->selected = $this->country;
			else $select->selected = constants\site\ADDRESS_COUNTRY_DEFAULT;
			
			$countries = new \lib\Countries();
			$select->add(new \html\form\Select_Option('--', '--'));
			for ($i = 0; $i < $countries->country_count(); $i++) {
				$select->add(new \html\form\Select_Option($countries->name($i), $countries->name($i)));
			}
			$form->add_element($select);
			
			return $form;
		}
		*/
		public function render_input_form() {
			$indent = 5;
			$address = new \ordit\Information_Window();
			$address->header = 'Add a Delivery Address';//constants\descriptive\TEXT_CUST_SETTINGS_GENERAL_INF_H1;
			$address->notification_decoder_class = '\\lib\\Address_Messages';
			$address->notifications = $this->errors(); // not functional
			
			
			// adding text fields to the form	
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_STREET_NUMBER;
			$tf->class			= 'txtf';
			$tf->size			= 30;
			$tf->maxlength		= 10;
			$tf->group = true;
			if ($this->error) $tf->default_value 	= $this->street_number;
			$address->add_content_edit(\lib\constants\descriptive\TEXT_STREET_NUMBER , $tf);
			
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_STREET;
			$tf->class			= 'txtf';
			$tf->size			= 30;
			$tf->maxlength		= 255;
			if ($this->error) $tf->default_value 	= $this->street;
			$address->add_content_edit(\lib\constants\descriptive\TEXT_STREET , $tf);
			
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_SUITE_NUMBER;
			$tf->class			= 'txtf';
			$tf->size			= 30;
			$tf->maxlength		= 10;
			if ($this->error) $tf->default_value 	= $this->suite_number;
			$address->add_content_edit(constants\descriptive\TEXT_SUITE_NUMBER , $tf);
			
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_CITY;
			$tf->class			= 'txtf';
			$tf->size			= 30;
			$tf->maxlength		= 255;
			if ($this->error) $tf->default_value 	= $this->city;
			$address->add_content_edit(constants\descriptive\TEXT_CITY , $tf);
			
			$select = new \html\form\Select();
			$select->name = $this::TF_STATE;
			if ($this->error) $select->selected = $this->state;
			else $select->selected = constants\site\ADDRESS_STATE_DEFAULT;
			
			$states = new \lib\US_States();
			$select->add(new \html\form\Select_Option('--', '--'));
			for ($i = 0; $i < $states->state_count(); $i++) {
				$select->add(new \html\form\Select_Option($states->abbr($i), $states->name($i)));
			}
			$address->add_content_edit(constants\descriptive\TEXT_STATE , $select);
			
			$tf = new \html\form\Text_Field();
			$tf->name 			= $this::TF_ZIP;
			$tf->class			= 'txtf';
			$tf->size			= 30;
			$tf->maxlength		= 10;
			if ($this->error) $tf->default_value 	= $this->zip;
			$address->add_content_edit(constants\descriptive\TEXT_ZIP , $tf);
			
			$select = new \html\form\Select();
			$select->name = $this::TF_COUNTRY;
			if ($this->error) $select->selected = $this->country;
			else $select->selected = constants\site\ADDRESS_COUNTRY_DEFAULT;
			
			$countries = new \lib\Countries();
			$select->add(new \html\form\Select_Option('--', '--'));
			for ($i = 0; $i < $countries->country_count(); $i++) {
				$select->add(new \html\form\Select_Option($countries->name($i), $countries->name($i)));
			}
			$address->add_content_edit(constants\descriptive\TEXT_COUNTRY , $select);
			
			return \html\row($address->render_edit_contend(), $indent);
		}
	}
}
?>