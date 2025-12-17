<?php
require_once "../includes/database.php";
require_once "../includes/functions.php";

session_start();
if (!isset($_SESSION["email"])) {
    redirect("../index.php");
}


?>

<?php
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["logout"])) {
    session_unset();
    session_destroy();
    redirect("../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/font.css">
    <link rel="stylesheet" href="../assets/cart.css">
    <link rel="stylesheet" href="../assets/style.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (isset($_SESSION["email"])): ?>
        <title><?php echo htmlspecialchars($_SESSION["name"]) ?>'s Cart</title>
    <?php endif; ?>
    <link rel="shortcut icon" href="../assets/images/logo.png" type="image/x-icon">
</head>



<body>
    <?php include "./nav.php" ?>

    <div class="cart">
        <h1 class="title"><?php echo $_SESSION['name']; ?>'s Cart</h1>

        <div class="order">
            <div class="product">
                <img width="200px" height="200px" src="./assets/images/400x400.jpeg">
                <div class="label">
                    <h1 class="product-name">White Collar Shirt</h1>
                    <p class="product-description">For Official/Corporate business</p>
                </div>


                <div class="finance">
                    <p class="price">$<span class="price-dig">4</span></p>
                    <!-- <button>Add to Cart</button> -->
                    <div class="counter-compact">
                        <button class="counter-btn-1 counter-btn" onclick="updateCount( -1)">-</button>
                        <span class="counter-display">1</span>
                        <button class="counter-btn-2 counter-btn" onclick="updateCount( 1)">+</button>
                    </div>

                </div>
                <button class="checkout-btn">Checkout</button>

            </div>

        </div>

    </div>


</body>

<script>
    
    document.getElementById("accountToggle").addEventListener("click", function (e) {
        e.preventDefault();
        document.querySelector(".account-dropdown").classList.toggle("show");
        document.querySelector(".help").classList.toggle("show");

    });

    const hamburger = document.getElementById("hamburger");
    const navMenu = document.getElementById("navMenu");

    hamburger.addEventListener("click", function () {
        hamburger.classList.toggle("active");
        navMenu.classList.toggle("show");
        document.querySelector(".no-1").classList.toggle("clicked");
        document.querySelector(".no-2").classList.toggle("clicked");
        document.querySelector(".no-3").classList.toggle("clicked");
    });
    //item count logic

    const counter_btn_two = document.querySelector(".counter-btn-2");
    const counter_btn_one = document.querySelector(".counter-btn-1");
    const count_display = document.querySelector(".counter-display");
    const price_dig = document.querySelector(".price-dig");
    let price = parseInt(price_dig.textContent);
    let count_dig = parseInt(count_display.textContent);
    // console.log(count);

    function updateCount(count) {
        count_dig += count;
        count_display.textContent = count_dig;
        let new_price = count_dig * price;
        price_dig.textContent = new_price;
    }

    const home_redirect =  document.querySelector("#home");

    home_redirect.addEventListener('click', function () {
        window.location = "http://localhost/project-1";
    });

    const home_redirect =  document.querySelector("#home");

    home_redirect.addEventListener('click', function () {
        window.location = "http://localhost/project-1";
    });

</script>

</html>