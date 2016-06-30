<?php 
namespace aae\html {	
	class HTML {

		
		// updates a $_GET variable. if the seccond parameter is left blank, the variable will be unset
		public static function updateGetVar($var_name, $var_val = NULL, $url = NULL) {
			if ($url === NULL) {
				$url = $_SERVER['REQUEST_URI'];
			}
			$var_exists = false;
			$url_array = explode('&', $url);
			for ($i = 0; $i < count($url_array); $i++) {
				$var = explode('=', $url_array[$i]);
				if ($var[0] == $var_name) {
					$var_exists = true;
					if (count($var) > 1) {
						$var[1] = $var_val;
						if ($var_val === NULL) {
							$last_index = count($url_array)-1;
							for ($a = $i; $a < $last_index; $a++) {
								$url_array[$a] = $url_array[$a + 1];
							}
							unset($url_array[$last_index]);
							unset($var);
						}
					} else {
						$var[] = $var_val;
					}
					if (!empty($var))
						$url_array[$i] = implode('=', $var);
				}
			}
			$url = implode('&', $url_array);
			if ($var_exists === false) {
				$url_array_qm = explode('?', $url);
				for ($n = 0; $n < count($url_array_qm); $n++) {
					$url_array = explode('&', $url_array_qm[$n]);
					for ($i = 0; $i < count($url_array); $i++) {
						$var = explode('=', $url_array[$i]);
						if ($var[0] == $var_name) {
							$var_exists = true;
							if (count($var) > 1) {
								$var[1] = $var_val;
							} else {
								$var[] = $var_val;
							}
							$url_array[$i] = implode('=', $var);
						}
					}
					$url_array_qm[$n] = implode('&', $url_array);
				}
				$url = implode('?', $url_array_qm);
			}
			if ( ($var_exists === false) && ($var_val !== NULL)) {
				$url .= '&'.$var_name.'='.$var_val;
			}
			return $url;
		}
	}
}