<?php
require_once "../includes/database.php";
require_once "../includes/functions.php";
session_start();

if (!isset($_SESSION['pending_email'])) {
    redirect("./signup.php");
}

$errors = [];
$success = "";
$email = $_SESSION['pending_email'];

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['otp'])) {
    $otp_input = trim($_POST['otp']);

    $stmt = $pdo->prepare("SELECT otp_code, otp_expiry FROM customers WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $errors[] = "User not found.";
    } elseif (strtotime($user['otp_expiry']) < time()) {
        $pdo->prepare("DELETE FROM customers WHERE email = :email")->execute([':email' => $email]);
        unset($_SESSION['pending_email']);
        $errors[] = "OTP expired. Please register again.";
    } elseif ($otp_input !== $user['otp_code']) {
        $errors[] = "Invalid OTP.";
    } else {
        $pdo->prepare("UPDATE customers SET is_verified = 1, otp_code = NULL, otp_expiry = NULL WHERE email = :email")
            ->execute([':email' => $email]);
        unset($_SESSION['pending_email']);
        redirect("./login.php");
    }
}

if (isset($_GET['resend']) && $_GET['resend'] === 'success') {
    $success = "A new OTP has been sent to your email.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../assets/otp_style.css">
</head>
<body>

    <form method="post" id="otpForm">
        <h1>Verify Your Email</h1>

        <?php if ($errors): ?>
            <div class="error-box">
                <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-box">
                <p><?= $success ?></p>
            </div>
        <?php endif; ?>

        <label>Enter OTP:</label>
        <div class="otp-inputs">
            <?php for ($i = 0; $i < 6; $i++): ?>
                <input type="text" class="otp-digit" maxlength="1" required>
            <?php endfor; ?>
        </div>
        <input type="hidden" name="otp" id="otpFull">

        <input type="submit" value="Verify">

        <div class="resend-link">
            <a href="resend_otp.php">Resend OTP</a>
        </div>
    </form>
</body>

<script>
    
    // const inputs = document.querySelectorAll('.otp-digit');
    // const otpFull = document.getElementById('otpFull');

    // inputs.forEach((input, index) => {
    //     input.addEventListener('input', () => {
    //         if (input.value.length > 0 && index < inputs.length - 1) {
    //             inputs[index + 1].focus();
    //         }
    //         updateOTP();
    //     });

    //     input.addEventListener('keydown', (e) => {
    //         if (e.key === 'Backspace' && input.value === '' && index > 0) {
    //             inputs[index - 1].focus();
    //         }
    //     });
    // });

    // function updateOTP() {
    //     otpFull.value = Array.from(inputs).map(i => i.value).join('');
    // }

    // document.getElementById('otpForm').addEventListener('submit', (e) => {
    //     updateOTP();
    //     if (otpFull.value.length !== 6) {
    //         e.preventDefault();
    //         alert('Please enter all 6 digits of your OTP.');
    //     }
    // });

    const inputs = document.querySelectorAll('.otp-digit');
    const otpFull = document.getElementById('otpFull');

    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            const value = e.target.value;

            // Handle paste (if user pastes all digits)
            if (value.length > 1) {
                const chars = value.split('').slice(0, inputs.length);
                chars.forEach((char, i) => {
                    inputs[i].value = char;
                });
                inputs[Math.min(chars.length, inputs.length) - 1].focus();
                updateOTP();
                return;
            }

            // Auto-focus next field
            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
            updateOTP();
        });

        // Backspace moves to previous field
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        // Allow paste from clipboard
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').trim();
            const chars = pasteData.split('').slice(0, inputs.length);
            chars.forEach((char, i) => {
                inputs[i].value = char;
            });
            inputs[Math.min(chars.length, inputs.length) - 1].focus();
            updateOTP();
        });
    });

    function updateOTP() {
        otpFull.value = Array.from(inputs).map(i => i.value).join('');
    }

    document.getElementById('otpForm').addEventListener('submit', (e) => {
        updateOTP();
        if (otpFull.value.length !== inputs.length) {
            e.preventDefault();
            alert('Please enter all digits of your OTP.');
        }
    });


    
</script>

</html>
