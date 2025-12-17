<?php
require_once "../includes/database.php";
require_once "../includes/functions.php";
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = filter_var(trim($_POST["name"] ?? ""), FILTER_SANITIZE_SPECIAL_CHARS);
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["pass"] ?? "";
    $confirm_password = $_POST["confirm-pass"] ?? "";

    if (empty($name)) $errors[] = "Name field is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters long.";
    if ($password !== $confirm_password) $errors[] = "Passwords don't match.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT customer_id FROM customers WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "This email already exists.";
        }
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $otp = rand(100000, 999999); 
        $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $stmt = $pdo->prepare("INSERT INTO customers (name, email, password, otp_code, otp_expiry, is_verified) 
                               VALUES (:name, :email, :password, :otp, :expiry, 0)");
        $stmt->execute([
            ":name" => $name,
            ":email" => $email,
            ":password" => $hashed_password,
            ":otp" => $otp,
            ":expiry" => $otp_expiry
        ]);

        sendEmailOTP($email, $otp);
        $_SESSION['pending_email'] = $email;

        redirect("./otp_verify.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/login_style.css">
    <link rel="stylesheet" href="../assets/font.css">
    <title>Sign Up</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
        <h1>BYIT  |  Sign Up<hr></h1>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <label for="name">Enter your name...</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">

        <label for="email">Enter your email...</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label for="pass" >Enter your password...</label>
        <input type="password" style="font-size:2em;" name="pass">

        <label for="pass">Confirm password...</label>
        <input type="password" style="font-size:2em;"  name="confirm-pass">

        <p class="reg-redirect">Already have an account? <a href="./login.php">Login here</a></p>
        <input type="submit" value="Sign Up" name="signup">
    </form>
</body>
</html>
