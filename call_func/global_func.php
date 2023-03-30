<?php 
/**
 * Check input for existing only of digits (numbers)
 * @author Tim Boormans <info@directwebsolutions.nl>
 * @param $digit
 * @return bool
 */

function set_password($text){
	if(trim($text) !=""){
		return SHA1($text.SALT);
	}
	return '';
}

function is_digit($digit) {
	if(is_int($digit)) {
		return true;
	} elseif(is_string($digit)) {
		return ctype_digit($digit);
	} else {
		// booleans, floats and others
		return false;
	}
}


function array_html($str){ // ARRAY TYPE avoid HTML tags --> plain text
	$flags=ENT_QUOTES;
	$encoding ="UTF-8";
	if(!(is_null($str)) AND is_array($str)){
		foreach ($str as $index=>$value){
			if(empty($value)) { continue; }
			$temp = js_clean($value);
			$str[$index] = htmlspecialchars($temp, $flags, $encoding);
		}	
	}
	return $str;
}

function js_clean_array($array){ // ARRAY TYPE avoid JS attack --> text editor
	$return =array();
	if(!(empty($array)) AND is_array($array)){
		foreach ($array as $index=>$value){
			if(empty($value)) { continue; }
			$return[$index] = js_clean($value);
		}	
	}
	return $return;
}
function var_html($str){ // avoid HTML tags --> plain text
	$flags=ENT_QUOTES;
	$encoding ="UTF-8";
	$temp = js_clean($str);
	$str = htmlspecialchars($temp, $flags, $encoding);
	return $str;
}

function output($array){
	$json = json_encode($array, JSON_NUMERIC_CHECK );
	return $json;
}

function js_clean($data) { // avoid JS attack --> text editor
//$data = strip_tags($data);
//Remove Script tags
//$data = preg_replace('/<script>/', '$1>',$data);

// Remove any attribute starting with "on" or xmlns
$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

// Remove javascript: and vbscript: protocols
$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

// Remove namespaced elements (we do not need them)
$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

do
{
    // Remove really unwanted tags
    $old_data = $data;
    $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
}
while ($old_data !== $data);

// we are done...
return $data;
}
