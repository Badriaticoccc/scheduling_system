<?php
require '../config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;
require CL_SESSION_PATH;
require ISLOGIN;

if (isset($_POST['update_profile'])) {
    if (isset($_FILES['profile-pic'])) {
        $img_name = $_FILES['profile-pic']['name'];
        $img_size = $_FILES['profile-pic']['size'];
        $tmp_name = $_FILES['profile-pic']['tmp_name'];
        $error = $_FILES['profile-pic']['error'];


        if ($error === 0) {
            if ($img_size > 625000) {
                $res = [
                    'status' => 400,
                    'msg' => "Sorry, your file is too large."
                ];
                echo json_encode($res);
                return;
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);
                $allowed_exs = array("jpg", "jpeg", "png");

                if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name = "IMG-" . date("Y.m.d") . " - " . date("h.i.sa.") . $img_ex_lc;
                    $img_upload_path = "../images/user/" . $new_img_name;

                    move_uploaded_file($tmp_name, $img_upload_path);

                    $location = BASE_URL . "images/user/" . $new_img_name;
                    $query = "UPDATE `user_account` SET `photo`='$new_img_name' WHERE `id` = '$user_id'";
                    $query_run = mysqli_query($db_connect, $query);
                    if ($query_run) {
                        $session_class->setValue('photo', $location);
                        $res = [
                            'status' => 200,
                            'msg' => 'Profile Updated!',
                            'profile' => $location,
                        ];
                        echo json_encode($res);
                        return;
                    }
                } else {

                    $res = [
                        'status' => 400,
                        'msg' => "You can't upload files of this type."
                    ];
                    echo json_encode($res);
                    return;
                }
            }
        }
    }
}

if (isset($_POST['update_password'])) {

    $cur_pass = set_password($_POST['cur_password']);
    $new_pass = set_password($_POST['new_password']);
    $con_pass = set_password($_POST['con_password']);

    if ($cur_pass == '' || $new_pass == '' || $con_pass == '') {
        $res = [
            'status' => 400,
            'msg' => "All fields is required"
        ];
        echo json_encode($res);
        return;
    }

    $exist = mysqli_query($db_connect, "SELECT * FROM `user_account` WHERE `id` = '$user_id' ");
    $row = mysqli_fetch_assoc($exist);


    if ($cur_pass == $new_pass and $cur_pass == $con_pass) {
        //same cur and new pass
        $res = [
            'status' => 400,
            'msg' => "New password is same as the current password."
        ];
        echo json_encode($res);
        return;
    }

    if ($row['password'] != $cur_pass) {
        //wrong password
        $res = [
            'status' => 400,
            'msg' => "Wrong current password"
        ];
        echo json_encode($res);
        return;
    }

    if ($new_pass != $con_pass) {
        //unmatch password
        $res = [
            'status' => 400,
            'msg' => "New password and Confirm new password does not match"
        ];
        echo json_encode($res);
        return;
    }

    $update = mysqli_query($db_connect, "UPDATE `user_account` SET `password`='$new_pass' WHERE `id` = '$user_id'");
    
    if ($update) {
        //return
        $res = [
            'status' => 200,
            'msg' => "Update password successfully"
        ];
        echo json_encode($res);
        return;
    }
}

if(isset($_POST['update_info'])){

    $faculty_id = $_POST['faculty_id'];
    $full_name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $usertype = $_POST['usertype'];

    if($faculty_id == '' || $full_name == '' || $email == '' || $username == '' || $usertype == ''){
        $res = [
            'status' => 400,
            'msg' => "Please fill all fields."
        ];
        echo json_encode($res);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $res = [
            'status' => 400,
            'msg' => "The email is not valid."
        ];
        echo json_encode($res);
        return;
    }

    $sql = mysqli_query($db_connect,"SELECT * FROM user_account WHERE email = '$email' AND faculty_id != '$faculty_id'");
    if(mysqli_num_rows($sql) > 0){
        $res = [
            'status' => 400,
            'msg' => "Email is already used."
        ];
        echo json_encode($res);
        return;
    }

    $sql = mysqli_query($db_connect,"SELECT * FROM user_account WHERE username = '$username' AND faculty_id != '$faculty_id'");
    if(mysqli_num_rows($sql) > 0){
        $res = [
            'status' => 400,
            'msg' => "Username is already used."
        ];
        echo json_encode($res);
        return;
    }
    $sql = mysqli_query($db_connect,"SELECT * FROM user_account WHERE email = '$email' AND faculty_name = '$full_name' AND  username = '$username'");
    if(mysqli_num_rows($sql) > 0){
        $res = [
            'status' => 400,
            'msg' => "No changes found."
        ];
        echo json_encode($res);
        return;
    }

    mysqli_query($db_connect, "UPDATE user_account SET email = '$email', username = '$username', faculty_name = '$full_name' WHERE faculty_id = '$faculty_id'");
    $session_class->setValue('name', $full_name);
    $session_class->setValue('email', $email);
    $session_class->setValue('username', $username);
    $res = [
        'status' => 200,
        'msg' => "Success."
    ];
    echo json_encode($res);
    return;
   
}