-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `coachers`;
CREATE TABLE `coachers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `last_education` varchar(64) NOT NULL,
  `experience` text NOT NULL,
  `phone` varchar(32) NOT NULL,
  `sector` varchar(255) NOT NULL,
  `topic` text NOT NULL,
  `status` enum('New Request','Follow Up','Confirmed','Canceled','Rejected') NOT NULL DEFAULT 'New Request',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`(191)),
  KEY `company_name` (`company_name`(191)),
  KEY `position` (`position`(191)),
  KEY `email` (`email`(191)),
  KEY `photo` (`photo`(191)),
  KEY `latest_education` (`last_education`),
  KEY `phone` (`phone`),
  KEY `company_sector` (`sector`(191)),
  KEY `topic` (`topic`(191)),
  FULLTEXT KEY `experience` (`experience`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `participants`;
CREATE TABLE `participants` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_sector` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `sector_to_be_coached` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `problem_desc` text NOT NULL,
  `status` enum('New Request','Invoice Sent','Confirmed','Canceled','Rejected') DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `company_name` (`company_name`),
  KEY `position` (`company_sector`),
  KEY `company_sector` (`sector_to_be_coached`),
  KEY `email` (`email`),
  FULLTEXT KEY `problem_desc` (`problem_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `payment_confirmation`;
CREATE TABLE `payment_confirmation` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email_user_id` bigint(20) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `total_payment` varchar(255) NOT NULL,
  `payment_type` enum('participant','sponsor','coacher') NOT NULL,
  `date_transfer` varchar(64) NOT NULL,
  `no_ref` varchar(255) NOT NULL,
  `bank_name` varchar(64) NOT NULL,
  `bank_username` varchar(64) NOT NULL,
  `screenshot` text NOT NULL,
  `notes` text,
  `email_verif_sent` enum('0','1') DEFAULT '0',
  `status` enum('pending','approved','rejected') NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email_user_id` (`email_user_id`),
  CONSTRAINT `payment_confirmation_ibfk_2` FOREIGN KEY (`email_user_id`) REFERENCES `participants` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `speakers`;
CREATE TABLE `speakers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `position` varchar(64) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `last_education` varchar(64) DEFAULT NULL,
  `experience` text,
  `phone` varchar(32) DEFAULT NULL,
  `sector` varchar(32) DEFAULT NULL,
  `topic` text,
  `fb_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `instagram_link` varchar(255) DEFAULT NULL,
  `promoted` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('New Request','Follow Up','Confirmed','Canceled','Rejected') DEFAULT 'New Request',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `company_name` (`company_name`),
  KEY `position` (`position`),
  KEY `email` (`email`),
  KEY `photo` (`photo`),
  KEY `last_education` (`last_education`),
  KEY `company_sector` (`sector`),
  KEY `phone` (`phone`),
  FULLTEXT KEY `experience` (`experience`),
  FULLTEXT KEY `topic` (`topic`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `sponsors`;
CREATE TABLE `sponsors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `company_sector` varchar(255) NOT NULL,
  `email_pic` varchar(255) NOT NULL,
  `company_logo` varchar(255) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `sponsor_type` enum('platinum','gold','silver','media_partner') NOT NULL,
  `status` enum('New Request','Invoice Sent','Confirmed','Canceled','Rejected') NOT NULL DEFAULT 'New Request',
  `remarks` varchar(500) DEFAULT NULL,
  `payment_amount` decimal(15,2) DEFAULT '0.00',
  `payment_date` date DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `company_name` (`company_name`(191)),
  KEY `company_sector` (`company_sector`(191)),
  KEY `email_pic` (`email_pic`(191)),
  KEY `phone` (`phone`),
  KEY `sponsor_type` (`sponsor_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `volunteers`;
CREATE TABLE `volunteers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `why_you_apply_desc` text NOT NULL,
  `created_at` datetime NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `status` enum('New Request','Follow Up','Confirmed','Canceled','Rejected') DEFAULT 'New Request',
  `ig_link` varchar(255) DEFAULT NULL,
  `fb_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `email` (`email`),
  KEY `phone` (`phone`),
  FULLTEXT KEY `why_you_apply_desc` (`why_you_apply_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2019-02-01 04:28:15
