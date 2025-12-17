<?php 
    require_once "./includes/functions.php";
    include "./includes/database.php";
    session_start();

    
?>


<?php 
    if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["logout"])){
        session_unset();
        session_destroy();
    }

    $search_query = "";
    if(isset($_GET['search']) && !empty(trim($_GET['search']))){
        $search_query = trim($_GET['search']);
        $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 AND (product_name LIKE :search OR product_desc LIKE :search) ORDER BY product_id DESC");
        $stmt->execute([':search' => '%' . $search_query . '%']);
        $products = $stmt->fetchAll();
    } else {
        $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY product_id DESC");
        $products = $stmt->fetchAll();
    }

    $stmt = $pdo->query("SELECT * FROM products WHERE is_active = 1 ORDER BY product_id DESC");
    $products = $stmt->fetchAll();

    if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["button"])){
        $id = $_POST["product_id"];

        $date = date("Y-m-d");
        if(isset($_SESSION["email"]) && $_SESSION["email"] != "user@gmail.com"){
            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, order_date) VALUES (:customer_id, :order_date)");
            $stmt->execute([
                ":customer_id" => $_SESSION['id'],
                ":order_date" => $date
            ]);

            $stmt = $pdo->query("SELECT * FROM orders");
            $orders = $stmt->fetchAll();

            foreach($orders as $order){
                if($order['customer_id'] == $_SESSION['id']){
                    $order_id = $order['order_id'];
                }
            }

            
            

            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([
                $order_id, $id, 1
            ]);
        }

    }


    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/font.css">
    <link rel="shortcut icon" href="./assets/images/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/style.css">
    <title>BYIT</title>
</head>
<body>
    <?php include "./pages/nav.php"; ?>

    <div class="product-catalogue">
        <h2>Our Products</h2>
        <p class="business-desc">We offer great Products at affordable prices.</p>


        <div class="products">
            <!-- php for($i = 0; $i < 10; $i++):?>
                php include "./pages/product.php"?>
            php endfor -->


            <?php if(empty($products)):?>
                <p style="text-align: center; color: #666; font-size: 18px;">No products found. 
                <?php if(isset($_SESSION['email']) && $_SESSION['email'] == "user@gmail.com"):?>    
                    <a href="./pages/adminpage.php">Add your first product</a></p>
                <?php endif;?>
            <?php else:?>
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <!-- <img  src="./assets/images/400x400.jpeg"> -->
                        <img width="200px" height="250px" src="./assets/images/<?php echo htmlspecialchars($product['image_path']); ?>" 
                                                alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                                onerror="this.src='../assets/images/placeholder.jpg'">
                        
                        <div class="label">
                            <h1 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                            <p class="product-description"><?php echo htmlspecialchars($product['product_desc']); ?></p>
                        </div>
                        <div class="finance">
                            <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                            <form style="height:50px; margin-top:10px; display:flex; flex-direction:column; align-contents:center;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
                                <input type="text" hidden name="product_id" value="<?php echo htmlspecialchars($product['product_id']);?>">
                                <button type="submit" name="button">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach?>
            <?php endif;?>


        </div>
            
        
    </div>
    

    <?php include "./pages/footer.php" ?>
    
</body>

<script>
document.getElementById("accountToggle").addEventListener("click", function(e) {
    e.preventDefault();
    document.querySelector(".account-dropdown").classList.toggle("show");
    document.querySelector(".help").classList.toggle("show");

});

// document.querySelector(".cart-2t").addEventListener('click', () => {
//     document.querySelector(".account-dropdown").classList.toggle("show");
//     document.querySelector(".help").classList.toggle("show");
// })

function account_toggle(){
    document.querySelector(".account-dropdown").classList.toggle("show");
    document.querySelector(".help").classList.toggle("show");
}

const nav = document.querySelector(".nav");
const navHeight = nav.offsetHeight + 150;

let isFixed = false; // Track state to avoid repeated animations
let animating = false; // Prevent overlapping animations

// Set initial position
nav.style.position = "relative";
nav.style.top = "0px";
nav.style.transition = "none";

window.addEventListener("scroll", () => {
    if (animating) return;

    if (window.scrollY > navHeight && !isFixed) {
        // Fix navbar with slide-down
        isFixed = true;
        animating = true;
        nav.style.position = "fixed";
        nav.style.top = `-${navHeight}px`; // start hidden above
        nav.style.width = "100%";
        nav.style.transition = "top 0.3s ease";
        requestAnimationFrame(() => {
            nav.style.top = "0px"; // slide down
            setTimeout(() => animating = false, 300);
        });
    } else if (window.scrollY <= navHeight && isFixed) {
        // Unfix navbar with slide-up
        animating = true;
        nav.style.top = `-${navHeight}px`; // slide up
        setTimeout(() => {
            nav.style.position = "relative";
            nav.style.transition = "none";
            nav.style.top = "0px";
            isFixed = false;
            animating = false;
        }, 300);
    }
});


// document.getElementsByClassName("no-1").addEventListener('click', function(){
//     document.querySelector(".line").classList.toggle("clicked");
// });

const hamburger = document.getElementById("hamburger");
const navMenu = document.getElementById("navMenu");

hamburger.addEventListener("click", function() {
    hamburger.classList.toggle("active");
    navMenu.classList.toggle("show");
    document.querySelector(".no-1").classList.toggle("clicked");
    document.querySelector(".no-2").classList.toggle("clicked");
    document.querySelector(".no-3").classList.toggle("clicked");


});

const home_redirect =  document.querySelector("#home");

home_redirect.addEventListener('click', function () {
    window.location = "http://localhost/project-1";
});

const admin_redirect = document.querySelector("#admin");

admin_redirect.addEventListener('click', function () {
    window.location = "http://localhost/project-1/pages/adminpage.php"
})

</script>

</html>

<?php 
    

?>