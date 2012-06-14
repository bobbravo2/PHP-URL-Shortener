-- -----------------------------------------------------
-- Table `url`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `url` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `long_url` VARCHAR(255) NOT NULL ,
  `remote_ip` CHAR(15) NOT NULL COMMENT 'ip address used to shorten URL' ,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `long` (`long_url` ASC) )
ENGINE = InnoDB


-- -----------------------------------------------------
-- Table `click`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `click` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `url_id` INT UNSIGNED NOT NULL ,
   `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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