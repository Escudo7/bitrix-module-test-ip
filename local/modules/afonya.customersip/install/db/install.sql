CREATE TABLE `afonya_castomers_ip` (
        `ID` INT NOT NULL AUTO_INCREMENT,
        `ORDER_ID` INT NOT NULL,
        `IP` VARCHAR (20) NOT NULL,
        `DATA` TEXT NULL,
        PRIMARY KEY(ID),
        INDEX `FK_AB_ORDER_idx` (`ORDER_ID` ASC),
        CONSTRAINT `FK_AB_ORDER`
        FOREIGN KEY (`ORDER_ID`)
        REFERENCES `b_sale_order` (`ID`)
        ON DELETE CASCADE
        ON UPDATE CASCADE)
        ENGINE = InnoDB;