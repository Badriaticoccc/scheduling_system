<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;


foreach ($_POST as $key => $value) {
    $_POST[$key] = trim($value);
}

$is_digit = '/^[0-9]+$/';

$faculty_id = isset($_POST['faculty_id']) ? $_POST['faculty_id'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$department = isset($_POST['department']) ? $_POST['department'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';
$global_id = isset($_POST['global_id']) ? $_POST['global_id'] : 0;

$result_msg = [];
$result_msg['msg'] = '';
// if ($faculty_id == '') {
//     $result_msg['msg'] = "Faculty ID is required!";
//     $result_msg['result'] = "error";
//     echo json_encode($result_msg);
//     exit();
// }

if($faculty_id == '' || $name == '' || $department == '' || $email == '' || $status == '') {
    $result_msg['msg'] = "Please fill all blanks!";
    $result_msg['result'] = "error";
    echo json_encode($result_msg);
    exit();
}

if (empty($name)) {
    $result_msg['msg'] = "Faculty ID is required!";
    $result_msg['result'] = "error";
    echo json_encode($result_msg);
    exit();
}

//email validate
//is_digit($global_id)
//do all validation

$faculty_id = mysqli_real_escape_string($db_connect, $_POST['faculty_id']);
$name = mysqli_real_escape_string($db_connect, $_POST['name']);
$department = mysqli_real_escape_string($db_connect, $_POST['department']);
$email = mysqli_real_escape_string($db_connect, $_POST['email']);
$status = mysqli_real_escape_string($db_connect, $_POST['status']);
$user_role = "Faculty";





//select professor via global id  exist

if ($global_id == 0) { // add faculty

    $facultyid_sql = mysqli_query($db_connect, "SELECT faculty_id FROM user_account WHERE faculty_id = '" . $faculty_id . "'");
    if (mysqli_num_rows($facultyid_sql) > 0) {
        $result_msg['msg'] = "Faculty ID Already Exist!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }
    $email_sql = mysqli_query($db_connect, "SELECT email FROM user_account WHERE email = '" . $email . "'");

    if (mysqli_num_rows($email_sql) > 0) {
        $result_msg['msg'] = "Email Already Exist!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result_msg['msg'] = "Email Invalid Format!";
        $result_msg['result'] = "warning";
        echo json_encode($result_msg);
        exit();
    }
    if ($result_msg['msg'] == '') {
        $insert_sql = mysqli_query($db_connect, "INSERT INTO user_account (faculty_id,faculty_name,department,email,status,user_type,username,id_no) VALUES ('" . $faculty_id . "','" . $name . "','" . $department . "','" . $email . "','" . $status . "','".$user_role."','".$faculty_id."','".$faculty_id."')");
        if ($insert_sql) {
            //number insert row;
            $result_msg['msg'] = "";
            $result_msg['result'] = "success";
            echo json_encode($result_msg);
            exit();
        }
    }
} else { //update faculty
    if ($global_id == 0) {
        $result_msg['msg'] = "Faculty ID is required!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }

    $query3 = mysqli_query($db_connect, "SELECT * FROM user_account WHERE email = '" . $email . "' AND faculty_id = '" . $faculty_id . "' AND department = '" . $department . "' AND faculty_name = '" . $name . "' AND status = '" . $status . "'");
    if (mysqli_num_rows($query3) > 0) {
        $result_msg['msg'] = "No data has been change!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }

    $facultyid_sql = mysqli_query($db_connect, "SELECT faculty_id FROM user_account WHERE id != '".$global_id."' AND faculty_id = '".$faculty_id."'"); //id!='useredit' and faculty_id=udset
    if(mysqli_num_rows($facultyid_sql) > 0) {
        $result_msg['msg'] = "Faculty ID Already Exist!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }

    $emailid_sql = mysqli_query($db_connect, "SELECT email FROM user_account WHERE id != '".$global_id."' AND email = '".$email."'"); //id!='useredit' and faculty_id=udset
    if(mysqli_num_rows($emailid_sql) > 0) {
        $result_msg['msg'] = "Email Already Exist!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }
    

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result_msg['msg'] = "Email Invalid Format!";
        $result_msg['result'] = "warning";
        echo json_encode($result_msg);
        exit();
    }


    if ($result_msg['msg'] == '') {
        $update_sql = mysqli_query($db_connect, "UPDATE user_account SET faculty_id = '" . $faculty_id . "', faculty_name = '" . $name . "', department ='" . $department . "',email = '" . $email . "',status = '" . $status . "' WHERE id = '" . $global_id . "'");
        if ($update_sql) {
            $result_msg['msg'] = "";
            $result_msg['result'] = "success";
            echo json_encode($result_msg);
            exit();
        }
    }

    # code...


}
