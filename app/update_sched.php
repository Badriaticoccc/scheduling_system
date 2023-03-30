<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;

function military_time($data)
{
    $military_time = preg_match('/^([01][0-9]|2[0-3]):([0-5][0-9])$/', $data);
    if (!$military_time) {
        return false;
    }
    return true;
}

$result_msg = [];
$result_msg['msg'] = '';

foreach ($_POST as $key => $value) {
    $_POST[$key] = trim($value);
}
$day_data = array('M', 'T', 'W', 'TH', 'F', 'S', 'SU');

$faculty_id = isset($_POST['faculty_id']) ? $_POST['faculty_id'] : '';
$teacher = isset($_POST['teacher']) ? $_POST['teacher'] : '';
$course_code = isset($_POST['course_code']) ? $_POST['course_code'] : '';
$course_title = isset($_POST['course_title']) ? $_POST['course_title'] : '';
$section = isset($_POST['section']) ? $_POST['section'] : '';
$day = isset($_POST['day']) ? strtoupper($_POST['day']) : '';
$from_time = isset($_POST['from_time']) ? $_POST['from_time'] : '';
$to_time = isset($_POST['to_time']) ? $_POST['to_time'] : '';

if ($faculty_id == '' || $teacher == '' || $course_code == '' || $course_title == '' || $section == '' || $day == '' || $from_time == '' || $to_time == '') {
    $result_msg['msg'] = "Please fill all blanks.";
    $result_msg['result'] = "error";
    echo json_encode($result_msg);
    exit();
}

$sql = mysqli_query($db_connect, "SELECT * FROM faculty_sched WHERE faculty_id = '" . $faculty_id . "' AND teacher = '" . $teacher . "'  AND course_code = '" . $course_code . "'  AND course_title = '" . $course_title . "' AND section = '" . $section . "' AND day = '" . $day . "' AND from_time = '" . $from_time . "' AND to_time = '" . $to_time . "'");
if (mysqli_num_rows($sql) > 0) {
    $result_msg['msg'] = "No Data has been changed.";
    $result_msg['result'] = "error";
    echo json_encode($result_msg);
    exit();
}

if(!in_array(strtoupper($day),$day_data)) {
    $result_msg['msg'] = "Invalid Day.";
    $result_msg['result'] = "error";
    echo json_encode($result_msg);
    exit();
}

$validate_from_time = military_time($from_time);

if (!$validate_from_time) {
    $result_msg['msg'] = "From Time is Invalid.";
    $result_msg['result'] = "error";
    echo json_encode($result_msg);
    exit();
}

$validate_to_time = military_time($to_time);

if (!$validate_to_time) {
    $result_msg['msg'] = "To Time is Invalid.";
    $result_msg['result'] = "error";
    echo json_encode($result_msg);
    exit();
}

if ($from_time > $to_time) {
    $result_msg['msg'] = "From Time is cannot be greater than To Time.";
    $result_msg['result'] = "error";
    echo json_encode($result_msg);
    exit();
}


mysqli_query($db_connect, "UPDATE faculty_sched SET course_code = '" . $course_code . "', course_title = '" . $course_title . "', section = '" . $section . "', day = '" . $day . "', from_time = '" . $from_time . "', to_time = '" . $to_time . "' WHERE faculty_id = '" . $faculty_id . "' AND teacher = '" . $teacher . "'");
$result_msg['msg'] = "Successfully Updated.";
$result_msg['result'] = "success";
echo json_encode($result_msg);
exit();