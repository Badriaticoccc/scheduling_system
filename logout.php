<?php
require 'config/config.php';
require CONNECT_PATH;
require GLOBAL_FUNC;
require CL_SESSION_PATH;

// $g_user_role = $session_class->getValue('role_id');
// $g_user_name = $session_class->getValue('name');

// $event   = isset($_GET['event']) ? "@".$_GET['event']: ''; 

// $location ="login.php";
// if($g_user_role=="ADMIN"  OR $g_user_role=="FACULTY"){
// 	user_log('LOGOUT');
// 	$location = BASE_URL."login.php";
// }

$session_class->end();

$location = BASE_URL."login.php";
header('Location: '.$location);
exit();

// header('Location: '.$location); 
// exit();
?>