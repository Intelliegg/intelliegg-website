<?php
session_start();
require_once '../classes/customer.class.php';
require_once '../tools/functions.php';
$otp_sent = false;
if(isset($_POST['verify'])){
    $entered_otp = $_POST['otp_code'];
    $session_otp = $_SESSION['otp'];
    $email = $_SESSION['mail'];
    
    if($entered_otp == $session_otp){
        $user = new Customer();
        $user->email = $email;
        if($user->activate_account()){
            header('location: ../webpages/connect.php');
            exit();
        } else {
            echo "Failed to activate account";
        }
    } else {
        echo "Invalid OTP";
    }
}

if(isset($_POST['resend_otp'])){
    $email = $_SESSION['mail'];
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    if(sendOTP($email, $otp)){
        $otp_sent = true;
    } else {
        echo "<script>alert('Failed to resend OTP. Please try again later.');</script>";
    }
}

function sendOTP($email, $otp) {
    require "../Mail/phpmailer/PHPMailerAutoload.php";
    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host='smtp.gmail.com';
    $mail->Port=587;
    $mail->SMTPAuth=true;
    $mail->SMTPSecure='tls';

    $mail->Username='intellieggincubator@gmail.com';
    $mail->Password='wxcu nagz ewjv bfva';

    $mail->setFrom('intellieggincubator@gmail.com', 'OTP Verification');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject="Your verify code";
    $mail->Body="<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3>";

    if(!$mail->send()){
        return false;
    } else {
        return true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
  <title>OTP Verification</title>
  <link rel="icon" type="image/x-icon" href="../images/logo-home.png">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css'>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container" id="verify-container" style="display: flex;flex-wrap: wrap;justify-content: space-evenly;align-items: stretch;">
    <div class="verify-form-container">
        <form action="" method="post">
            <h1>Verify OTP</h1>
            <div style="display: flex;margin:20px;width:100%;">
            <input type="text" placeholder="Enter OTP" class="form-control" id="otp_code" name="otp_code"style="width:450px;">  
            <button type="submit" name="resend_otp" class="btn btn-link"style="margin-left:20px;height:50px;">Resend OTP</button>
</div>
            <button type="submit" name="verify">Verify</button>
          
        </form>
    </div>
</div>
  
<?php if ($otp_sent): ?>
    <script>
        Swal.fire({
            title: "Resend OTP Successfully!",
            icon: "success"
        });
    </script>
    <?php endif; ?>
<script src="../js/script.js"></script>
</body>
</html>

