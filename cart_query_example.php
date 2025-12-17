<?php
// Example SQL query to get order details with product information for a specific customer
// This should be used in your cart.php file

// Assuming you have the customer ID from session
$customer_id = $_SESSION['id'];

// Query to get orders with product details
$query = "
    SELECT 
        o.order_id,
        o.order_date,
        oi.quantity,
        p.product_id,
        p.product_name,
        p.product_desc,
        p.price,
        p.image_path,
        (p.price * oi.quantity) as total_price
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.customer_id = :customer_id
    ORDER BY o.order_date DESC, o.order_id
";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute([':customer_id' => $customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Example of how to display the orders
if (!empty($orders)) {
    $current_order_id = null;
    
    foreach ($orders as $order) {
        // Group by order_id to show orders separately
        if ($current_order_id !== $order['order_id']) {
            if ($current_order_id !== null) {
                echo '</div>'; // Close previous order
            }
            echo '<div class="order">';
            echo '<p class="order-id"><span class="tag">Order ID</span>: ' . htmlspecialchars($order['order_id']) . '</p>';
            echo '<p class="date-of-order"><span class="tag">Date</span>: ' . htmlspecialchars($order['order_date']) . '</p>';
            $current_order_id = $order['order_id'];
        }
        
        // Display each product in the order
        echo '<div class="order-item">';
        echo '<img src="./assets/images/' . htmlspecialchars($order['image_path']) . '" alt="' . htmlspecialchars($order['product_name']) . '" width="50" height="50">';
        echo '<div class="item-details">';
        echo '<p class="product-name">' . htmlspecialchars($order['product_name']) . '</p>';
        echo '<p class="product-desc">' . htmlspecialchars($order['product_desc']) . '</p>';
        echo '<p class="quantity">Quantity: ' . htmlspecialchars($order['quantity']) . '</p>';
        echo '<p class="price">$' . number_format($order['price'], 2) . ' each</p>';
        echo '<p class="total">Total: $' . number_format($order['total_price'], 2) . '</p>';
        echo '</div>';
        echo '</div>';
    }
    
    if ($current_order_id !== null) {
        echo '</div>'; // Close last order
    }
} else {
    echo '<p style="text-align: center; color: #666; font-size: 18px;">No orders found.</p>';
}
?>
