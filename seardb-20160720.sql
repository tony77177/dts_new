-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-07-20 09:58:59
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

-- --------------------------------------------------------

--
-- 表的结构 `conndoip`
--

CREATE TABLE IF NOT EXISTS `conndoip` (
  `DOId` int(11) NOT NULL AUTO_INCREMENT,
  `IPId` int(11) NOT NULL,
  `TimeStamp` date DEFAULT NULL,
  PRIMARY KEY (`DOId`,`IPId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `connhashdo`
--

CREATE TABLE IF NOT EXISTS `connhashdo` (
  `DOId` int(11) NOT NULL AUTO_INCREMENT,
  `HASHId` int(11) NOT NULL,
  PRIMARY KEY (`DOId`,`HASHId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `connhaship`
--

CREATE TABLE IF NOT EXISTS `connhaship` (
  `IPId` int(11) NOT NULL AUTO_INCREMENT,
  `HASHId` int(11) NOT NULL,
  PRIMARY KEY (`IPId`,`HASHId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `seardomain`
--

CREATE TABLE IF NOT EXISTS `seardomain` (
  `DOId` int(11) NOT NULL AUTO_INCREMENT,
  `MAILId` int(11) DEFAULT NULL,
  `Domain` varchar(100) NOT NULL,
  `RegEmail` varchar(80) DEFAULT NULL,
  `SameDomain` text,
  `DoReference` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`DOId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `searhash`
--

CREATE TABLE IF NOT EXISTS `searhash` (
  `HASHId` int(11) NOT NULL AUTO_INCREMENT,
  `SMD5` char(32) NOT NULL,
  `SSHA1` char(160) DEFAULT NULL,
  `SScan` text,
  PRIMARY KEY (`HASHId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `searip`
--

CREATE TABLE IF NOT EXISTS `searip` (
  `IPId` int(11) NOT NULL AUTO_INCREMENT,
  `IPAdress` varchar(15) NOT NULL,
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
  UNIQUE KEY `IPAdress` (`IPAdress`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- 转存表中的数据 `searip`
--

INSERT INTO `searip` (`IPId`, `IPAdress`, `Country`, `Province`, `City`, `Organization`, `Telecom`, `Longitude`, `Latitude`, `Area1`, `Area2`, `AdDivisions`, `InterNum`, `CountryNum`, `Continents`, `Update_time`) VALUES
(30, '23.236.78.10', '美国', '加利福尼亚州', '洛杉矶', '', '', '34.0535010', '-118.2450030', 'America/Los_Angeles', 'UTC-7', 0, 1, 'US', 'NA', '2016-07-20 15:55:47'),
(31, '120.55.150.196', '中国', '浙江', '杭州', '', '阿里云/电信/联通/移动/铁通/教育网', '30.2874590', '120.1535760', 'Asia/Shanghai', 'UTC+8', 330100, 86, 'CN', 'AP', '2016-07-20 15:55:57'),
(32, '202.101.70.151', '中国', '贵州', '贵阳', '', '电信', '26.5783430', '106.7134780', 'Asia/Chongqing', 'UTC+8', 520100, 86, 'CN', 'AP', '2016-07-20 15:56:18');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
