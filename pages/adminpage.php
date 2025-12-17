<?php 
    include "../includes/functions.php";
    include "../includes/database.php";
    session_start();

    if (isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        
        // Handle file upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            
            // Define upload directory
            $upload_dir = "../assets/images/";
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Get file information
            $file_name = $_FILES['product_image']['name'];
            $file_tmp = $_FILES['product_image']['tmp_name'];
            $file_size = $_FILES['product_image']['size'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Generate unique filename
            $new_filename = uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            // Validate file type
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_ext, $allowed_extensions)) {
                // Validate file size (5MB max)
                if ($file_size <= 5000000) {
                    // Move uploaded file
                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Insert into database
                        $stmt = $pdo->prepare("INSERT INTO products (product_name, product_desc, price, image_path) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$title, $description, $price, $new_filename]);
                        
                        echo "<script>alert('Product added successfully!');</script>";
                    } else {
                        echo "<script>alert('Failed to upload image.');</script>";
                    }
                } else {
                    echo "<script>alert('File size too large. Maximum 5MB allowed.');</script>";
                }
            } else {
                echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP allowed.');</script>";
            }
        } else {
            echo "<script>alert('Please select an image.');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/font.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/font.css">
    <title>Admin - Add Products</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .admin-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .admin-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .admin-header h1 {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .admin-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }

        .product_entry {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e9ecef;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .product_entry label {
            display: block;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .product_entry input[type="text"],
        .product_entry input[type="file"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .product_entry input[type="text"]:focus,
        .product_entry input[type="file"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .product_entry input[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        .product_entry button {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 14px 32px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product_entry button:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }

        .product_entry button:active {
            transform: translateY(0);
        }

        /* File input styling */
        input[type="file"]::-webkit-file-upload-button {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            background: #e9ecef;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .admin-container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .product_entry {
                padding: 25px;
            }

            .admin-header h1 {
                font-size: 2rem;
            }
        }

        /* Success/Error message styling */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php 
        include "./nav.php";
    ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Add New Product</h1>
            <p>Fill out the form below to add a new product to your inventory</p>
        </div>

        <form action="adminpage.php" class="product_entry" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Product Title:</label>
                <input type="text" name="title" id="title" placeholder="Enter product name" required>
            </div>

            <div class="form-group">
                <label for="description">Product Description:</label>
                <input type="text" name="description" id="description" placeholder="Brief description of the product" required>
            </div>

            <div class="form-group">
                <label for="price">Product Price:</label>
                <input type="text" name="price" id="price" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label for="product_image">Product Image:</label>
                <input type="file" name="product_image" id="product_image" accept="image/*" required>
            </div>
        
            <button type="submit" name="submit">Add Product</button>
        </form>
    </div>
    <?php include "./pages/footer.php" ?>

    <script>
        
        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Alternative: Clear form after successful submission
        document.addEventListener('DOMContentLoaded', function() {
            // Check if page was reloaded after form submission
            if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
                // Clear the form
                document.querySelector('.product_entry').reset();
            }
        });

        const home_redirect = document.querySelector("#home");
        if (home_redirect) {
            home_redirect.addEventListener('click', function () {
                window.location = "http://localhost/project-1";
            });
        }

        const hamburger = document.getElementById("hamburger");
        const navMenu = document.getElementById("navMenu");

        hamburger.addEventListener("click", function() {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("show");
            document.querySelector(".no-1").classList.toggle("clicked");
            document.querySelector(".no-2").classList.toggle("clicked");
            document.querySelector(".no-3").classList.toggle("clicked");


        });
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

const manage_redirect = document.querySelector("#manage");

manage_redirect.addEventListener('click', function () {
    window.location = "http://localhost/project-1/pages/manage_products.php"

})
    </script>
</body>
</html>