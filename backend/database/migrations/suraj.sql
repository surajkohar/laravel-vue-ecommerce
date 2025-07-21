ALTER TABLE `roles`
ADD `status` tinyint NOT NULL DEFAULT '1' AFTER `name`;

ALTER TABLE `permissions`
ADD `status` tinyint NOT NULL DEFAULT '1' AFTER `name`;

ALTER TABLE `users`
ADD `status` tinyint NOT NULL DEFAULT '1' AFTER `email`;

ALTER TABLE `users`
ADD `phone` varchar(255) NULL AFTER `status`;

INSERT INTO `permissions` (`name`, `status`, `guard_name`, `created_at`, `updated_at`) VALUES
('view dashboard', 1, 'web', NOW(), NOW()),
('view roles', 1, 'web', NOW(), NOW()),
('edit role', 1, 'web', NOW(), NOW()),
('delete role', 1, 'web', NOW(), NOW()),
('view permission', 1, 'web', NOW(), NOW()),
('add permission', 1, 'web', NOW(), NOW()),
('edit permission', 1, 'web', NOW(), NOW()),
('delete permission', 1, 'web', NOW(), NOW()),
('view user', 1, 'web', NOW(), NOW()),
('add user', 1, 'web', NOW(), NOW()),
('edit user', 1, 'web', NOW(), NOW()),
('delete user', 1, 'web', NOW(), NOW()),
('view products', 1, 'web', NOW(), NOW()),
('add product', 1, 'web', NOW(), NOW()),
('edit product', 1, 'web', NOW(), NOW()),
('delete product', 1, 'web', NOW(), NOW()),
('view orders', 1, 'web', NOW(), NOW()),
('process orders', 1, 'web', NOW(), NOW()),
('cancel orders', 1, 'web', NOW(), NOW());

ALTER TABLE `users`
ADD `is_admin` int NULL AFTER `phone`;

CREATE TABLE `sizes` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `type` varchar(255) NULL,
  `size_title` varchar(100) NOT NULL,
  `created` timestamp NULL,
  `modified` timestamp NULL
);

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    gender VARCHAR(50),
    description TEXT,
    purchase_price NUMERIC(10, 2),
    price NUMERIC(10, 2),
    sku VARCHAR(100),
    stock INT,
    category_id INT REFERENCES categories(id),
    category_slug VARCHAR(255),
    main_image_name VARCHAR(255),
    main_image_type VARCHAR(100),
    main_image_size INT,
    size_guide_name VARCHAR(255),
    size_guide_type VARCHAR(100),
    size_guide_size INT,
    created TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE product_subcategories (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES products(id) ON DELETE CASCADE,
    subcategory_id INT NOT NULL
);


CREATE TABLE product_variants (
    id SERIAL PRIMARY KEY,
    product_id INT REFERENCES products(id) ON DELETE CASCADE,
    color VARCHAR(20),
    color_name VARCHAR(50)
);


CREATE TABLE product_variant_sizes (
    id SERIAL PRIMARY KEY,
    variant_id INT REFERENCES product_variants(id) ON DELETE CASCADE,
    size_id INT REFERENCES sizes(id)
);


CREATE TABLE product_variant_images (
    id SERIAL PRIMARY KEY,
    variant_id INT REFERENCES product_variants(id) ON DELETE CASCADE,
    image_name VARCHAR(255),
    image_type VARCHAR(100),
    image_size INT
);


CREATE TABLE sizes (
    id SERIAL PRIMARY KEY,
    size_title VARCHAR(50),
    type VARCHAR(50), -- e.g., clothing/shoes
    created TIMESTAMP,
    modified TIMESTAMP
);

ALTER TABLE `products`
ADD `status` tinyint NULL DEFAULT '1' AFTER `size_guide_size`;

