<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

$result_msg = [];
$result_msg['msg'] = '';
$newpass = randomPassword();
$global_id = $_POST['user_id'];

$encrp_pass = set_password($newpass);

$query = mysqli_query($db_connect, "UPDATE user_account SET password = '" . $encrp_pass . "', locked = '0' WHERE id = '" . $global_id . "'");
if ($query) {
    $result_msg['msg'] = $newpass;
    $result_msg['result'] = "success";
    echo json_encode($result_msg);
    exit();
}
?>