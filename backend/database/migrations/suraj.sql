ALTER TABLE `roles`
ADD `status` tinyint NOT NULL DEFAULT '1' AFTER `name`;

ALTER TABLE `permissions`
ADD `status` tinyint NOT NULL DEFAULT '1' AFTER `name`;

ALTER TABLE `users`
ADD `status` tinyint NOT NULL DEFAULT '1' AFTER `email`;

ALTER TABLE `users`
ADD `phone` varchar(255) NULL AFTER `status`;
