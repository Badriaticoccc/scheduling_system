<?php
require 'config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;
require CL_SESSION_PATH;
include ISLOGIN;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    include DOMAIN_PATH . "/app/global/meta_data.php";
    include DOMAIN_PATH . "/app/global/include_top.php";
    ?>

    <style>
        /* The message box is shown when the user clicks on the password field */
        #message {
            background: #f1f1f1;
            color: #000;
            position: relative;
            padding: 20px;
            margin-top: 10px;
        }

        #message col p {
            padding: 10px 35px;
            font-size: 18px;
        }

        /* Add a green text color and a checkmark when the requirements are right */
        .valid {
            color: green;
        }

        .valid:before {
            position: relative;
            left: -10px;
            content: "✔";
        }

        /* Add a red text color and an "x" when the requirements are wrong */
        .invalid {
            color: red;
        }

        .invalid:before {
            position: relative;
            left: -10px;
            content: "✖";
        }
    </style>
</head>


<body class="loading" data-layout-config='{"leftSideBarTheme":"light","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
    <div class="page"></div>
    <div id="loading"></div>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- SIDE VAR -->
        <?php
        include DOMAIN_PATH . "/app/global/side_var.php";

        ?>
        <!-- END SIDE VAR -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <div class="content">

                <?php
                include DOMAIN_PATH . "/app/global/top_var.php";

                ?>

                <!-- Start Content-->
                <div class="container-fluid">

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">

                                <h4 class="page-title">Profile</h4>
                            </div>
                        </div>
                    </div>
                    <!-- start page title -->
                    <div class="main-body">

                        <div class="row gutters-sm">
                            <div class="col-md-4 mb-3">
                                <form id="profile-form" action="">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex flex-column align-items-center text-center">
                                                <img src="<?php echo $global_profile_pic ?>" alt="Admin" class="rounded-circle" width="150">
                                                <div class="mt-3">
                                                    <h4><?php echo $name ?></h4>
                                                    <p class="text-secondary mb-1"><?php echo $role; ?></p>

                                                    <div class="input-group mb-3">
                                                        <input type="file" class="form-control" name="profile-pic" required />
                                                        <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Update</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h4 class="mb-0">User Info</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Faculty Id</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary" id="faculty_id_profile">
                                                <?php echo $faculty_id; ?>

                                            </div>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Full Name</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary" id="full_name_profile">
                                                <?php echo ucwords(strtolower($name)); ?>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Email</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary" id="email_profile">
                                                <?php echo $g_email; ?>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Username</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary" id="username_profile">
                                                <?php echo $g_username; ?>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Usertype</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary" id="usertype_profile">
                                                <?php echo $g_type; ?>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div id="edit_profile">
                                                    <button class="btn bbtn btn-primary edit_profile">Edit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-3">
                                    <div class="card-header">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <h4 class="mb-0">Change Password</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form id="update_password_form">
                                            <div class="row mb-3">
                                                <label for="inputEmail3" class="col-sm-2 col-form-label">Current password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" class="form-control" id="cur_password" name="cur_password" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label for="inputPassword3" class="col-sm-2 col-form-label">New Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" class="form-control" id="new_password" name="new_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label for="inputPassword3" class="col-sm-2 col-form-label">Confirm Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" class="form-control" id="con_password" name="con_password" required>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <h5>Password must contain the following:</h5>
                                            </div>
                                            <div class="row">
                                                <div id="message">
                                                    <div class="col">
                                                        <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
                                                    </div>
                                                    <div class="col">
                                                        <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
                                                    </div>
                                                    <div class="col">
                                                        <p id="number" class="invalid">A <b>number</b></p>
                                                    </div>
                                                    <div class="col">
                                                        <p id="length" class="invalid">Minimum <b>8 characters</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- end page title -->

                    <!-- end row -->


                </div>
                <!-- container -->

            </div>
            <!-- content -->

            <!-- Footer Start -->
            <?php include DOMAIN_PATH . "/call_func/footer.php"; ?>
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <?php include DOMAIN_PATH . "/app/global/include_buttom.php";
    include DOMAIN_PATH . "/app/set_sched.php";

    ?>
</body>

</html>

