<?php 
    require_once "../includes/functions.php";

    if(isLoggedIn()){
        redirect("../index.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>redirect logged in</title>
</head>
<body>
    <h1>User is already logged in</h1>
</body>
</html>