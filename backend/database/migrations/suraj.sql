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
