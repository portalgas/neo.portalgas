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
