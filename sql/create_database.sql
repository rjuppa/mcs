-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema mcs
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `mcs` ;

-- -----------------------------------------------------
-- Schema mcs
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mcs` DEFAULT CHARACTER SET utf8 ;
USE `mcs` ;

-- -----------------------------------------------------
-- Table `mcs`.`posts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mcs`.`posts` ;

CREATE TABLE IF NOT EXISTS `mcs`.`posts` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(100) CHARACTER SET 'utf8' NOT NULL,
  `slug` VARCHAR(100) CHARACTER SET 'utf8' NOT NULL,
  `author_id` INT(10) UNSIGNED NOT NULL,
  `abstract` TEXT CHARACTER SET 'utf8' NOT NULL,
  `file` MEDIUMBLOB NULL DEFAULT NULL,
  `file_name` VARCHAR(100) CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `published` TIMESTAMP NULL DEFAULT NULL,
  `published_by_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `deleted` TINYINT(4) NULL DEFAULT '0',
  `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;

CREATE UNIQUE INDEX `UNIQ_SLUG` ON `mcs`.`posts` (`slug` ASC);

CREATE INDEX `FK_POST_AUTHOR` ON `mcs`.`posts` (`author_id` ASC);

CREATE INDEX `FK_POST_PUBLISHER` ON `mcs`.`posts` (`published_by_id` ASC);


-- -----------------------------------------------------
-- Table `mcs`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mcs`.`users` ;

CREATE TABLE IF NOT EXISTS `mcs`.`users` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(50) CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `last_name` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
  `email` VARCHAR(50) CHARACTER SET 'utf8' NOT NULL,
  `password_hash` VARCHAR(100) CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `is_active` BIT(1) NOT NULL DEFAULT b'0',
  `type` TINYINT(4) NOT NULL DEFAULT '1',
  `deleted` BIT(1) NOT NULL DEFAULT b'0',
  `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 16
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;

CREATE UNIQUE INDEX `uniq_email` ON `mcs`.`users` (`email` ASC);


-- -----------------------------------------------------
-- Table `mcs`.`scores`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mcs`.`scores` ;

CREATE TABLE IF NOT EXISTS `mcs`.`scores` (
  `post_id` INT(10) UNSIGNED NOT NULL,
  `reviewer_id` INT(10) UNSIGNED NOT NULL,
  `rating_originality` TINYINT(4) NOT NULL DEFAULT '0',
  `rating_language` TINYINT(4) NOT NULL DEFAULT '0',
  `rating_quality` TINYINT(4) NOT NULL DEFAULT '0',
  `score` DECIMAL(10,0) NOT NULL DEFAULT '0',
  `note` VARCHAR(255) CHARACTER SET 'utf8' NULL DEFAULT NULL,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `FK_SCORE_POST`
    FOREIGN KEY (`post_id`)
    REFERENCES `mcs`.`posts` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `FK_SCORE_USER`
    FOREIGN KEY (`reviewer_id`)
    REFERENCES `mcs`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_czech_ci;

CREATE UNIQUE INDEX `UK_RATING` USING BTREE ON `mcs`.`scores` (`post_id` ASC, `reviewer_id` ASC);

CREATE INDEX `FK_SCORE_USER_idx` ON `mcs`.`scores` (`reviewer_id` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
