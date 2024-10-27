<?php
session_start();
$notif = false;
require_once('../classes/account.class.php'); 

if (isset($_POST['recover'])) {
    $email = htmlentities($_POST['email']);

    $account = new Account();
    $accountData = $account->getAccountByEmail($email); // Use the existing connection in Account class

    if ($accountData) {
        if ($accountData['status'] == 'not_verified') {
            ?>
            <script>
                alert("Sorry, your account must be verified before you can recover your password!");
                window.location.replace("../webpages/login.php");
            </script>
            <?php
            exit();
        }

        $token = bin2hex(random_bytes(50));

        $_SESSION['token'] = $token;
        $_SESSION['email'] = $email;

        require_once "../Mail/phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Username = 'intellieggincubator@gmail.com';
        $mail->Password = 'wxcu nagz ewjv bfva';
        $mail->setFrom('intellieggincubator@gmail.com', 'Password Reset');
        $mail->addAddress($_POST["email"]);
        $mail->isHTML(true);
        $mail->Subject = "Recover your password";
        $mail->Body = "<b>Dear User,</b>
            <h3>We received a request to reset your password.</h3>
            <p>Kindly click the below link to reset your password:</p>
            <a href='http://localhost/Thesis-intelliegg/webpages/reset_password.php?token=$token'>Reset Password Link</a>";

        if (!$mail->send()) {
            ?>
            <script>
                alert("Invalid Email");
            </script>
            <?php
        } else {
            $notif = true; // Set notification flag for success
        }
    } else {
        ?>
        <script>
            alert("Sorry, no account exists with this email.");
        </script>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main class="login-form">
<div class="container" id="verify-container" style="display: flex;flex-wrap: wrap;justify-content: space-evenly;align-items: stretch;">
        <div class="verify-form-container">
            <form action="#" method="POST" name="recover_psw">
                <h1>Password Recovery</h1>
                <div style="display: flex;margin:20px;width:100%;">

                    <label for="email_address">E-Mail Address</label>
                    <input type="email" id="email_address" class="form-control" name="email" required autofocus>
                </div>
                <div class="form-group">
                    <input type="submit" value="Recover" name="recover" class="btn btn-primary"style="border:none;color;black;background-color:#F1A661;box-shadow: 0 0 0 2px #F1A661;">
                </div>
            </form>
        </div>
    </div>
</main>

<?php if ($notif): ?>
    <script>
        Swal.fire({
            title: "Email Sent!",
            text: "Kindly check your email inbox.",
            icon: "success"
        }).then(function() {
            window.location.replace('../webpages/login.php');
        });
    </script>
<?php endif; ?>

</body>
</html>
