DROP TABLE IF EXISTS `movements`;
CREATE TABLE `movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `movement_type_id` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `supplier_organization_id` bigint(20) DEFAULT NULL,
  `year` int(4) UNSIGNED NOT NULL,
  `name` varchar(75) NOT NULL,
  `descri` text DEFAULT NULL,
  `importo` double(11,2) NOT NULL DEFAULT 0.00,
  `date` date NOT NULL,
  `payment_type` enum('CONTANTI','BONIFICO','SATISPAY','CASSA','ALTRO') DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

DROP TABLE IF EXISTS `movement_types`;
CREATE TABLE `movement_types` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `model` enum('USERS','SUPPLIERS','INVOICE') DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0,
  `created` datetime DEFAULT current_timestamp(),
  `modified` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `movement_types` (`id`, `name`, `is_active`, `is_system`, `model`, `sort`, `created`, `modified`) VALUES
(1, 'Spesa del G.A.S.', 1, 1, NULL, 10, '2023-03-28 21:38:08', '2023-03-28 21:38:08'),
(2, 'Entrata del G.A.S.', 1, 1, NULL, 20, '2023-03-28 21:38:37', '2023-03-28 21:38:37'),
(3, 'Sconto al fornitore', 1, 1, 'SUPPLIERS', 30, '2023-03-28 21:39:08', '2023-03-28 21:39:08'),
(4, 'Accredito dal fornitore', 1, 1, 'SUPPLIERS', 40, '2023-03-28 21:39:29', '2023-03-28 21:39:29'),
(5, 'Pagamento fattura a fornitore', 1, 1, 'INVOICE', 50, '2023-03-28 21:39:58', '2023-03-28 21:39:58'),
(6, 'Rimborso Gasista', 1, 1, 'USERS', 60, '2023-03-28 21:39:58', '2023-03-28 21:39:58'),
(7, 'Movimento di cassa', 1, 1, 'USERS', 70, '2023-03-28 21:39:58', '2023-03-28 21:39:58');

ALTER TABLE `movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_organization` (`organization_id`);

ALTER TABLE `movement_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `movement_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `k_orders_actions` 
ADD COLUMN `permissions` TEXT NULL AFTER `permission_or`;

ALTER TABLE `k_orders_actions` 
ADD COLUMN `neo_url` VARCHAR(75) NULL AFTER `action`;

ALTER TABLE `k_orders_actions` 
CHANGE COLUMN `permission_or` `permission_or` VARCHAR(512) NULL DEFAULT NULL ;

ALTER TABLE `k_orders_actions` 
CHANGE COLUMN `query_string` `query_string` VARCHAR(100) NULL DEFAULT NULL ;

ALTER TABLE `k_orders_actions` 
CHANGE COLUMN `label_more` `label_more` VARCHAR(25) NULL DEFAULT NULL ;

UPDATE `k_orders_actions` SET `permissions` = '[{\"isProdGasPromotion\":\"N\"}]' WHERE (`id` = '1');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"articlesOwner\":\"REFERENT\",\"orderIsDes\":\"N\",\"isProdGasPromotion\":\"N\"},{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"orderIsDes\":\"Y\",\"isTitolareDesSupplier\":\"Y\"}]' WHERE (`id` = '4');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasPayToDelivery\":\"POST\"}]' WHERE (`id` = '31');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"isProdGasPromotion\":\"N\"}]' WHERE (`id` = '6');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"articlesOwner\":\"DES\",\"orderIsDes\":\"Y\",\"orgHasDes\":\"Y\",\"isTitolareDesSupplier\":\"N\",\"isProdGasPromotion\":\"N\"},{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"articlesOwner\":\"DES\",\"orderIsDes\":\"N\",\"isProdGasPromotion\":\"N\"}]' WHERE (`id` = '7');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"N\",\"orderIsDes\":\"N\"},{\"orgHasArticlesOrder\":\"N\",\"userHasArticlesOrder\":\"N\",\"orderIsDes\":\"N\"}]' WHERE (`id` = '9');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orderTypeGest\":\"AGGREGATE\"}]' WHERE (`id` = '11');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orderTypeGest\":\"SPLIT\"}]' WHERE (`id` = '12');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasTrasport\":\"Y\",\"orderHasTrasport\":\"Y\"}]' WHERE (`id` = '13');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasValidate\":\"Y\",\"isToValidate\":true}]' WHERE (`id` = '14');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasValidate\":\"Y\",\"isToValidate\":true}]' WHERE (`id` = '15');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasStoreroom\":\"Y\",\"isCartToStoreroom\":true}]' WHERE (`id` = '17');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasPayToDelivery\":\"POST\"},{\"orgHasPayToDelivery\":\"ON-POST\"}]' WHERE (`id` = '18');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasPayToDelivery\":\"ON-POST\"}]' WHERE (`id` = '19');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasPayToDelivery\":\"POST\"},{\"orgHasPayToDelivery\":\"ON-POST\"}]' WHERE (`id` = '20');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasPayToDelivery\":\"ON\"},{\"orgHasPayToDelivery\":\"ON-POST\"}]' WHERE (`id` = '24');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasPayToDelivery\":\"ON\"},{\"orgHasPayToDelivery\":\"ON-POST\"}]' WHERE (`id` = '25');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasPayToDelivery\":\"ON\"},{\"orgHasPayToDelivery\":\"ON-POST\"}]' WHERE (`id` = '26');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasPayToDelivery\":\"ON\"},{\"orgHasPayToDelivery\":\"ON-POST\"}]' WHERE (`id` = '27');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasCostMore\":\"Y\",\"orderHasCostMore\":\"Y\"}]' WHERE (`id` = '28');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasCostLess\":\"Y\",\"orderHasCostLess\":\"Y\"}]' WHERE (`id` = '29');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"orderIsDes\":\"N\",\"isProdGasPromotion\":\"Y\"}]' WHERE (`id` = '30');
UPDATE `k_orders_actions` SET `permissions` = '[{\"isProdGasPromotion\":\"Y\"}]' WHERE (`id` = '34');
UPDATE `k_orders_actions` SET `permissions` = '[{\"isProdGasPromotion\":\"Y\"}]' WHERE (`id` = '35');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasOrdersGdxp\":\"Y\"}]' WHERE (`id` = '38');
UPDATE `k_orders_actions` SET `permissions` = '[{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"articlesOwner\":\"SUPPLIER\",\"orderIsDes\":\"Y\",\"orgHasDes\":\"Y\",\"isTitolareDesSupplier\":\"Y\"},{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"articlesOwner\":\"SUPPLIER\",\"orderIsDes\":\"N\",\"isProdGasPromotion\":\"N\"}]' WHERE (`id` = '36');

INSERT INTO `k_orders_actions` (`controller`, `action`, `neo_url`, `permission`, `permissions`, `flag_menu`, `label`, `css_class`, `img`) VALUES ('Orders', 'edit', '/admin/orders/edit/{order_type_id}/{order_id}/{parent_id}', '{"isOrderType": 10, "isOrderType": 11}', '[{"isOrderType": 10, "isOrderType": 11}]', 'Y', 'Edit Order Short', 'actionEdit', 'edit.jpg');
UPDATE `k_orders_actions` SET `neo_url` = '', `permission` = '{"isOrderType": 1, "isOrderType": 2,"isOrderType": 3, "isOrderType": 7, "isOrderType": 9}', `permissions` = '[{"isOrderType": 1, "isOrderType": 2,"isOrderType": 3, "isOrderType": 7, "isOrderType": 9}]' WHERE (`id` = '1');


UPDATE `k_orders_actions` SET `permission` = '{"isOrderTypes": "1,2,3,7,9"}', `permissions` = '[{"isOrderTypes": "1,2,3,7,9"}]' WHERE (`id` = '1');
UPDATE `k_orders_actions` SET `permission` = '{"isOrderTypes": "10,11"}', `permissions` = '[{"isOrderTypes": "10,11"}]' WHERE (`id` = '39');



UPDATE `k_orders_actions` SET `neo_url` = '/admin/orders/edit/{order_type_id}/{order_id}/{parent_id}' WHERE (`id` = '1');

INSERT INTO `k_templates_orders_states_orders_actions`
(`template_id`,
`group_id`,
`state_code`,
`order_action_id`,
`sort`)
select 
`template_id`,
`group_id`,
`state_code`,
39,
`sort` from k_templates_orders_states_orders_actions where order_action_id = 1;


INSERT INTO `k_orders_actions` (`controller`, `action`, `neo_url`, `permission`, `permissions`, `flag_menu`, `label`, `css_class`, `img`) VALUES ('ArticlesOrders', 'index', '/admin/articles-orders/index/{order_type_id}/{order_id}', '{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"isOrderTypes\": \"10,11\"}', '[{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"isOrderTypes\": \"10,11\"}]', 'Y', 'Edit ArticlesOrder Short', 'actionEditCart', 'legno-frutta-cassetta.jpg');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"isOrderTypes\": \"1,2,3,7,9\"}', `permissions` = '[{\"orgHasArticlesOrder\":\"Y\",\"userHasArticlesOrder\":\"Y\",\"isOrderTypes\": \"1,2,3,7,9\"}]' WHERE (`id` = '6');


INSERT INTO `k_templates_orders_states_orders_actions`
(`template_id`,
`group_id`,
`state_code`,
`order_action_id`,
`sort`)
select 
`template_id`,
`group_id`,
`state_code`,
40,
`sort` from k_templates_orders_states_orders_actions where order_action_id = 6;

INSERT INTO `k_orders_actions` (`controller`, `action`, `neo_url`, `permission`, `permissions`, `flag_menu`, `label`, `css_class`, `img`) VALUES ('Orders', 'neo-add', '/admin/orders/add/10/{order_id}', '{\"isOrderTypes\": \"11\"}', '[{\"isOrderTypes\": \"11\"}]', 'Y', 'Add OrderGasGroup', 'actionAdd', 'trenino.jpg');


INSERT INTO `k_templates_orders_states_orders_actions`
(`template_id`,
`group_id`,
`state_code`,
`order_action_id`,
`sort`)
select 
`template_id`,
`group_id`,
`state_code`,
41,
`sort` from k_templates_orders_states_orders_actions where order_action_id = 6;

UPDATE `k_orders_actions` SET `permission` = '{\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permission_or` = '', `permissions` = '[{\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '10');
UPDATE `k_orders_actions` SET `permission` = '{\"orderTypeGest\":\"AGGREGATE\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orderTypeGest\":\"AGGREGATE\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '11');
UPDATE `k_orders_actions` SET `permission` = '{\"orderTypeGest\":\"SPLIT\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orderTypeGest\":\"SPLIT\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '12');
UPDATE `k_orders_actions` SET `permission` = '{\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '16');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasPayToDelivery\":\"POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permission_or` = '{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orgHasPayToDelivery\":\"POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"},{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '18');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permission_or` = '{\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '19');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasPayToDelivery\":\"POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permission_or` = '{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orgHasPayToDelivery\":\"POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"},{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '20');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasPayToDelivery\":\"POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orgHasPayToDelivery\":\"POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '31');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasPayToDelivery\":\"ON\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permission_or` = '{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orgHasPayToDelivery\":\"ON\",\"isOrderTypes\": \"1,2,3,7,9,10\"},{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '24');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasPayToDelivery\":\"ON\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permission_or` = '{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orgHasPayToDelivery\":\"ON\",\"isOrderTypes\": \"1,2,3,7,9,10\"},{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '25');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasPayToDelivery\":\"ON\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permission_or` = '{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orgHasPayToDelivery\":\"ON\",\"isOrderTypes\": \"1,2,3,7,9,10\"},{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '26');
UPDATE `k_orders_actions` SET `permission` = '{\"orgHasPayToDelivery\":\"ON\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permission_or` = '{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}', `permissions` = '[{\"orgHasPayToDelivery\":\"ON\",\"isOrderTypes\": \"1,2,3,7,9,10\"},{\"orgHasPayToDelivery\":\"ON-POST\",\"isOrderTypes\": \"1,2,3,7,9,10\"}]' WHERE (`id` = '27');

INSERT INTO `k_orders_actions` (`controller`, `action`, `neo_url`, `permission`, `permissions`, `flag_menu`, `label`, `css_class`, `img`) VALUES ('Docs', 'referentDocsExport', '/admin/referent-docs-export/index/{order_type_id}/{order_id}', '{\"isOrderTypes\": \"11\"}', '[{\"isOrderTypes\": \"11\"}]', 'Y', 'Print Doc', 'actionPrinter', 'lista.jpg');
INSERT INTO `k_templates_orders_states_orders_actions`
(`template_id`,
`group_id`,
`state_code`,
`order_action_id`,
`sort`)
select 
`template_id`,
`group_id`,
`state_code`,
42,
`sort` from k_templates_orders_states_orders_actions where order_action_id = 16;

UPDATE `k_orders_actions` SET `action` = 'neo-index' WHERE (`id` = '40');
UPDATE `k_orders_actions` SET `action` = 'neo-edit' WHERE (`id` = '39');
UPDATE `k_orders_actions` SET `action` = 'neo-referentDocsExport' WHERE (`id` = '42');

delete FROM k_templates_orders_states_orders_actions where group_id = 19;

UPDATE `k_orders_actions` SET `neo_url` = NULL WHERE (`id` = '1');
