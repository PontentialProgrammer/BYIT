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
        <form style="height:50px; margin-top:10px; display:flex; flex-direction:column; align-contents:center;" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST"><button type="submit" name="button">Add to Cart</button></form>
    </div>
</div>