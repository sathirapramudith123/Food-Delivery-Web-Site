# 🍔 Food Delivery Web Application - FoodExpress

Welcome to **FoodExpress**, a responsive and interactive food delivery web application built with HTML, CSS, JavaScript, and Bootstrap. This project simulates a modern online food ordering system, offering features like food menu management, customer feedback, cart functionality, and user-friendly navigation.

## 📌 Features

- 🏠 Home Page with modern layout and navigation
- 🍽️ Food Menu Page with search and CRUD operations (Create, Read, Update, Delete)
- 🛒 Shopping Cart with add/remove/update items functionality
- 🧾 Feedback Page to collect customer reviews and ratings
- 🔐 Login and Signup Pages for user access control
- 🧭 Navbar and Footer included on all pages for easy navigation
- 📸 Image file input support for food menu entries
- 🎨 Clean and responsive UI with Bootstrap

## 🛠️ Technologies Used

- HTML5
- CSS3
- JavaScript (Vanilla)
- Bootstrap 5



##mySQL Table 

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    food_id INT NOT NULL,
    food_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total DECIMAL(10,2) AS (price * quantity) STORED,
    FOREIGN KEY (food_id) REFERENCES food_menu(id) ON DELETE CASCADE
);

CREATE TABLE food_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255)
);


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('user', 'delivery', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('completed', 'pending', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Redundant but useful denormalized fields for reporting/display
    food_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    user_name VARCHAR(100) NOT NULL,

    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
); 



CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

