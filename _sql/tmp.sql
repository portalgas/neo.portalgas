-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3310
-- Creato il: Mar 14, 2025 alle 12:17
-- Versione del server: 10.0.38-MariaDB-1~xenial
-- Versione PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

--
-- Database: `portalgas`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_docs`
--

DROP TABLE IF EXISTS `cms_docs`;
CREATE TABLE `cms_docs` (
                            `id` int(10) UNSIGNED NOT NULL,
                            `organization_id` int(11) NOT NULL,
                            `cms_menu_id` int(11) DEFAULT NULL,
                            `name` VARCHAR(256) NOT NULL,
                            `path` VARCHAR(256) DEFAULT NULL,
                            `ext` varchar(75) NOT NULL,
                            `size` DOUBLE NULL DEFAULT '0.00',
                            `created` datetime DEFAULT CURRENT_TIMESTAMP,
                            `modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_menus`
--

DROP TABLE IF EXISTS `cms_menus`;
CREATE TABLE `cms_menus` (
                             `id` int(10) UNSIGNED NOT NULL,
                             `organization_id` int(11) NOT NULL,
                             `cms_menu_type_id` int(11) NOT NULL,
                             `name` varchar(75) NOT NULL,
                             `options` text,
                             `sort` int(11) DEFAULT '0',
                             `is_public` tinyint(1) NOT NULL DEFAULT '0',
                             `is_system` tinyint(1) NOT NULL DEFAULT '0',
                             `is_active` tinyint(1) NOT NULL DEFAULT '0',
                             `created` datetime DEFAULT CURRENT_TIMESTAMP,
                             `modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cms_menus`
--

INSERT INTO `cms_menus` (`id`, `organization_id`, `cms_menu_type_id`, `name`, `options`, `sort`, `is_public`, `is_system`, `is_active`, `created`, `modified`) VALUES
                                                                                                                                                                   (1, 37, 1, 'home gas', '', 0, 1, 0, 1, '2025-03-13 13:56:21', '2025-03-13 13:56:21'),
                                                                                                                                                                   (2, 37, 2, 'pdf', '', 1, 0, 1, 0, '2025-03-13 15:50:56', '2025-03-13 15:50:56');

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_menu_types`
--

DROP TABLE IF EXISTS `cms_menu_types`;
CREATE TABLE `cms_menu_types` (
                                  `id` int(10) UNSIGNED NOT NULL,
                                  `code` varchar(15) DEFAULT NULL,
                                  `name` varchar(75) NOT NULL,
                                  `descri` text,
                                  `created` datetime DEFAULT CURRENT_TIMESTAMP,
                                  `modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cms_menu_types`
--

INSERT INTO `cms_menu_types` (`id`, `code`, `name`, `descri`, `created`, `modified`) VALUES
                                                                                         (1, 'PAGE', 'Pagina', NULL, '2025-03-12 14:01:50', '2025-03-12 14:01:50'),
                                                                                         (2, 'DOC', 'Documento', NULL, '2025-03-12 14:01:50', '2025-03-12 14:01:50'),
                                                                                         (3, 'LINK_EXT', 'Link pagina esterna', NULL, '2025-03-12 14:01:50', '2025-03-12 14:01:50');

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_pages`
--

DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE `cms_pages` (
                             `id` int(10) UNSIGNED NOT NULL,
                             `organization_id` int(11) NOT NULL,
                             `cms_menu_id` int(11) DEFAULT NULL,
                             `name` varchar(75) NOT NULL,
                             `body` text,
                             `created` datetime DEFAULT CURRENT_TIMESTAMP,
                             `modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `cms_pages`
--

INSERT INTO `cms_pages` (`id`, `organization_id`, `cms_menu_id`, `name`, `body`, `created`, `modified`) VALUES
    (1, 37, 1, 'home', '<p><img alt=\"\" src=\"https://picsum.photos/id/237/200/300\"></p><p>\r\n<strong>Lorem Ipsum</strong> is simply dummy text of the printing and \r\ntypesetting industry. Lorem Ipsum has been the industry\'s standard dummy\r\n text ever since the 1500s, when an unknown printer took a galley of \r\ntype and scrambled it to make a type specimen book. It has survived not \r\nonly five centuries, but also the leap into electronic typesetting, \r\nremaining essentially unchanged. It was popularised in the 1960s with \r\nthe release of Letraset sheets containing Lorem Ipsum passages, and more\r\n recently with desktop publishing software like Aldus PageMaker \r\nincluding versions of Lorem Ipsum&nbsp;</p><p><br></p><p><a target=\"_blank\" rel=\"nofollow\" href=\"https://www.portalgas.it\">https://www.portalgas.it/</a> <br></p>', '2025-03-13 13:58:42', '2025-03-13 13:58:42');

-- --------------------------------------------------------

--
-- Struttura della tabella `cms_page_images`
--

DROP TABLE IF EXISTS `cms_page_images`;
CREATE TABLE `cms_page_images` (
                                   `id` int(10) UNSIGNED NOT NULL,
                                   `organization_id` int(11) NOT NULL,
                                   `cms_page_id` int(11) NOT NULL,
                                   `name` VARCHAR(256) NOT NULL,
                                   `path` VARCHAR(256) DEFAULT NULL,
                                   `ext` varchar(75) NOT NULL,
                                   `size` DOUBLE NULL DEFAULT '0.00' ;
                                   `sort` int(11) DEFAULT '0',
                                   `created` datetime DEFAULT CURRENT_TIMESTAMP,
                                   `modified` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `cms_docs`
--
ALTER TABLE `cms_docs`
    ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `cms_menus`
--
ALTER TABLE `cms_menus`
    ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `cms_menu_types`
--
ALTER TABLE `cms_menu_types`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique code` (`code`);

--
-- Indici per le tabelle `cms_pages`
--
ALTER TABLE `cms_pages`
    ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `cms_page_images`
--
ALTER TABLE `cms_page_images`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `cms_docs`
--
ALTER TABLE `cms_docs`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `cms_menus`
--
ALTER TABLE `cms_menus`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `cms_menu_types`
--
ALTER TABLE `cms_menu_types`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `cms_pages`
--
ALTER TABLE `cms_pages`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `cms_page_images`
--
ALTER TABLE `cms_page_images`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
