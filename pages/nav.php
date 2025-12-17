
<?php
// Program to display URL of current page.
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https";
else 
      $link = "http";
    
// Here append the common URL characters.
$link .= "://";
    
// Append the host(domain name, ip) to the URL.
$link .= $_SERVER['HTTP_HOST'];
    
// Append the requested resource location to the URL
$link .= $_SERVER['REQUEST_URI'];
    
// Print the link


$stmt = $pdo->prepare("SELECT COUNT(*) AS order_num FROM orders ");
$stmt->execute();

$results = $stmt->fetch();

$order_num = $results["order_num"];
?>

<div class="nav">
        <h1 id="home">BYIT</h1>
        <!-- <search class="search">
            <form>
                <input name="fsrch" id="fsrch" placeholder="Search BYIT...">
                <button></button>
            </form>

        </search> -->
        
        <div class="menu-toggle " id="hamburger">
            <span class="line  no-1"></span>
            <span class="no-2  line"></span>
            <span class="no-3  line"></span>
        </div>
        <div class="nav-ele" id="navMenu">
           <?php if(isset($_SESSION["email"]) && $_SESSION["email"] == "user@gmail.com" && $link != "http://localhost/project-1/pages/adminpage.php"): ?>
    <p><a href="#" id="admin">Add Products.</a></p>
    <?php elseif($link == 'http://localhost/project-1/pages/adminpage.php'): ?>
        <p><a href="#" id="manage">Manage Products.</a></p>
        
<?php else: ?>
    <p><a  <?php if(!isset($_SESSION["email"])):?>
                        onclick="account_toggle()"
                        href="#"
                        <?php else:?>
                            href=<?php href_redirect("cart.php") ?>
                        
                    <?php endif;?>>
        <?php if(isset($_SESSION["email"])):?>
            <span><span><?php echo $order_num?></span> Cart</span>
        <?php else:?>
            <svg width="15px" height="15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                <path fill="currentColor" d="M24 48C10.7 48 0 58.7 0 72C0 85.3 10.7 96 24 96L69.3 96C73.2 96 76.5 98.8 77.2 102.6L129.3 388.9C135.5 423.1 165.3 448 200.1 448L456 448C469.3 448 480 437.3 480 424C480 410.7 469.3 400 456 400L200.1 400C188.5 400 178.6 391.7 176.5 380.3L171.4 352L475 352C505.8 352 532.2 330.1 537.9 299.8L568.9 133.9C572.6 114.2 557.5 96 537.4 96L124.7 96L124.3 94C119.5 67.4 96.3 48 69.2 48L24 48zM208 576C234.5 576 256 554.5 256 528C256 501.5 234.5 480 208 480C181.5 480 160 501.5 160 528C160 554.5 181.5 576 208 576zM432 576C458.5 576 480 554.5 480 528C480 501.5 458.5 480 432 480C405.5 480 384 501.5 384 528C384 554.5 405.5 576 432 576z"/>
            </svg>
            <span>Cart</span>
        <?php endif;?>
        
        
    </a></p>
<?php endif; ?>
            <!-- <p><a href="#"><svg width="15px" height="15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="person"><path d="M4.2 19.89c.44-2.7 2.88-5.55 5.88-5.55h3.84c3 0 5.44 2.84 5.88 5.55H4.2zM12 4.1a4.06 4.06 0 110 8.12 4.06 4.06 0 010-8.12zm7.52 10.68a8.45 8.45 0 00-3.27-2.16A6.18 6.18 0 0012 2a6.17 6.17 0 00-4.25 10.63A8.91 8.91 0 002 20.94V22h20v-1.06c0-2.28-.88-4.46-2.48-6.15z"/></svg> Account <svg width="20px" height="20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="arrow-down"><path d="M7.41 8.59 12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg></a></p> -->
            <!-- Account with dropdown -->
            <div class="account-menu">
                <a href="#" id="accountToggle">
                    <svg width="15px" height="15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="person">
                        <path d="M4.2 19.89c.44-2.7 2.88-5.55 5.88-5.55h3.84c3 0 5.44 2.84 5.88 5.55H4.2zM12 4.1a4.06 4.06 0 110 8.12 4.06 4.06 0 010-8.12zm7.52 10.68a8.45 8.45 0 00-3.27-2.16A6.18 6.18 0 0012 2a6.17 6.17 0 00-4.25 10.63A8.91 8.91 0 002 20.94V22h20v-1.06c0-2.28-.88-4.46-2.48-6.15z"/>
                    </svg>
                    Account
                    <svg width="20px" height="20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="arrow-down">
                        <path d="M7.41 8.59 12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                    </svg>
                </a>
                
                    <div class="account-dropdown">
                        <?php if(isset($_SESSION["name"])): ?>
                            <p><?php echo "Hi, " . $_SESSION["name"];?></p>
                            <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post">
                                <!-- <a href="#" style="color: red;">Logout</a> -->
                                <input type="submit"  value="Logout" name="logout">
                            </form>
                        <?php else:?>
                            <a href="./pages/login.php">Login</a>
                            <a href="./pages/signup.php">Sign Up</a>
                        <?php endif;?>
                        
                    </div>
            </div>

            
            
            <p class="help"><a href="#"><svg width="15px" height="15px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="help-outline"><path d="M11 18h2v-2h-2v2zm1-16a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm0-14a4 4 0 0 0-4 4h2c0-1.1.9-2 2-2s2 .9 2 2c0 2-3 1.8-3 5h2c0-2.3 3-2.5 3-5a4 4 0 0 0-4-4z"/></svg> Help <svg width="20px" height="20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="arrow-down"><path d="M7.41 8.59 12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg></a></p>
        </div>
        
    </div>