<script>
    $(document).ready(function() {
        $(document).on('click', '#edit_profile', function() {
            $('#faculty_id_profile').html('<input type="text" id="form_faculty_id" class="form-control" value = "<?php echo $faculty_id ?>" readonly>');
            $('#full_name_profile').html('<input type="text" id="form_name" class="form-control" value = "<?php echo $name ?>" required>');
            $('#email_profile').html('<input type="text" id="form_email" class="form-control" value = "<?php echo $g_email ?>" required>');
            $('#username_profile').html('<input type="text" id="form_username" class="form-control" value = "<?php echo $g_username ?>" required>');
            $('#usertype_profile').html('<input type="text" id="form_usertype" class="form-control" value = "<?php echo $g_type ?>" readonly>');
            $('#edit_profile').html('<button class="btn btn-primary" id="profile_update">Update</button>');

        });

        $(document).on('click', '#profile_update', function() {
            var faculty_id = $('#form_faculty_id').val();
            var name = $('#form_name').val();
            var email = $('#form_email').val();
            var username = $('#form_username').val();
            var usertype = $('#form_usertype').val();

            $.ajax({
                type: "POST",
                url: "ajax/profile_ajax.php",
                data: {
                    faculty_id: faculty_id,
                    name: name,
                    email: email,
                    username: username,
                    usertype: usertype,
                    update_info: true
                },
                success: function(response) {
                    var res = jQuery.parseJSON(response);
                    if (res.status == 200) {
                        Swal.fire({
                            title: "Success!",
                            icon: "success",
                            button: false,
                            timer: 5000,
                        }).then(function() {
                            location.reload();
                        });

                    } else if (res.status == 400) {
                        Swal.fire({
                            title: "Warning!",
                            text: res.msg,
                            icon: "warning",
                            button: false,
                            timer: 5000,
                        })
                    }
                }
            });

        });



        // update profile
        $(document).on('submit', '#profile-form', function(e) {
            e.preventDefault();



            var formData = new FormData(this);
            formData.append("update_profile", true);

            $.ajax({
                type: "POST",
                url: "ajax/profile_ajax.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var res = jQuery.parseJSON(response);
                    if (res.status == 200) {
                        Swal.fire({
                            title: "Success!",
                            icon: "success",
                            button: false,
                            timer: 5000,
                        }).then(function() {
                            location.reload();
                        });

                    } else if (res.status == 400) {
                        Swal.fire({
                            title: "Warning!",
                            text: res.msg,
                            icon: "warning",
                            button: false,
                            timer: 5000,
                        })
                    }
                }
            });


        });

        // update password
        $(document).on('submit', '#update_password_form', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("update_password", true);

            $.ajax({
                type: "POST",
                url: "ajax/profile_ajax.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var res = jQuery.parseJSON(response);
                    if (res.status == 200) {
                        Swal.fire({
                            title: "Success!",
                            icon: "success",
                            button: false,
                            timer: 5000,
                        }).then(function() {
                            location.reload();
                        });

                    } else if (res.status == 400) {
                        Swal.fire({
                            title: "Warning!",
                            text: res.msg,
                            icon: "warning",
                            button: false,
                            timer: 5000,
                        })
                    }
                }
            });
        });
    });
</script>

<script>
    var myInput = document.getElementById("new_password");
    var letter = document.getElementById("letter");
    var capital = document.getElementById("capital");
    var number = document.getElementById("number");
    var length = document.getElementById("length");

    // When the user clicks on the password field, show the message box
    myInput.onfocus = function() {
        document.getElementById("message").style.display = "block";
    }

    // When the user clicks outside of the password field, hide the message box
    myInput.onblur = function() {
        document.getElementById("message").style.display = "none";
    }

    // When the user starts to type something inside the password field
    myInput.onkeyup = function() {
        // Validate lowercase letters
        var lowerCaseLetters = /[a-z]/g;
        if (myInput.value.match(lowerCaseLetters)) {
            letter.classList.remove("invalid");
            letter.classList.add("valid");
        } else {
            letter.classList.remove("valid");
            letter.classList.add("invalid");
        }

        // Validate capital letters
        var upperCaseLetters = /[A-Z]/g;
        if (myInput.value.match(upperCaseLetters)) {
            capital.classList.remove("invalid");
            capital.classList.add("valid");
        } else {
            capital.classList.remove("valid");
            capital.classList.add("invalid");
        }

        // Validate numbers
        var numbers = /[0-9]/g;
        if (myInput.value.match(numbers)) {
            number.classList.remove("invalid");
            number.classList.add("valid");
        } else {
            number.classList.remove("valid");
            number.classList.add("invalid");
        }

        // Validate length
        if (myInput.value.length >= 8) {
            length.classList.remove("invalid");
            length.classList.add("valid");
        } else {
            length.classList.remove("valid");
            length.classList.add("invalid");
        }
    }
</script>