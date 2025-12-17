<?php 
    include "../includes/database.php";
    include "../includes/functions.php";
    session_start();

    // Handle delete functionality
    if(isset($_GET['delete_order']) && is_numeric($_GET['delete_order'])) {
        $order_id = $_GET['delete_order'];
        
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Delete order items first
            $stmt1 = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt1->execute([$order_id]);
            
            // Delete order
            $stmt2 = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
            $stmt2->execute([$order_id]);
            
            // Commit transaction
            $pdo->commit();
            
            header("Location: cart.php?success=deleted");
            exit();
        } catch (Exception $e) {
            // Rollback on error
            $pdo->rollback();
            header("Location: cart.php?error=delete_failed");
            exit();
        }
    }

    $stmt = $pdo->query("SELECT * FROM orders");
    $orders = $stmt->fetchAll();

    // Handle success/error messages
    $success_message = '';
    $error_message = '';

    if (isset($_GET['success']) && $_GET['success'] == 'deleted') {
        $success_message = 'Order deleted successfully!';
    }

    if (isset($_GET['error']) && $_GET['error'] == 'delete_failed') {
        $error_message = 'Failed to delete order. Please try again.';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/checkout.css">
    <link rel="stylesheet" href="../assets/font.css">
    <title>Orders</title>
    <style>
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .order {
            position: relative;
        }
        
        .delete-btn {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 10px;
            display: inline-block;
        }
        
        .delete-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <?php include "./nav.php" ?>

    <div class="orders">
        <h1>Pending Orders</h1>
        
        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php foreach($orders as $order):?>
            <?php
                $stmt = $pdo->prepare("SELECT product_id FROM order_items WHERE order_id = :id");
                $stmt->execute([':id' => $order['order_id']]);
                $p_id = $stmt->fetchColumn();

                $stmt = $pdo->prepare("SELECT product_name FROM products WHERE product_id = :id");
                $stmt->execute([':id' => $p_id]);
                $name = $stmt->fetchColumn();

                if (!$name) continue;
            ?>
            <div class="order">
                <p class="order-id"><span class="tag">id</span>: <?php echo $order['order_id']; ?></p>
                <p class="order-name"><span class="tag">order</span>: <?php echo $name; ?></p>
                <p class="date-of-order"><span class="tag">date-of-purchase</span>: <?php echo date("F j, Y", strtotime($order['order_date'])); ?></p>
                
                <!-- DELETE FUNCTIONALITY ADDED -->
                <a href="cart.php?delete_order=<?php echo $order['order_id']; ?>" 
                   class="delete-btn" 
                   onclick="return confirm('Are you sure you want to delete this order?')">
                    Delete Order
                </a>
            </div>
        <?php endforeach?>

        <?php include "./pages/footer.php" ?>

    </div>

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
        const hamburger = document.getElementById("hamburger");
        const navMenu = document.getElementById("navMenu");

        hamburger.addEventListener("click", function () {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("show");
            document.querySelector(".no-1").classList.toggle("clicked");
            document.querySelector(".no-2").classList.toggle("clicked");
            document.querySelector(".no-3").classList.toggle("clicked");
        });

        const home_redirect = document.querySelector("#home");

        home_redirect.addEventListener('click', function () {
            window.location = "http://localhost/project-1";
        });
    </script>
</body>
</html>