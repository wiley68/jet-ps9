CREATE TABLE IF NOT EXISTS `ps_jet_kop` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `jet_product_id` VARCHAR(20) NOT NULL,
  `jet_product_percent` DECIMAL(5,2) NOT NULL,
  `jet_product_meseci` VARCHAR(50) NOT NULL,
  `jet_product_price` DECIMAL(10,2) UNSIGNED NOT NULL,
  `jet_product_start` DATE NOT NULL,
  `jet_product_end` DATE NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `idx` (`jet_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
