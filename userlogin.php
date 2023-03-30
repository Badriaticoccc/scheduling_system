<?php
require 'config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;
require CL_SESSION_PATH;

$username = isset($_POST['u_name']) ? trim($_POST['u_name']) : '';
$password = isset($_POST['pass']) ? trim($_POST['pass']) : '';

$password = set_password($password);
function success()
{
    global $db_connect;
    global $session_class;
    global $username;
    global $password;
    $user = mysqli_query($db_connect, "SELECT * FROM user_account WHERE username = '" . $username . "' AND password = '" . $password . "' ");
    $row = mysqli_fetch_assoc($user);

    $photo  = empty(trim($row['photo'])) ? "" : BASE_URL . "images/user/" . $row['photo'];

    $response['success'] = "200";
    $response['title'] = 'Welcome!';
    $response['message'] = 'Login Succesfully!';

    $session_class->setValue('login', 'success');
    $session_class->setValue('user_id', $row['id']);
    $session_class->setValue('name', $row['faculty_name']);
    $session_class->setValue('role', $row['user_type']);
    $session_class->setValue('session_sched', '');
    $session_class->setValue('faculty_id', $row['faculty_id']);
    $session_class->setValue('photo',  $photo);
    $session_class->setValue('email', $row['email']);
    $session_class->setValue('username', $row['username']);
    $session_class->setValue('status', $row['status']);

    $user = mysqli_query($db_connect, "SELECT * FROM permanent_sched_summary ORDER BY id DESC LIMIT 1");

    if ($user) {
        # code..
        if ($row = mysqli_fetch_assoc($user)) {
            # code...
            if ($row['id']) {
                $session_class->setValue('session_sched', $row['id']);
                $session_class->setValue('session_title', $row['title']);
            }
        }
    }


    echo json_encode($response);
}

if ($username == "" || $password == "") {
    $response['success'] = "400";
    $response['title'] = "SOMETHING WENT WRONG!";
    $response['message'] = "Please Fill all Fields.";
    echo json_encode($response);
    exit;
}

$exist = mysqli_query($db_connect, "SELECT * FROM user_account WHERE username = '" . $username . "' ");
$row = mysqli_fetch_assoc($exist);
if (mysqli_num_rows($exist) > 0) {
    $id = $row['id'];
    $locked = mysqli_query($db_connect, "SELECT * FROM user_account WHERE username = '" . $username . "' AND password = '" . $password . "'");
    $lock = mysqli_fetch_assoc($locked);
    if (mysqli_num_rows($locked) > 0) {
        if ($lock['locked'] > 2) {
            $response['success'] = "400";
            $response['title'] = "Account Locked!";
            $response['message'] = "Your Account is Locked Please Contact the admin.";
            echo json_encode($response);
            exit;
        }
        if ($lock['locked'] < 3) {
            mysqli_query($db_connect, "UPDATE user_account set locked = '0' WHERE id = '" . $lock['id'] . "'");
            success();
        }
    } else {
        $id = $row['id'];
        $locked = $row['locked'] + 1;
        $user_is_locked = mysqli_query($db_connect, "SELECT * FROM user_account WHERE id = '" . $id . "' ");
        $user = mysqli_fetch_assoc($user_is_locked);
        if ($user['locked'] > 2) {
            $response['success'] = "400";
            $response['title'] = "Account Locked!";
            $response['message'] = "Your Account is Locked Please Contact the admin.";
            echo json_encode($response);
            exit;
        }
        if ($user['locked'] < 3) {
            $user_locked = mysqli_query($db_connect, "UPDATE user_account SET locked ='" . $locked . "' WHERE id = '" . $id . "'");
            $response['success'] = "400";
            $response['title'] = "Wrong Password!";
            $response['message'] = "Attemp " . $locked;
            echo json_encode($response);
            exit;
        }
    }
} else {
    $response['success'] = "400";
    $response['title'] = "SOMETHING WENT WRONG!";
    $response['message'] = "Invalid Username or Password.";
    echo json_encode($response);
    exit;
}
