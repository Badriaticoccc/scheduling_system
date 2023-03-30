<?php
defined('DOMAIN_PATH') || define('DOMAIN_PATH', dirname(__DIR__));
include DOMAIN_PATH . '/config/db_data.php';
$db_connect = mysqli_connect(HOST, DB_USER, DB_PASS, DB_NAME);

if (mysqli_connect_errno()) {
	$error = "Failed to connect to Database: " . mysqli_connect_error();
	error_log($error);
	echo $error;
	exit();
}

function escape($con = "", $str = "")
{
	global $db_connect;
	$string = mysqli_real_escape_string($db_connect, $str);
	return $string;
}

function Schedule($selected_time, $time=0)
{
	$add_time = "+" . $time . "minutes";

	$selectedTime = $selected_time;

	$selectedTime = strtotime("$add_time", strtotime($selectedTime));

	return date('H:i:s', $selectedTime);
}

function Schedule_datetime($selected_time, $time=0)
{
	$add_time = "+" . $time . "minutes";

	$selectedTime = strtotime("$add_time", strtotime($selected_time));

	return $selectedTime;
}

function get_profile_pic($user_id, $field, $table)
{
	global $db_connect;
	$path = "";
	if (trim($user_id) == "" ||  trim($field) == "" || trim($table) == "") {
		return "";
	}
	$query = "SELECT photo FROM " . $table . " WHERE " . $field . " = '" . $user_id . "' LIMIT 1";
	if ($query = mysqli_query($db_connect, $query)) {
		$num = mysqli_num_rows($query);
		if ($num != 0) {
			if ($data = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
				$path = empty($data['photo']) ? "" : BASE_URL . $data['photo'];
			}
		}
	}

	return $path;
}


function get_room_type()
{
	global $db_connect;

	$array = array();
	if ($query = mysqli_query($db_connect, "SELECT * FROM `room_type`")) {
		while ($data = mysqli_fetch_assoc($query)) {
			array_push($array, $data['type']);
		}
	}
	return $array;
}

define('ROOM_TYPE',  get_room_type());//DATABASE
