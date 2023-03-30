<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;

if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
    include HTTP_404;
    exit();
}

$error_message = "";

$summary_id = isset($_POST['summary_id']) ? $_POST['summary_id'] : '';

$sql = mysqli_query($db_connect, "DELETE FROM `permanent_sched_detail` WHERE `parent_id` = '$summary_id'");

if(!$sql){
    $res = [
        'status' => 400,
        'message' => "Schedule notn deleted"
    ];
    echo json_encode($res);
}

$sql_1 = mysqli_query($db_connect, "DELETE FROM `permanent_sched_summary` WHERE `id` = '$summary_id'");

if ($error_message == '') {
    $res = [
        'status' => 400,
        'message' => "Schedule has been deleted"
    ];
    echo json_encode($res);
}
exit();
