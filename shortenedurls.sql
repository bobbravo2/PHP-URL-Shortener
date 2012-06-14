SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `php_url_shortener` ;
CREATE SCHEMA IF NOT EXISTS `php_url_shortener` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `php_url_shortener` ;

-- -----------------------------------------------------
-- Table `url`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `url` ;

CREATE  TABLE IF NOT EXISTS `url` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `long_url` VARCHAR(255) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `remote_ip` CHAR(15) NOT NULL COMMENT 'ip address used to shorten URL' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `long` (`long_url` ASC) )
ENGINE = InnoDB


-- -----------------------------------------------------
-- Table `click`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `click` ;

CREATE  TABLE IF NOT EXISTS `click` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `url_id` INT UNSIGNED NOT NULL ,
  `time` DATETIME NULL ,
  `ua` TEXT NULL COMMENT 'user agent string' ,
  `referrer` TEXT NULL COMMENT 'Referrer String' ,
  `remote_ip` CHAR(15) NOT NULL COMMENT 'IP address', 
  PRIMARY KEY (`id`, `url_id`) ,
  INDEX `fk_click_url` (`url_id` ASC) ,
  CONSTRAINT `fk_click_url`
    FOREIGN KEY (`url_id` )
    REFERENCES `url` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
