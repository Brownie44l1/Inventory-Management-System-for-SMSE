CREATE TABLE brands (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status INT(11) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE category (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status INT(11) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE stores (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status INT(11) NOT NULL,
    PRIMARY KEY (id)
);


CREATE TABLE attribute (
    attribute_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    attribute_name VARCHAR(255) NOT NULL,
    status INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE attribute_value (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attribute_id INT NOT NULL,
    value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attribute_id) REFERENCES attribute(id) ON DELETE CASCADE
);

CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    description TEXT,
    color VARCHAR(100),
    size VARCHAR(100),
    brand VARCHAR(100),
    category VARCHAR(100),
    store VARCHAR(100),
    availability ENUM('Yes', 'No') NOT NULL,
);

CREATE TABLE company (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    fee DECIMAL(10, 2) NOT NULL,
    vat_charge DECIMAL(10, 2) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    country VARCHAR(100) NOT NULL,
    message TEXT,
    currency VARCHAR(3) DEFAULT 'NGN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(255) NOT NULL,
    customer_address VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    order_date DATETIME NOT NULL,
    gross_amount DECIMAL(10, 2) NOT NULL,
    vat_amount DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(10, 2) DEFAULT 0,
    net_amount DECIMAL(10, 2) NOT NULL
);

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    fee DECIMAL(10, 2) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product(id) ON DELETE CASCADE
);

CREATE TABLE admin_login (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    phone VARCHAR(15),
    user_group VARCHAR(50) NOT NULL DEFAULT 'Administrator',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    password VARCHAR(255) NOT NULL
);

CREATE TABLE user_login (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    phone VARCHAR(15),
    user_group VARCHAR(50) NOT NULL DEFAULT 'Administrator',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    password VARCHAR(255) NOT NULL
);

CREATE TABLE group_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_name VARCHAR(255) NOT NULL,
    users_view TINYINT(1) DEFAULT 0,
    groups_view TINYINT(1) DEFAULT 0,
    brands_view TINYINT(1) DEFAULT 0,
    category_view TINYINT(1) DEFAULT 0
);



