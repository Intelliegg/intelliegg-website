<?php
session_start();
require "../Mail/phpmailer/PHPMailerAutoload.php";

$email = json_decode(file_get_contents('php://input'), true)['email'] ?? null;

if ($email) {
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['mail'] = $email;

    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';

    $mail->Username = 'intellieggincubator@gmail.com';
    $mail->Password = 'wxcu nagz ewjv bfva';

    $mail->setFrom('intellieggincubator@gmail.com', 'OTP Verification');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Your verify code";
    $mail->Body = "<p>Dear user, </p> <h3>Your verify OTP code is $otp <br></h3>";

    if ($mail->send()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send email']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Email not provided']);
}
?>
