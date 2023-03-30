<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;

function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}


foreach ($_POST as $key => $value) {
    $_POST[$key] = trim($value);
}

$is_digit = '/^[0-9.-]*$/';

// $faculty_id = isset($_POST['user_type']) ? $_POST['user_type'] : '';

$faculty_id = isset($_POST['faculty_id']) ? $_POST['faculty_id'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$username = isset($_POST['username']) ? $_POST['username'] : '';
$user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$global_id = isset($_POST['global_id']) ? $_POST['global_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

$password = randomPassword();


// validations
$result_msg = [];

$result_msg['msg'] = '';
if ($faculty_id == '' || $name == '' || $username == '' || $user_type == '' || $email == '') {
    $result_msg['msg'] = "Please fill all fields!";
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

if ($action == "RESET") {
    // generate random pasword
    //iupdate yung faculty
    //ipadala sa user ang bago ng passwoird via json / modal
    $query = mysqli_query($db_connect, "UPDATE user_account SET password = '" . $password . "' WHERE id = '" . $global_id . "'");
    if ($query) {
        $result_msg['msg'] = $password;
        $result_msg['result'] = "reset";
        echo json_encode($result_msg);
        exit();
    }
}

//email validate
//is_digit($global_id)
//do all validation

$faculty_id = mysqli_real_escape_string($db_connect, $_POST['faculty_id']);
$user_type = mysqli_real_escape_string($db_connect, $_POST['user_type']);
$name = mysqli_real_escape_string($db_connect, $_POST['name']);
$username = mysqli_real_escape_string($db_connect, $_POST['username']);
$email = mysqli_real_escape_string($db_connect, $_POST['email']);

// $action

//select professor via global id  exist

if ($global_id == 0) { // add user account


    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $department = isset($_POST['department']) ? $_POST['department'] : '';

    $status = mysqli_real_escape_string($db_connect, $_POST['status']);
    $department = mysqli_real_escape_string($db_connect, $_POST['department']);

    if ($status == '' || $department == '') {
        $result_msg['msg'] = "Please fill all fields";
        $result_msg['result'] = "warning";
        echo json_encode($result_msg);
        exit();
    }



    if (!preg_match($is_digit, $faculty_id)) {
        $result_msg['msg'] = "Faculty ID not numerical";
        $result_msg['result'] = "warning";
        echo json_encode($result_msg);
        exit();
    }

    $duplicate_id = mysqli_query($db_connect, "SELECT faculty_id FROM user_account WHERE faculty_id = '" . $faculty_id . "'");
    if (mysqli_num_rows($duplicate_id) > 0) {
        $result_msg['msg'] = "Faculty ID Already Exist!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }

    $query1 = mysqli_query($db_connect, "SELECT email FROM user_account WHERE email = '" . $email . "'");
    if (mysqli_num_rows($query1) > 0) {
        $result_msg['msg'] = "The Email is existing!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result_msg['msg'] = "The email is not valid";
        $result_msg['result'] = "warning";
        echo json_encode($result_msg);
        exit();
    }

    if ($result_msg['msg'] == '') {
        $ecrp_password = set_password($password);
        $query = mysqli_query($db_connect, "INSERT INTO user_account (id_no,faculty_id,faculty_name,user_type,email,username,password,department,status) VALUES ('" . $username . "','" . $faculty_id . "','" . $name . "','" . $user_type . "','" . $email . "','" . $username . "','" . $ecrp_password . "','".$department."','".$status."')");
        if ($query) {
            //number insert row;
            $result_msg['msg'] = "Username: " . $username . " Password: " . $password;
            $result_msg['result'] = "add";
            echo json_encode($result_msg);
            exit();
        }
    }
} else { //edit button // update user account


    if ($global_id == 0) {
        $result_msg['msg'] = "Faculty ID is required!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }
    $duplicate_id = mysqli_query($db_connect, "SELECT faculty_id FROM user_account WHERE id !='" . $global_id . "' AND faculty_id = '" . $faculty_id . "'");
    if (mysqli_num_rows($duplicate_id) > 0) {
        $result_msg['msg'] = "Faculty ID Already Exist!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }
    $username_up = mysqli_query($db_connect, "SELECT faculty_id FROM user_account WHERE id !='" . $global_id . "' AND username = '" . $username . "'");
    if (mysqli_num_rows($username_up) > 0) {
        $result_msg['msg'] = "Username Already Exist!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }

    $query1 = mysqli_query($db_connect, "SELECT email FROM user_account WHERE id !='" . $global_id . "' AND email = '" . $email . "'");
    if (mysqli_num_rows($query1) > 0) {
        $result_msg['msg'] = "The Email is existing!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result_msg['msg'] = "The email is not valid";
        $result_msg['result'] = "warning";
        echo json_encode($result_msg);
        exit();
    }

    $query3 = mysqli_query($db_connect, "SELECT * FROM user_account WHERE email = '" . $email . "' AND faculty_id = '" . $faculty_id . "' AND id_no = '" . $username . "' AND faculty_name = '" . $name . "' AND user_type = '" . $user_type . "' AND username = '" . $username . "'");
    if (mysqli_num_rows($query3) > 0) {
        $result_msg['msg'] = "No Changes!";
        $result_msg['result'] = "error";
        echo json_encode($result_msg);
        exit();
    } else {
        # code...
        $query = mysqli_query($db_connect, "UPDATE user_account SET email = '" . $email . "', faculty_id = '" . $faculty_id . "', id_no = '" . $username . "', username ='" . $username . "',faculty_name = '" . $name . "',user_type = '" . $user_type . "' WHERE id = '" . $global_id . "'");
        if ($query) {
            $result_msg['msg'] = "";
            $result_msg['result'] = "success";
            echo json_encode($result_msg);
            exit();
        }
    }
}
