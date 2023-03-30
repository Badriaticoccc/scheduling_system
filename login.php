<?php
require 'config/config.php';
require GLOBAL_FUNC;
require CONNECT_PATH;
require CL_SESSION_PATH;

$login = $session_class->getValue('login');
$role = $session_class->getValue('role');


if (isset($login) && isset($role)) {
	if ($login == "success" && $role == "Admin") {
		header('Location: index.php');
		exit();
	}
	if ($login == "success" && $role == "Faculty") {
		header('Location: index.php');
		exit();
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php
	include DOMAIN_PATH . "/app/global/meta_data.php";
	include DOMAIN_PATH . "/app/global/include_top.php" ?>
</head>


<body class="text-center">
	<br><br><br>
	<section class="position-relative py-4 py-xl-5">
		<div class="container">
			<div class="row mb-3">
				<div class="col-md-9 col-xl-6 col-xxl-6 text-center mx-auto">
					<h2 style="color:#418af2;font-size:2;">CCC SCHEDULING SYSTEM</h2>
					<p class="w-lg-50">Competence, Commitment and Character</p>
				</div>
			</div>
			<div class="row d-flex justify-content-center">
				<div class="col-md-6 col-xl-4">
					<div class="card mb-5">
						<div class="card-body d-flex flex-column align-items-center"><img src="<?php echo BASE_URL; ?>assets/images/ccc_logo.png" style="width: 150px;height: 150px;">
							<form class="text-center" method="post" action="">
								<div class="mb-3">
									<p>Login to your account</p>
									<input class="form-control" type="text" name="username" placeholder="Username" id="username">
								</div>
								<div class="mb-3"><input class="form-control" type="password" name="password" placeholder="Password" id="password"></div>
								<div class="mb-3"><a name="login" class="btn d-block w-100" id="login" style="background-color:#418af2;color:white;">Login</a></div>
								<p class="mt-5 mb-3 text-muted">&copy; <?php echo date('Y') ?>-<?php echo date('Y', strtotime('+1 year')); ?></p>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php include DOMAIN_PATH . "/app/global/include_buttom.php" ?>
</body>

<script>
	document.getElementById('Noite')
	// PARA DI NALABAS YUNG RESUBMIT SA TAAS
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
	document.addEventListener("keydown", function(event) {
            if (event.keyCode === 13) {
                document.getElementById("login").click();
            }
        });
	$(document).ready(function() {
		$('#login').on('click', function() {
			// var formdata = new FormData(user_login);
			var username = $('#username').val();
			var password = $('#password').val();
			// alert(username + password);
			if (username == '' || password == '') {
				Swal.fire({
					icon: 'warning',
					title: 'Something Went Wrong!',
					text: 'Please fill all fields.'
				})
			}

			$.ajax({
				url: '<?php echo BASE_URL ?>userlogin.php',
				type: 'POST',
				data: {
					u_name: username,
					pass: password
				},
				success: function(response) {
					var res = jQuery.parseJSON(response);
					if (res.success == 200) {
							location.reload();
					} else if (res.success == 400) {
						Swal.fire({
							icon: 'warning',
							title: res.title,
							text: res.message
						})
					}
				}
			})
		});
	})
</script>

</html>