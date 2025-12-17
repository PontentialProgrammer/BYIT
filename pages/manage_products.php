<?php 
    include "../includes/functions.php";
    include "../includes/database.php";

    session_start();
    
    if(!isset($_SESSION) || $_SESSION["email"] != "user@gmail.com"){
        redirect("../index.php");
    }

    // Handle delete request (SOFT DELETE - FIXED)
    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        $product_id = $_GET['delete'];
        
        // Check if product has existing orders
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
        $check_stmt->execute([$product_id]);
        $order_count = $check_stmt->fetchColumn();
        
        if ($order_count > 0) {
            // Product has orders - use soft delete
            $soft_delete_stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE product_id = ?");
            if ($soft_delete_stmt->execute([$product_id])) {
                header("Location: manage_products.php?success=deactivated");
                exit();
            } else {
                header("Location: manage_products.php?error=delete_failed");
                exit();
            }
        } else {
            // No orders - safe to hard delete
            // Get image path before deleting
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch();
            
            if ($product) {
                // Delete image file
                $image_path = "../assets/images/" . $product['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                
                // Delete from database
                $delete_stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
                if ($delete_stmt->execute([$product_id])) {
                    header("Location: manage_products.php?success=deleted");
                    exit();
                } else {
                    header("Location: manage_products.php?error=delete_failed");
                    exit();
                }
            }
        }
    }

    // Handle reactivate request
    if (isset($_GET['reactivate']) && is_numeric($_GET['reactivate'])) {
        $product_id = $_GET['reactivate'];
        $reactivate_stmt = $pdo->prepare("UPDATE products SET is_active = 1 WHERE product_id = ?");
        if ($reactivate_stmt->execute([$product_id])) {
            header("Location: manage_products.php?success=reactivated");
            exit();
        }
    }

    // Handle edit request
    if (isset($_POST['edit_submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $product_id = $_POST['product_id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        
        $success = false;
        $error_message = '';
        
        // Check if new image is uploaded
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            // Get old image path
            $stmt = $pdo->prepare("SELECT image_path FROM products WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $old_product = $stmt->fetch();
            
            // Handle new image upload
            $upload_dir = "../assets/images/";
            $file_name = $_FILES['product_image']['name'];
            $file_tmp = $_FILES['product_image']['tmp_name'];
            $file_size = $_FILES['product_image']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $new_filename = uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_ext, $allowed_extensions)) {
                if ($file_size <= 5000000) {
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Delete old image
                        if ($old_product && file_exists($upload_dir . $old_product['image_path'])) {
                            unlink($upload_dir . $old_product['image_path']);
                        }
                        
                        // Update database with new image
                        $stmt = $pdo->prepare("UPDATE products SET product_name = ?, product_desc = ?, price = ?, image_path = ? WHERE product_id = ?");
                        if ($stmt->execute([$title, $description, $price, $new_filename, $product_id])) {
                            header("Location: manage_products.php?success=updated");
                            exit();
                        }
                    } else {
                        $error_message = 'Failed to upload new image.';
                    }
                } else {
                    $error_message = 'File size too large. Maximum 5MB allowed.';
                }
            } else {
                $error_message = 'Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP allowed.';
            }
        } else {
            // Update without changing image
            $stmt = $pdo->prepare("UPDATE products SET product_name = ?, product_desc = ?, price = ? WHERE product_id = ?");
            if ($stmt->execute([$title, $description, $price, $product_id])) {
                header("Location: manage_products.php?success=updated");
                exit();
            }
        }
        
        if (!$success && !$error_message) {
            $error_message = 'Failed to update product.';
        }
        
        if ($error_message) {
            header("Location: manage_products.php?error=" . urlencode($error_message));
            exit();
        }
    }

    // Get all products (including inactive ones for admin view)
    $stmt = $pdo->query("SELECT * FROM products ORDER BY is_active DESC, product_id DESC");
    $products = $stmt->fetchAll();

    // Handle success/error messages
    $success_message = '';
    $error_message = '';

    if (isset($_GET['success'])) {
        switch($_GET['success']) {
            case 'deleted':
                $success_message = 'Product deleted successfully!';
                break;
            case 'deactivated':
                $success_message = 'Product deactivated successfully! (Had existing orders)';
                break;
            case 'reactivated':
                $success_message = 'Product reactivated successfully!';
                break;
            case 'updated':
                $success_message = 'Product updated successfully!';
                break;
        }
    }

    if (isset($_GET['error'])) {
        $error_message = $_GET['error'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/font.css">
    <link rel="stylesheet" href="../assets/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
    <style>
        *{
            font-family: Raleway;
        }
        body{
            background-color: #1E1E1E;
        }

        .products-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .product-card.inactive {
            opacity: 0.6;
            border-color: #ffc107;
        }
        
        .inactive-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ffc107;
            color: #212529;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .product-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .product-info p {
            margin: 5px 0;
            color: #666;
        }
        
        .product-price {
            font-size: 18px;
            font-weight: bold;
            color: #2c5282;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            font-size: 14px;
        }
        
        .btn-edit {
            background: #3182ce;
            color: white;
        }
        
        .btn-delete {
            background: #e53e3e;
            color: white;
        }
        
        .btn-reactivate {
            background: #38a169;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .success-message {
            background: #c6f6d5;
            color: #2f855a;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-group textarea {
            height: 80px;
            resize: vertical;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .add-product-btn {
            display: inline-block;
            background: #38a169;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .manag{
            color: white;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include "./nav.php"; ?>

    <div class="products-container">
        <div class="page-header">
            <h1 class="manag">Manage Products</h1>
            <a href="adminpage.php" class="add-product-btn">Add New Product</a>
        </div>

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

        <?php if (empty($products)): ?>
            <p style="text-align: center; color: #666; font-size: 18px;">No products found. <a href="./adminpage.php">Add your first product</a></p>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card <?php echo $product['is_active'] ? '' : 'inactive'; ?>">
                        <?php if (!$product['is_active']): ?>
                            <div class="inactive-badge">INACTIVE</div>
                        <?php endif; ?>
                        
                        <img src="../assets/images/<?php echo htmlspecialchars($product['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                             class="product-image"
                             onerror="this.src='../assets/images/placeholder.jpg'">
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['product_desc']); ?></p>
                            <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                        </div>
                        
                        <div class="product-actions">
                            <button class="btn btn-edit" onclick="openEditModal(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['product_name']); ?>', '<?php echo addslashes($product['product_desc']); ?>', <?php echo $product['price']; ?>)">
                                Edit
                            </button>
                            
                            <?php if ($product['is_active']): ?>
                                <a href="manage_products.php?delete=<?php echo $product['product_id']; ?>" 
                                   class="btn btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete/deactivate this product?')">
                                    Delete
                                </a>
                            <?php else: ?>
                                <a href="manage_products.php?reactivate=<?php echo $product['product_id']; ?>" 
                                   class="btn btn-reactivate" 
                                   onclick="return confirm('Reactivate this product?')">
                                    Reactivate
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" id="edit_product_id">
                
                <div class="form-group">
                    <label>Product Title:</label>
                    <input type="text" name="title" id="edit_title" required>
                </div>
                
                <div class="form-group">
                    <label>Product Description:</label>
                    <textarea name="description" id="edit_description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Price:</label>
                    <input type="number" step="0.01" name="price" id="edit_price" required>
                </div>
                
                <div class="form-group">
                    <label>Product Image (leave empty to keep current image):</label>
                    <input type="file" name="product_image" accept="image/*">
                </div>
                
                <div style="text-align: right;">
                    <button type="button" onclick="closeEditModal()" style="background: #666; color: white; padding: 10px 20px; border: none; border-radius: 4px; margin-right: 10px; cursor: pointer;">Cancel</button>
                    <button type="submit" name="edit_submit" style="background: #3182ce; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Update Product</button>
                </div>
            </form>
        </div>

        <?php include "./pages/footer.php" ?>

    </div>

    <script>
        
        function openEditModal(id, name, description, price) {
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_title').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

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

    const home_redirect =  document.querySelector("#home");

    home_redirect.addEventListener('click', function () {
        window.location = "http://localhost/project-1";
    });

    const add_prod = document.querySelector("#admin");

    add_prod.addEventListener('click', function () {
        window.location = "http://localhost/project-1/pages/adminpage.php"
    })
    </script>
</body>
</html>