<?php 

    require "C:/xampp/htdocs/project-1/PHPMailerMaster/src/PHPMailer.php";
    require "C:/xampp/htdocs/project-1//PHPMailerMaster/src/SMTP.php";
    require "C:/xampp/htdocs/project-1//PHPMailerMaster/src/Exception.php";
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

 
 
    function redirect($url){
        header('Location:' . $url);
        exit();
    }

    function isLoggedIn(){
        return false;
    }

    function sendEmailOTP($toEmail, $otp) {
        $mail = new PHPMailer(true);

        

        $subject = "Your BYIT Account Verification Code";
        $message = "
        <html>
        <head>
            <title>Email Verification</title>
        </head>
        <body>
            <p>Hi,</p>
            <p>Your OTP code is: <strong>$otp</strong></p>
            <p>This code will expire in 10 minutes.</p>
            <p>If you didn’t request this, please ignore this email.</p>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: BYIT <no-reply@byit.com>\r\n";
    try {
        // SMTP setup (use Gmail App Password)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ayot.oyewumi@gmail.com';
        $mail->Password   = 'tdpb imaz deuq irhf'; // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        // Sender & Recipient
        $mail->setFrom('ayot.oyewumi@gmail.com', 'BYIT');
        $mail->addAddress($toEmail);
 
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = "Dear User,\n\nYour One-Time Password (OTP) for verification is: $otp\n\nThis code will expire in 5 minutes. Do not share it with anyone.\n\nThank you,\nThe BYIT Team";
 
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }

        // return mail($toEmail, $subject, $message, $headers);
    }

    function href_redirect($url){
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $target_page = '/project-1/pages/' . $url; // Absolute path from document root
        $full_url = "{$protocol}://{$host}{$target_page}";
        echo "$full_url"; // Important to stop further script execution
    }

?>