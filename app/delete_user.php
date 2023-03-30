<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;

if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
    include HTTP_404;
    exit();
}

$error_message = "";

$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';

$sql = mysqli_query($db_connect, "UPDATE user_account SET active = 1 WHERE id = '" . $user_id . "'");


if ($error_message == '') {
    $res = [
        'status' => 400,
        'message' => "Faculty has been deleted"
    ];
    echo json_encode($res);
}
exit();
