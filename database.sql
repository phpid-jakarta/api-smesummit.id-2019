-- Adminer 4.6.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `coachers`;
CREATE TABLE `coachers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `last_education` varchar(64) NOT NULL,
  `experience` text NOT NULL,
  `phone` varchar(32) NOT NULL,
  `sector` varchar(32) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `company_name` (`company_name`),
  KEY `position` (`position`),
  KEY `email` (`email`),
  KEY `photo` (`photo`),
  KEY `latest_education` (`last_education`),
  KEY `phone` (`phone`),
  KEY `company_sector` (`sector`),
  KEY `topic` (`topic`),
  FULLTEXT KEY `experience` (`experience`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `faq`;
CREATE TABLE `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `faq` (`id`, `question`, `answer`) VALUES
(1, 'What is <b>SME Summit</b>?', 'SME Summit 2019 is a conference and mentoring panel session organized by PHP Indonesia which aims to help the technological transformation in non-tech companies, small and medium enterprises and traditional companies, shift the paradigm that IT division is only as a support division in the company into a profit-center division and helping to transform your stagnant companies into exponential growth companies.'),
(2, 'I am a question blablabla blablabla?', 'I am the answer <b>qweqweqwe</b>');

DROP TABLE IF EXISTS `participants`;
CREATE TABLE `participants` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `coached_sector` varchar(255) NOT NULL,
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

INSERT INTO `speakers` (`id`, `name`, `company_name`, `position`, `email`, `photo`, `last_education`, `experience`, `phone`, `sector`, `topic`, `fb_link`, `twitter_link`, `instagram_link`, `promoted`, `created_at`) VALUES
(1, 'Ken Ratri Iswari', 'Geekhunter', 'Founder & CEO Geekhunter', NULL, 'https://www.smesummit.id/images/speaker/ken-ratri-iswari.jpeg',  NULL, NULL, NULL, 'Human Asset Sector', NULL, 'https://www.facebook.com/kenratriiswari',  'https://twitter.com/kenratriiswari', 'https://www.instagram.com/kenratriiswari/',  '1',  '2018-12-21 20:40:42'),
(2, 'Rendy Maulana',  'Qwords', 'CEO of Qwords',  NULL, 'https://www.smesummit.id/images/speaker/rendy-maulana.jpeg', NULL, NULL, NULL, 'Regulation Sector',  NULL, 'https://www.facebook.com/rendy.maulana', '', 'https://www.linkedin.com/in/rendy/', '1',  '2018-12-21 20:41:21');

DROP TABLE IF EXISTS `sponsors`;
CREATE TABLE `sponsors` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `company_sector` varchar(255) NOT NULL,
  `email_pic` varchar(255) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `sponsor_type` enum('platinum','gold','silver') NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `company_name` (`company_name`),
  KEY `company_sector` (`company_sector`),
  KEY `email_pic` (`email_pic`),
  KEY `phone` (`phone`),
  KEY `sponsor_type` (`sponsor_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `volunteers`;
CREATE TABLE `volunteers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `why_you_apply_desc` text NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `email` (`email`),
  KEY `phone` (`phone`),
  FULLTEXT KEY `why_you_apply_desc` (`why_you_apply_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2018-12-21 13:41:45
