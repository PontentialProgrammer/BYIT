<?php 
    define("DB_HOST", "localhost");
    define("DB_NAME", "eComm");
    define("DB_USER", "root");
    define("DB_PASS", "password");
    define("DB_PORT", "3306");
    define("SUPER_EMAIL", "user@gmail.com");
    define("SUPER_NAME", "admin");
    define("SUPER_PASS", password_hash("34qiW=L]1mt-", PASSWORD_BCRYPT));


    $data_source_name = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    try{
        $pdo = new PDO($data_source_name, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        


    }catch(PDOException $e){
        echo "Connection failed: ". $e->getMessage();
    }


?>