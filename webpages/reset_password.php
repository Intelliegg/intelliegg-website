<?php
session_start();
$notif = false;
$warning = false;
$warningMessage = '';
require_once('../classes/account.class.php'); 
require_once('../tools/functions.php');

if (isset($_POST["reset"])) {
    $newPassword = htmlentities($_POST["password"]);
    $passwordValidation = validate_password($newPassword);
    $confirmPasswordValidation = validate_cpw($newPassword, $_POST['cpassword']);

    if ($passwordValidation === "success" && $confirmPasswordValidation) {
        $token = $_SESSION['token'];
        $email = $_SESSION['email'];
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $account = new Account();
        $success = $account->updatePassword($email, $hashedPassword);

        if ($success) {
            $notif = true; 
        } else {
            echo '<script>alert("Failed to reset password. Please try again.");</script>';
        }
    } else {
        $warning = true;
        $warningMessage = $passwordValidation !== "success" ? $passwordValidation : "Passwords do not match.";
    }
}
?>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main class="login-form">
    <div class="container" id="verify-container" style="display: flex; flex-wrap: wrap; justify-content: space-evenly; align-items: stretch;">
        <div class="verify-form-container">
            <form action="#" method="POST" name="resetForm">
                <h1>Reset Password</h1>
                <div style="display: flex; margin: 20px; width: 100%;">
                    <label for="password">New Password</label>
                    <input type="password" id="password" class="form-control" style="margin: 20px;" name="password" required autofocus>
                    <i class="bi bi-eye-slash" id="togglePassword" style="cursor: pointer;"></i>
                </div>
                <div style="display: flex; margin: 20px; width: 100%;">
                    <label for="cpassword">Confirm Password</label>
                    <input type="password" id="cpassword" class="form-control" style="margin: 20px;" name="cpassword" required>
                </div>
                <input type="submit" value="Reset" name="reset" style="border: none; color: black; background-color: #F1A661; box-shadow: 0 0 0 2px #F1A661;">
            </form>
        </div>
    </div>
</main>

<script>
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    toggle.addEventListener('click', function(){
        if (password.type === "password") {
            password.type = 'text';
        } else {
            password.type = 'password';
        }
        this.classList.toggle('bi-eye');
    });
</script>

<?php if ($notif): ?>
    <script>
        Swal.fire({
            title: "Success!",
            text: "Your password has been reset. Please log in with your new password.",
            icon: "success"
        }).then(function() {
            window.location.replace('../webpages/login.php');
        });
    </script>
<?php endif; ?>

<?php if ($warning): ?>
    <script>
        Swal.fire({
            title: "Warning!",
            text: "<?php echo $warningMessage; ?>",
            icon: "warning"
        });
    </script>
<?php endif; ?>
</body>
</html>
