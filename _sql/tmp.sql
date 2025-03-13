CREATE TABLE `cms_menu_types` (
                                              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                              `code` VARCHAR(15) NULL,
                                              `name` VARCHAR(75) NOT NULL,
                                              `descri` TEXT NULL,
                                              `created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                                              `modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                                              PRIMARY KEY (`id`));

ALTER TABLE `portalgas`.`cms_menu_types`
    ADD UNIQUE INDEX `unique code` (`code` ASC);

INSERT INTO `cms_menu_types` (`code`, `name`) VALUES ('PAGE', 'Pagina');
INSERT INTO `cms_menu_types` (`code`, `name`) VALUES ('DOC', 'Documento');
INSERT INTO `cms_menu_types` (`code`, `name`) VALUES ('LINK_EXT', 'Link pagina esterna');

drop  TABLE `cms_menus`;
CREATE TABLE `cms_menus` (
                                  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                  `organization_id` int(11) not NULL,
                                  `cms_menu_type_id` int(11) not NULL,
                                  `name` VARCHAR(75) NOT NULL,
                                  `options` TEXT NULL,
                                  `created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                                  `modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                                  PRIMARY KEY (`id`));
drop  TABLE `cms_pages`;
CREATE TABLE `cms_pages` (
                             `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                             `organization_id` int(11) not NULL,
                             `cms_menu_id` int(11) not NULL,
                             `name` VARCHAR(75) NOT NULL,
                             `body` TEXT NULL,
                             `created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                             `modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                             PRIMARY KEY (`id`));

drop  TABLE `cms_page_images`;
CREATE TABLE `cms_page_images` (
                             `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                             `organization_id` int(11) not NULL,
                             `cms_page_id` int(11) not NULL,
                             `name` VARCHAR(75) NOT NULL,
                             `path` TEXT NULL,
                              ext VARCHAR(75) NOT NULL,
                              sort int(11) default 0,
                             `created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                             `modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                             PRIMARY KEY (`id`));


CREATE TABLE `cms_menu_docs` (
                                   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                   `organization_id` int(11) not NULL,
                                   `cms_menu_id` int(11) not NULL,
                                   `name` VARCHAR(75) NOT NULL,
                                   `path` TEXT NULL,
                                   ext VARCHAR(75) NOT NULL,
                                   `created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                                   `modified` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
                                   PRIMARY KEY (`id`));
