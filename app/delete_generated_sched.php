<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;

if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) and strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
    include HTTP_404;
    exit();
}

$error_message = "";

$faculty_id = isset($_POST['faculty_id']) ? $_POST['faculty_id'] : '';

$sql = mysqli_query($db_connect, "UPDATE faculty_sched SET active = 1 WHERE id = '" . $faculty_id . "'");


if ($error_message == '') {
    $res = [
        'status' => 400,
        'message' => "Shedule has been deleted"
    ];
    echo json_encode($res);
}
exit();
