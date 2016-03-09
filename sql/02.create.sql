CREATE TABLE `ilc_orange_roles` (
	`id`                                                                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name`                                                              TEXT NOT NULL,
	`description`                                                       TEXT NULL,
	`created_at`                                                        DATETIME NULL,
	`updated_at`                                                        DATETIME NULL,
	CONSTRAINT `pk_ilc_orange_roles` PRIMARY KEY (`id`)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

CREATE TABLE `ilc_orange_role_parents` (
	`child_id`                                                          INT(11) UNSIGNED NOT NULL,
	`parent_id`                                                         INT(11) UNSIGNED NOT NULL,
	CONSTRAINT `pk_ilc_orange_role_parents` PRIMARY KEY (`child_id`, `parent_id`),
	CONSTRAINT `fk_ilc_orange_role_parents_child` FOREIGN KEY (`child_id`) REFERENCES `ilc_orange_roles` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `fk_ilc_orange_role_parents_parent` FOREIGN KEY (`parent_id`) REFERENCES `ilc_orange_roles` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB  default charset=utf8;

CREATE TABLE `ilc_orange_users` (
	`id`                                                                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`username`                                                          TEXT NOT NULL,
	`email`                                                             TEXT NULL,
	`clearpassword`                                                     TEXT NULL,
	`passwd`                                                            TEXT NULL,
	`salt`                                                              TEXT NULL,
	`recoverycode`                                                      TEXT NULL,
	`recoveryexpiration`                                                DATETIME NULL,
	`lockout`                                                           INT(10) UNSIGNED NOT NULL DEFAULT 1,
	`logins`                                                            INT(10) UNSIGNED NOT NULL DEFAULT 0,
	`lastlogin`                                                         DATETIME NULL,
	`lastactivity`                                                      DATETIME NULL,
	`lastname`                                                          TEXT NULL,
	`firstname`                                                         TEXT NULL,
	`phone`                                                             TEXT NULL,
	`mobile`                                                            TEXT NULL,
	`lastname2`                                                         TEXT NULL,
	`firstname2`                                                        TEXT NULL,
	`email2`                                                            TEXT NULL,
	`level`                                                             TEXT NULL,
	`job`                                                               TEXT NULL,
	`birthday`                                                          DATE NULL,
	`infosent`                                                          INT(10) UNSIGNED NOT NULL DEFAULT 1,
	`validunitil`                                                       DATETIME NULL,
	`created_at`                                                        DATETIME NULL,
	`updated_at`                                                        DATETIME NULL,
	CONSTRAINT `pk_ilc_orange_users` PRIMARY KEY (`id`)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

CREATE TABLE `ilc_orange_users_roles` (
	`user_id`                                                           INT(11) UNSIGNED NOT NULL,
	`role_id`                                                           INT(11) UNSIGNED NOT NULL,
	CONSTRAINT `pk_ilc_orange_users_roles` PRIMARY KEY (`user_id`, `role_id`),
	CONSTRAINT `fk_ilc_orange_users_roles_user` FOREIGN KEY (`user_id`) REFERENCES `ilc_orange_users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `fk_ilc_orange_users_roles_role` FOREIGN KEY (`role_id`) REFERENCES `ilc_orange_roles` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB  default charset=utf8;

CREATE TABLE `ilc_orange_groupmodules` (
	`id`                                                                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name`                                                              TEXT NOT NULL,
	`created_at`                                                        DATETIME NULL,
	`updated_at`                                                        DATETIME NULL,
	CONSTRAINT `pk_ilc_orange_groupmodules` PRIMARY KEY (`id`)
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

CREATE TABLE `ilc_orange_moduleformations` (
	`id`                                                                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`gm_id`                                                             INT(11) UNSIGNED NOT NULL,
	`code`                                                              TEXT NOT NULL,
	`title`                                                             TEXT NOT NULL,
	`description`                                                       TEXT NULL,
	`created_at`                                                        DATETIME NULL,
	`updated_at`                                                        DATETIME NULL,
	CONSTRAINT `pk_ilc_orange_moduleformations` PRIMARY KEY (`id`),
	CONSTRAINT `fk_ilc_orange_moduleformations_gm` FOREIGN KEY (`gm_id`) REFERENCES `ilc_orange_groupmodules` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

CREATE TABLE `ilc_orange_modulepreinscriptions` (
	`id`                                                                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`mf_id`                                                             INT(11) UNSIGNED NOT NULL,
	`user_id`                                                           INT(11) UNSIGNED NOT NULL,
	`lockout`                                                           INT(10) UNSIGNED NOT NULL DEFAULT 1,
	`created_at`                                                        DATETIME NULL,
	`updated_at`                                                        DATETIME NULL,
	CONSTRAINT `pk_ilc_orange_modulepreinscription` PRIMARY KEY (`id`),
	CONSTRAINT `fk_ilc_orange_modulepreinscriptions_mf` FOREIGN KEY (`mf_id`) REFERENCES `ilc_orange_moduleformations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `fk_ilc_orange_modulepreinscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `ilc_orange_users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

CREATE TABLE `ilc_orange_sessionformations` (
	`id`                                                                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`mf_id`                                                             INT(11) UNSIGNED NOT NULL,
	`code`                                                              TEXT NOT NULL,
	`title`                                                             TEXT NOT NULL,
	`dtstart`                                                           TEXT NOT NULL,
	`location`                                                          TEXT NOT NULL,
	`phonecontactcenter`                                                TEXT NULL,
	`conditionsreport`                                                  TEXT NULL,
	`dateinfo`                                                          TEXT NULL,
	`otherinfo`                                                         TEXT NULL,
	`maxparticipants`                                                   INT(11) UNSIGNED NOT NULL DEFAULT 0,
	`lockout`                                                           INT(10) UNSIGNED NOT NULL DEFAULT 1,
	`created_at`                                                        DATETIME NULL,
	`updated_at`                                                        DATETIME NULL,
	CONSTRAINT `pk_ilc_orange_sessionformations` PRIMARY KEY (`id`),
	CONSTRAINT `fk_ilc_orange_sessionformations_mf` FOREIGN KEY (`mf_id`) REFERENCES `ilc_orange_moduleformations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

CREATE TABLE `ilc_orange_sessioninscriptions` (
	`id`                                                                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sf_id`                                                             INT(11) UNSIGNED NOT NULL,
	`user_id`                                                           INT(11) UNSIGNED NOT NULL,
	`convocation`                                                       INT(10) UNSIGNED NOT NULL DEFAULT 1,
	`created_at`                                                        DATETIME NULL,
	`updated_at`                                                        DATETIME NULL,
	CONSTRAINT `pk_ilc_orange_sessioninscriptions` PRIMARY KEY (`id`),
	CONSTRAINT `fk_ilc_orange_sessioninscriptions_sf` FOREIGN KEY (`sf_id`) REFERENCES `ilc_orange_sessionformations` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
	CONSTRAINT `fk_ilc_orange_sessioninscriptions_user` FOREIGN KEY (`user_id`) REFERENCES `ilc_orange_users` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
) engine=InnoDB  default charset=utf8 auto_increment=1 ;

