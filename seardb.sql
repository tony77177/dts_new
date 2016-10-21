-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-10-21 19:05:33
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `seardb`
--

DELIMITER $$
--
-- 存储过程
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `pr_update_antivirus_info`(IN `_antivirus` VARCHAR(255), IN `_hashes` TEXT,IN `_references` TEXT,IN `_permalink` VARCHAR(255), IN `_upd_time` DATETIME, IN `_curr_interval_update_time` INT(15))
BEGIN
	/*数据是否存在的标志*/
SET @flag = (
	SELECT
		COUNT(*)
	FROM
		threat_antivirus
	WHERE
		threat_antivirus.antivirus = _antivirus
);

/*返回数据当前更新时间的unix时间戳*/
SET @upd_time = (
	SELECT
		UNIX_TIMESTAMP(threat_antivirus.upd_time)
	FROM
		threat_antivirus
	WHERE
		threat_antivirus.antivirus = _antivirus
);

/*是否更新数据的标志，利用当前时间减去数据更新时间，得到一个Unix时间戳*/
SET @is_update_flag = UNIX_TIMESTAMP(_upd_time) - @upd_time;

/*判断逻辑：1、如果数据不存在则插入数据；2、如果存在，对比数据更新时间与当前时间是否大于间隔时间值，如果大于，则进行更新*/
IF @flag = 0 THEN
	INSERT INTO threat_antivirus (
		threat_antivirus.antivirus,
		threat_antivirus.hashes,
		threat_antivirus.references,
		threat_antivirus.permalink,
		threat_antivirus.upd_time
	)
VALUES
	(
		_antivirus,     
		_hashes,     
		_references,     
		_permalink, 
		_upd_time    
	);


ELSEIF @is_update_flag > _curr_interval_update_time THEN
	UPDATE threat_antivirus
SET threat_antivirus.hashes = _hashes,
 threat_antivirus.references = _references,
 threat_antivirus.permalink = _permalink,
 threat_antivirus.upd_time = _upd_time
WHERE
	threat_antivirus.antivirus = _antivirus;

END
IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pr_update_domain_info`(IN `_domain` VARCHAR(255), IN `_last_resolved` TEXT, IN `_ip_address` TEXT, IN `_hashes` TEXT, IN `_emails` TEXT, IN `_subdomains` TEXT, IN `_references` TEXT, IN `_votes` VARCHAR(10), IN `_permalink` VARCHAR(255), IN `_upd_time` DATETIME, IN `_curr_interval_update_time` INT(15))
BEGIN
	/*数据是否存在的标志*/
SET @flag = (
	SELECT
		COUNT(*)
	FROM
		threat_domain
	WHERE
		threat_domain.domain = _domain
);

/*返回数据当前更新时间的unix时间戳*/
SET @upd_time = (
	SELECT
		UNIX_TIMESTAMP(threat_domain.upd_time)
	FROM
		threat_domain
	WHERE
		threat_domain.domain = _domain
);

/*是否更新数据的标志，利用当前时间减去数据更新时间，得到一个Unix时间戳*/
SET @is_update_flag = UNIX_TIMESTAMP(_upd_time) - @upd_time;

/*判断逻辑：1、如果数据不存在则插入数据；2、如果存在，对比数据更新时间与当前时间是否大于间隔时间值，如果大于，则进行更新*/
IF @flag = 0 THEN
	INSERT INTO threat_domain (
		threat_domain.domain,
		threat_domain.last_resolved,
		threat_domain.ip_address,
		threat_domain.hashes,
		threat_domain.emails,
		threat_domain.subdomains,
		threat_domain.references,
		threat_domain.votes,
		threat_domain.permalink,
		threat_domain.upd_time
	)
VALUES
	(
		_domain,     
		_last_resolved,
		_ip_address,
		_hashes,     
		_emails,     
		_subdomains, 
		_references, 
		_votes,      
		_permalink,  
		_upd_time    
	);


ELSEIF @is_update_flag > _curr_interval_update_time THEN
	UPDATE threat_domain
SET threat_domain.last_resolved = _last_resolved,
 threat_domain.ip_address = _ip_address,
 threat_domain.hashes = _hashes,
 threat_domain.emails = _emails,
 threat_domain.subdomains = _subdomains,
 threat_domain.references = _references,
 threat_domain.votes = _votes,
 threat_domain.permalink = _permalink,
 threat_domain.upd_time = _upd_time
WHERE
	threat_domain.domain = _domain;

END
IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pr_update_email_info`(IN `_email` VARCHAR(255), IN `_domains` TEXT, IN `_references` TEXT, IN `_permalink` VARCHAR(255), IN `_upd_time` DATETIME, IN `_curr_interval_update_time` INT(15))
BEGIN
	/*数据是否存在的标志*/
