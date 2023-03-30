<?php 

$login = $session_class->getValue('login');
$name = $session_class->getValue('name');
$role = $session_class->getValue('role');
$user_id = $session_class->getValue('user_id');
$session_sched = $session_class->getValue('session_sched');
$session_title = $session_class->getValue('session_title');
$faculty_id = $session_class->getValue('faculty_id');
$g_email = $session_class->getValue('email');
$g_username = $session_class->getValue('username');
$g_type = $session_class->getValue('status');
define('user_id',$user_id);




if(!defined('user_id')){
    define('user_id',$user_id);
}


if($login != "success"){
	header('Location: login.php');
	exit();
}

$global_profile_pic = $session_class->getValue('photo');
if (!isset($global_profile_pic) OR empty($global_profile_pic)) {
	$table_pic = "";
	$tb_id="";
	if($role == "Admin"){ 
		$table_pic = "user_account";
		$tb_id="id";
	}
	$get_path = get_profile_pic($user_id,$tb_id,$table_pic);
	//echo $get_path."SS";
	if(empty(trim($get_path))){
		$get_path = BASE_URL."images/default_photo.jpg";
	}
	$session_class->setValue('photo',$get_path);
    $global_profile_pic = $get_path;
}

