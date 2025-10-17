# BYIT - Mini E-Commerce Platform

A lightweight, full-stack e-commerce web application built with vanilla PHP, MySQL, and JavaScript. BYIT (Build Your IT) provides essential shopping cart functionality without the overhead of heavy frameworks.

## 🚀 Features

- **User Authentication**: Secure registration and login system with password hashing
- **Product Catalog**: Browse products with search and category filtering
- **Shopping Cart**: Add, update, and remove items with real-time price calculations
- **Order Management**: near-omplete checkout process with order history tracking
- **Admin Dashboard**: Manage products, inventory, and customer orders
- **Responsive Design**: Mobile-friendly interface using vanilla CSS

## 🛠️ Tech Stack

- **Backend**: PHP (Vanilla)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **Architecture**: MVC-inspired structure with separation of concerns

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Web browser with JavaScript enabled

## ⚙️ Installation

1. Clone the repository:
```bash
git clone https://github.com/PontentialProgrammer/BYIT.git
cd BYIT
```

2. Import the database:
```bash
mysql -u root -p < schema.sql
```

3. Configure database connection in `includes/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'eComm');
```

4. Start your local server:
```bash
php -S localhost:8000
```

5. Access the application at `http://localhost:8000`



## 🔐 Security Features

- SQL injection prevention using prepared statements
- XSS protection with input sanitization
- CSRF token validation for forms
- Password hashing with PHP's `password_hash()`
- Session management with secure cookies

## 🎯 Use Cases

Perfect for learning web development fundamentals, understanding e-commerce workflows, or as a foundation for more complex shopping platforms.

## 📝 Future Enhancements

- Payment gateway integration (Stripe/PayPal)
- Email notifications for orders
- Product reviews and ratings
- Advanced analytics dashboard
- REST API for mobile app integration

## 👨‍💻 Author

**Ayotomiwa David Oyewumi**
- GitHub: (https://github.com/PontentialProgrammer)
- LinkedIn: (https://www.linkedin.com/in/ayotomiwa-oyewumi-300b45285/)

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

Built as part of my web development internship at VAS2NETS Technologies Limited.