SET @flag = (
	SELECT
		COUNT(*)
	FROM
		threat_email
	WHERE
		threat_email.email = _email
);

/*返回数据当前更新时间的unix时间戳*/
SET @upd_time = (
	SELECT
		UNIX_TIMESTAMP(threat_email.upd_time)
	FROM
		threat_email
	WHERE
		threat_email.email = _email
);

/*是否更新数据的标志，利用当前时间减去数据更新时间，得到一个Unix时间戳*/
SET @is_update_flag = UNIX_TIMESTAMP(_upd_time) - @upd_time;

/*判断逻辑：1、如果数据不存在则插入数据；2、如果存在，对比数据更新时间与当前时间是否大于间隔时间值，如果大于，则进行更新*/
IF @flag = 0 THEN
	INSERT INTO threat_email (
		threat_email.email,
		threat_email.domains,
		threat_email.references,
		threat_email.permalink,
		threat_email.upd_time
	)
VALUES
	(
		_email,
		_domains,
		_references,
		_permalink,
		_upd_time
	);


ELSEIF @is_update_flag > _curr_interval_update_time THEN
	UPDATE threat_email
SET threat_email.domains = _domains,
 threat_email.references = _references,
 threat_email.permalink = _permalink,
 threat_email.upd_time = _upd_time
WHERE
	threat_email.email = _email;

END
IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pr_update_hash_info`(IN `_hash` VARCHAR(255), IN `_md5` VARCHAR(255), IN `_sha1` VARCHAR(255), IN `_scans` TEXT, IN `_ips` TEXT, IN `_domains` TEXT, IN `_references` TEXT, IN `_permalink` VARCHAR(255), IN `_upd_time` DATETIME, IN `_curr_interval_update_time` INT(15))
BEGIN
	/*数据是否存在的标志*/
SET @flag = (
	SELECT
		COUNT(*)
	FROM
		threat_hash
	WHERE
		threat_hash.hash = _hash
);

/*返回数据当前更新时间的unix时间戳*/
SET @upd_time = (
	SELECT
		UNIX_TIMESTAMP(threat_hash.upd_time)
	FROM
		threat_hash
	WHERE
		threat_hash.hash = _hash
);

/*是否更新数据的标志，利用当前时间减去数据更新时间，得到一个Unix时间戳*/
SET @is_update_flag = UNIX_TIMESTAMP(_upd_time) - @upd_time;

/*判断逻辑：1、如果数据不存在则插入数据；2、如果存在，对比数据更新时间与当前时间是否大于间隔时间值，如果大于，则进行更新*/
IF @flag = 0 THEN
	INSERT INTO threat_hash (
		threat_hash.hash,
		threat_hash.md5,
		threat_hash.sha1,
		threat_hash.scans,
		threat_hash.ips,
		threat_hash.domains,
		threat_hash.references,
		threat_hash.permalink,
		threat_hash.upd_time
	)
VALUES
	(
		_hash,      
		_md5,       
		_sha1,      
		_scans,     
		_ips,       
		_domains,   
		_references,
		_permalink, 
		_upd_time  
	);


ELSEIF @is_update_flag > _curr_interval_update_time THEN
	UPDATE threat_hash
SET threat_hash.md5 = _md5,
 threat_hash.sha1 = _sha1,
 threat_hash.scans = _scans,
 threat_hash.ips = _ips,
 threat_hash.domains = _domains,
 threat_hash.references = _references,
 threat_hash.permalink = _permalink,
 threat_hash.upd_time = _upd_time
WHERE
	threat_hash.hash = _hash;

END
IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pr_update_ip_info`(IN `_ip_address` VARCHAR(255), IN `_last_resolved` TEXT, IN `_domain` TEXT, IN `_hashes` TEXT,IN `_references` TEXT, IN `_votes` VARCHAR(10), IN `_permalink` VARCHAR(255), IN `_upd_time` DATETIME, IN `_curr_interval_update_time` INT(15))
BEGIN
	/*数据是否存在的标志*/
