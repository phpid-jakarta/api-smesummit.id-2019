-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `participants`;
CREATE TABLE `participants` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `company_sector` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `problem_desc` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `company_name` (`company_name`),
  KEY `position` (`position`),
  KEY `company_sector` (`company_sector`),
  KEY `email` (`email`),
  FULLTEXT KEY `problem_desc` (`problem_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2018-12-03 05:39:27
