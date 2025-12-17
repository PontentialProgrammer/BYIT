<?php
require_once "../includes/database.php";
require_once "../includes/functions.php";

session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST" && (isset($_POST["email"]) || isset($_POST["pass"]))) {
    $email = $_POST["email"];
    $pass = $_POST["pass"];

    // error handling
    if (empty($email)) {
        $errors[] = "Please enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }

    if (empty($pass)) {
        $errors[] = "Please enter your password.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM CUSTOMERS WHERE email = :email');
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); 

        
        if ($user && password_verify($pass, $user["password"])) {
            if (!$user['is_verified']) {
                $_SESSION['pending_email'] = $user['email'];
                // redirect("./otp_verify.php");    
            }
            $_SESSION["email"] = $user["email"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["id"] = $user['customer_id'];
            redirect("../index.php");
        } else {
            $errors[] = "Email or Password is incorrect.";
        }
        
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preload" href="../assets/login_style.css" as="style" onload="this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="../assets/login_style.css"></noscript>
    <link rel="stylesheet" href="../assets/font.css">
    <title>Login</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
        <h1>BYIT  |  Login<hr></h1>

        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <label for="email">Enter your email...</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

        <label for="pass">Enter your password...</label>
        <input type="password" style="font-size:2em;" name="pass">

        <p class="reg-redirect">Don't have an account? <a href="./signup.php">Register here</a></p>
        <input type="submit" value="Login" name="submit">
    </form>
</body>
</html>