SET @flag = (
	SELECT
		COUNT(*)
	FROM
		threat_ip
	WHERE
		threat_ip.ip_address = _ip_address
);

/*返回数据当前更新时间的unix时间戳*/
SET @upd_time = (
	SELECT
		UNIX_TIMESTAMP(threat_ip.upd_time)
	FROM
		threat_ip
	WHERE
		threat_ip.ip_address = _ip_address
);

/*是否更新数据的标志，利用当前时间减去数据更新时间，得到一个Unix时间戳*/
SET @is_update_flag = UNIX_TIMESTAMP(_upd_time) - @upd_time;

/*判断逻辑：1、如果数据不存在则插入数据；2、如果存在，对比数据更新时间与当前时间是否大于间隔时间值，如果大于，则进行更新*/
IF @flag = 0 THEN
	INSERT INTO threat_ip (
		threat_ip.ip_address,
		threat_ip.last_resolved,
		threat_ip.domain,
		threat_ip.hashes,
		threat_ip.references,
		threat_ip.votes,
		threat_ip.permalink,
		threat_ip.upd_time
	)
VALUES
	(
		_ip_address,     
		_last_resolved,
		_domain,
		_hashes,     
		_references,     
		_votes, 
		_permalink, 
		_upd_time    
	);


ELSEIF @is_update_flag > _curr_interval_update_time THEN
	UPDATE threat_ip
SET threat_ip.last_resolved = _last_resolved,
 threat_ip.domain = _domain,
 threat_ip.hashes = _hashes,
 threat_ip.references = _references,
 threat_ip.votes = _votes,
 threat_ip.permalink = _permalink,
 threat_ip.upd_time = _upd_time
WHERE
	threat_ip.ip_address = _ip_address;

