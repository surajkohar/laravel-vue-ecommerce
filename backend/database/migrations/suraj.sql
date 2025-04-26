ALTER TABLE `roles`
ADD `status` tinyint NOT NULL DEFAULT '1' AFTER `name`;

ALTER TABLE `permissions`
ADD `status` tinyint NOT NULL DEFAULT '1' AFTER `name`;
