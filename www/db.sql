CREATE DATABASE IF NOT EXISTS ticketing_system DEFAULT CHARSET utf8;
USE ticketing_system;

CREATE TABLE `tickets` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ticket_number` VARCHAR(10) NOT NULL,
  `sale_time` DATETIME NOT NULL,
  `check_status` VARCHAR(10) NOT NULL DEFAULT '未检票',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
