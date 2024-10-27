<?php
session_start();
require_once '../classes/customer.class.php';
require_once '../tools/functions.php';

$account_created = false;
$validation_errors = false;
$otp_sent = false;
$not_verified = false;

if (isset($_POST['signup'])) {
    $user = new Customer();
    // Sanitize
    $user->username = htmlentities($_POST['username']);
    $user->email = htmlentities($_POST['email']);
    $user->password = htmlentities($_POST['password']);

    // Validate
    if (validate_field($user->username) &&
        validate_field($user->email) &&
        validate_field($user->password) &&
        validate_password($user->password) === "success" &&
        validate_cpw($user->password, $_POST['confirmpassword']) &&
        validate_email($user->email) === "success" && !$user->is_email_exist()) {
        
        if ($user->add()) {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['mail'] = $user->email;
            require "../Mail/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';

            $mail->Username = 'intellieggincubator@gmail.com';
            $mail->Password = 'wxcu nagz ewjv bfva';

            $mail->setFrom('intellieggincubator@gmail.com', 'OTP Verification');
            $mail->addAddress($_POST["email"]);

            $mail->isHTML(true);
            $mail->Subject = "Your verify code";
            $mail->Body = "<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3>";

            if (!$mail->send()) {
                echo '<script>alert("Register Failed, Invalid Email");</script>';
            } else {
                $otp_sent = true;
            }
        } else {
            echo 'An error occurred while adding in the database.';
        }
    } else {
        $validation_errors = true;
    }
}

require_once('../classes/account.class.php');

if (isset($_POST['login'])) {
    $account = new Account();
    $account->email = htmlentities($_POST['email']);
    $account->password = htmlentities($_POST['password']);
    $login_status = $account->sign_in_customer();

    if ($login_status == 'verified') {
        $_SESSION['user'] = 'customer';
        header('location: ../webpages/connect.php');
    } elseif ($login_status == 'not_verified') {
        $not_verified = true;
    } else {
        $error = 'Invalid email/password. Try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
    require_once '../tools/functions.php';
?>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container" id="container">
	<div class="form-container sign-up-container">
		  <form action="" method="post">
            <h1>Create Account</h1>
            <input type="text"  placeholder="Username" class="form-control" id="username" name="username" value="<?php if(isset($_POST['username'])){ echo $_POST['username']; } ?>">
            <?php
                if(isset($_POST['username']) && !validate_field($_POST['username'])){
            ?>
                <p class="text-danger my-1">Username is required</p>
            <?php
                }
            ?>
            <input type="email"  placeholder="Email" class="form-control" id="email" name="email" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } ?>">
            <?php
                $new_user = new Customer();
                if(isset($_POST['email'])){
                    $new_user->email = htmlentities($_POST['email']);
                }else{
                    $new_user->email = '';
                }

                if(isset($_POST['email']) && strcmp(validate_email($_POST['email']), 'success') != 0){
            ?>
                <p class="text-danger"style="font-size:11px;color:red;margin-top:0px;margin-bottom:0px;"><?php echo validate_email($_POST['email']) ?></p>
            <?php
                }else if ($new_user->is_email_exist() && $_POST['email']){
            ?>
                <p class="text-danger"style="font-size:11px;color:red;margin-top:0px;margin-bottom:0px;">Email already exists</p>
            <?php      
                }
            ?>
            <input type="password" placeholder="Password" class="form-control" id="password" name="password" value="<?php if(isset($_POST['password'])){ echo $_POST['password']; } ?>">
            <?php
                if(isset($_POST['password']) && validate_password($_POST['password']) !== "success"){
            ?>
                <p class="text-danger"style="font-size:11px;color:red;margin-top:0px;margin-bottom:0px;"><?= validate_password($_POST['password']) ?></p>
            <?php
                }
            ?>
            <input type="password" placeholder="Confirm Password"  class="form-control" id="confirmpassword" name="confirmpassword" value="<?php if(isset($_POST['confirmpassword'])){ echo $_POST['confirmpassword']; } ?>">
            <?php
                if(isset($_POST['password']) && isset($_POST['confirmpassword']) && !validate_cpw($_POST['password'], $_POST['confirmpassword'])){
            ?>
                <p class="text-danger"style="font-size:11px;color:red;margin-top:0px;">Your confirm password didn't match</p>
            <?php
                }
            ?>
            <button type="submit" name="signup" >Sign Up</button>
		</form>
	</div>
	<div class="form-container sign-in-container">
    <form action="" method="post">
			<h1>Sign in</h1>
            <input type="email" placeholder="Email" class="form-control" id="email" name="email" value="<?php if(isset($_POST['email'])){ echo $_POST['email']; } ?>">
            <input type="password" placeholder="Password" class="form-control" id="password" name="password" value="<?php if(isset($_POST['password'])){ echo $_POST['password']; } ?>">
            <?php
                        if (isset($_POST['login']) && isset($error)){
                        ?>
                            <p class="text-danger "style="font-size:13px;color:red;margin-top:0px;margin-bottom:0px;"><?= $error ?></p>
                        <?php
                        }
                        ?>
            <a href="../webpages/recover_password.php">Forgot your password?</a>
			<button type="submit" name="login" >Sign In</button>
           
		</form>
	</div>
	<div class="overlay-container">
		<div class="overlay">
			<div class="overlay-panel overlay-left">
                <img src="../images/logo-home.png">
                <h4>Intelli-Egg</h4>
				<h1>Welcome Back!</h1>
				<p>To keep connected with us please login with your personal info</p>
				<button class="ghost" id="signIn">Sign In</button>
			</div>
			<div class="overlay-panel overlay-right">
            <img src="../images/logo-home.png">
				<h1>Intelli-Egg</h1>
				<p>Enter your personal details and start journey with us</p>
				<button class="ghost" id="signUp">Sign Up</button>
			</div>
		</div>
	</div>
</div>

<?php if ($account_created): ?>
<script>
    alert('Account successfully created');
    document.getElementById('username').value = '';
    document.getElementById('email').value = '';
    document.getElementById('password').value = '';
    document.getElementById('confirmpassword').value = '';
</script>
<?php endif; ?>

<?php if ($validation_errors): ?>
<script>
    document.getElementById('container').classList.add('right-panel-active');
</script>
<?php endif; ?>
<?php if ($otp_sent): ?>
    <script>
        Swal.fire({
            title: "Register Successfully!",
            text: "OTP sent to <?php echo $user->email; ?>",
            icon: "success"
        }).then(function() {
            window.location.replace('../webpages/verification.php');
        });
    </script>
<?php endif; ?>

<?php if ($not_verified): ?>
    <script>
     Swal.fire({
        text: "Account is not yet verified.",
  title: "Verify it?",
  icon: "error",
  showCancelButton: true,
  confirmButtonColor: "#3085d6",
  cancelButtonColor: "#d33",
  confirmButtonText: "Yes, verify it!"
}).then((result) => {
  if (result.isConfirmed) {
    var email = '<?php echo $account->email; ?>';
    fetch('../webpages/send_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.replace('../webpages/verification.php');
        } else {
            Swal.fire('Error', 'Failed to send OTP. Please try again.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Failed to send OTP. Please try again.', 'error');
    });
  }
});
    </script>
<?php endif; ?>

<script src="../js/script.js"></script>
</body>
</html>