END
IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pr_update_location_info`(IN `IPAddress` VARCHAR(15), IN `Country` VARCHAR(50), IN `Province` VARCHAR(50), IN `City` VARCHAR(80), IN `Organization` VARCHAR(100), IN `Telecom` VARCHAR(50), IN `Longitude` DECIMAL(10,7), IN `Latitude` DECIMAL(10,7), IN `Area1` VARCHAR(50), IN `Area2` VARCHAR(50), IN `AdDivisions` INT(11), IN `InterNum` TINYINT(4), IN `CountryNum` CHAR(4), IN `Continents` CHAR(4), IN `Update_time` DATETIME, IN `Curr_interval_update_time` INT(15))
BEGIN
	/*数据是否存在的标志*/
SET @flag = (
	SELECT
		COUNT(*)
	FROM
		location_info
	WHERE
		location_info.IPAddress = IPAddress
);

/*返回数据当前更新时间的unix时间戳*/
SET @upd_time = (
	SELECT
		UNIX_TIMESTAMP(location_info.Update_time)
	FROM
		location_info
	WHERE
		location_info.IPAddress = IPAddress
);

/*是否更新数据的标志，利用当前时间减去数据更新时间，得到一个Unix时间戳*/
SET @is_update_flag = UNIX_TIMESTAMP(Update_time) - @upd_time;

/*判断逻辑：1、如果数据不存在则插入数据；2、如果存在，对比数据更新时间与当前时间是否大于间隔时间值，如果大于，则进行更新*/
IF @flag = 0 THEN
	INSERT INTO location_info (
		location_info.IPAddress,
		location_info.Country,
		location_info.Province,
		location_info.City,
		location_info.Organization,
		location_info.Telecom,
		location_info.Longitude,
		location_info.Latitude,
		location_info.Area1,
		location_info.Area2,
		location_info.AdDivisions,
		location_info.InterNum,
		location_info.CountryNum,
		location_info.Continents,
		location_info.Update_time
	)
VALUES
	(
		IPAddress,
		Country,
		Province,
		City,
		Organization,
		Telecom,
		Longitude,
		Latitude,
		Area1,
		Area2,
		AdDivisions,
		InterNum,
		CountryNum,
		Continents,
		Update_time
	);


ELSEIF @is_update_flag > Curr_interval_update_time THEN
	UPDATE location_info
SET location_info.Country = Country,
 location_info.Province = Province,
 location_info.City = City,
 location_info.Organization = Organization,
 location_info.Telecom = Telecom,
 location_info.Longitude = Longitude,
 location_info.Latitude = Latitude,
 location_info.Area1 = Area1,
 location_info.Area2 = Area2,
 location_info.AdDivisions = AdDivisions,
 location_info.InterNum = InterNum,
 location_info.CountryNum = CountryNum,
 location_info.Continents = Continents,
 location_info.Update_time = Update_time
WHERE
	location_info.IPAddress = IPAddress;


END
IF;


END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `location_info`
--

CREATE TABLE IF NOT EXISTS `location_info` (
  `IPId` int(11) NOT NULL AUTO_INCREMENT,
  `IPAddress` varchar(15) NOT NULL,
  `Country` varchar(50) DEFAULT NULL,
  `Province` varchar(50) DEFAULT NULL,
  `City` varchar(80) DEFAULT NULL,
  `Organization` varchar(100) DEFAULT NULL,
  `Telecom` varchar(50) DEFAULT NULL,
  `Longitude` decimal(10,7) DEFAULT NULL,
  `Latitude` decimal(10,7) DEFAULT NULL,
  `Area1` varchar(50) DEFAULT NULL,
  `Area2` varchar(50) DEFAULT NULL,
  `AdDivisions` int(11) DEFAULT NULL,
  `InterNum` tinyint(4) DEFAULT NULL,
  `CountryNum` char(4) DEFAULT NULL,
  `Continents` char(4) DEFAULT NULL,
  `Update_time` datetime NOT NULL,
  PRIMARY KEY (`IPId`),
  UNIQUE KEY `IPAdress` (`IPAddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `log_info`
--

CREATE TABLE IF NOT EXISTS `log_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) NOT NULL,
  `ip_address` varchar(30) NOT NULL,
  `login_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `threat_antivirus`
--

CREATE TABLE IF NOT EXISTS `threat_antivirus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `antivirus` varchar(255) NOT NULL,
  `hashes` text,
  `references` text,
  `permalink` varchar(255) DEFAULT NULL,
  `upd_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `threat_domain`
--

CREATE TABLE IF NOT EXISTS `threat_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `last_resolved` text NOT NULL,
  `ip_address` text NOT NULL,
  `hashes` text,
  `emails` text,
  `subdomains` text,
  `references` text,
  `votes` varchar(10) DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `upd_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `threat_email`
--

CREATE TABLE IF NOT EXISTS `threat_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `domains` text,
  `references` text,
  `permalink` varchar(255) DEFAULT NULL,
  `upd_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `threat_hash`
--

CREATE TABLE IF NOT EXISTS `threat_hash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(255) NOT NULL,
  `md5` varchar(255) DEFAULT NULL,
  `sha1` varchar(255) DEFAULT NULL,
  `scans` text,
  `ips` text,
  `domains` text,
  `references` text,
  `permalink` varchar(255) DEFAULT NULL,
  `upd_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `threat_ip`
--

CREATE TABLE IF NOT EXISTS `threat_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) NOT NULL,
  `last_resolved` text NOT NULL,
  `domain` text NOT NULL,
  `hashes` text,
  `references` text,
  `votes` varchar(10) NOT NULL,
  `permalink` varchar(255) NOT NULL,
  `upd_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_info`
--

CREATE TABLE IF NOT EXISTS `user_info` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) NOT NULL,
  `user_pwd` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
