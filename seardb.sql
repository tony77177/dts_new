-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-07-24 15:06:22
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `pr_update_location_info`(IN `IPAddress` VARCHAR(15), IN `Country` VARCHAR(50), IN `Province` VARCHAR(50), IN `City` VARCHAR(80), IN `Organization` VARCHAR(100), IN `Telecom` VARCHAR(50), IN `Longitude` DECIMAL(10,7), IN `Latitude` DECIMAL(10,7), IN `Area1` VARCHAR(50), IN `Area2` VARCHAR(50), IN `AdDivisions` INT(11), IN `InterNum` TINYINT(4), IN `CountryNum` CHAR(4), IN `Continents` CHAR(4), IN `Update_time` DATETIME, IN `Curr_interval_update_time` INT(15))
BEGIN
	/*数据是否存在的标志*/
SET @flag = (
	SELECT
		COUNT(*)
	FROM
		searip
	WHERE
		searip.IPAddress = IPAddress
);

/*返回数据当前更新时间的unix时间戳*/
SET @upd_time = (
	SELECT
		UNIX_TIMESTAMP(searip.Update_time)
	FROM
		searip
	WHERE
		searip.IPAddress = IPAddress
);

/*是否更新数据的标志，利用当前时间减去数据更新时间，得到一个Unix时间戳*/
SET @is_update_flag = UNIX_TIMESTAMP(Update_time) - @upd_time;

/*判断逻辑：1、如果数据不存在则插入数据；2、如果存在，对比数据更新时间与当前时间是否大于间隔时间值，如果大于，则进行更新*/
IF @flag = 0 THEN
	INSERT INTO searip (
		searip.IPAddress,
		searip.Country,
		searip.Province,
		searip.City,
		searip.Organization,
		searip.Telecom,
		searip.Longitude,
		searip.Latitude,
		searip.Area1,
		searip.Area2,
		searip.AdDivisions,
		searip.InterNum,
		searip.CountryNum,
		searip.Continents,
		searip.Update_time
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
	UPDATE searip
SET searip.Country = Country,
 searip.Province = Province,
 searip.City = City,
 searip.Organization = Organization,
 searip.Telecom = Telecom,
 searip.Longitude = Longitude,
 searip.Latitude = Latitude,
 searip.Area1 = Area1,
 searip.Area2 = Area2,
 searip.AdDivisions = AdDivisions,
 searip.InterNum = InterNum,
 searip.CountryNum = CountryNum,
 searip.Continents = Continents,
 searip.Update_time = Update_time
WHERE
	searip.IPAddress = IPAddress;


END
IF;


END$$

DELIMITER ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=368 ;

--
-- 转存表中的数据 `searip`
--

INSERT INTO `searip` (`IPId`, `IPAddress`, `Country`, `Province`, `City`, `Organization`, `Telecom`, `Longitude`, `Latitude`, `Area1`, `Area2`, `AdDivisions`, `InterNum`, `CountryNum`, `Continents`, `Update_time`) VALUES
(67, '112.90.83.112', '中国', '广东', '深圳', '', '联通', '22.5470000', '114.0859470', 'Asia/Shanghai', 'UTC+8', 440300, 86, 'CN', 'AP', '2016-07-23 15:48:46'),
(68, '61.135.169.125', '中国', '北京', '北京', 'baidu.com', '联通', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 15:48:47'),
(69, '118.123.7.189', '中国', '四川', '绵阳', '', '电信', '31.4640200', '104.7417220', 'Asia/Chongqing', 'UTC+8', 510700, 86, 'CN', 'AP', '2016-07-23 15:48:47'),
(82, '220.250.64.23', '中国', '北京', '北京', '', '联通', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:02:58'),
(83, '1.1.1.1', '测试数据', 'APNIC', '', '', '', '0.0000000', '0.0000000', '', '', 0, 0, '*', '*', '2016-07-23 16:03:15'),
(85, '113.207.2.68', '中国', '重庆', '重庆', '', '联通', '29.5331550', '106.5049620', 'Asia/Chongqing', 'UTC+8', 500000, 86, 'CN', 'AP', '2016-07-23 16:08:08'),
(86, '202.108.33.60', '中国', '北京', '北京', '', '联通', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:08:08'),
(87, '123.125.104.197', '中国', '北京', '北京', '', '联通', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:08:09'),
(88, '113.31.26.67', '中国', '北京', '北京', '北京前景世纪电讯技术有限公司', '电信/铁通', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:08:09'),
(90, '106.11.62.61', '中国', '上海', '上海', '', '阿里云/电信/联通/移动/铁通/教育网', '31.2317060', '121.4726440', 'Asia/Shanghai', 'UTC+8', 310000, 86, 'CN', 'AP', '2016-07-23 16:08:09'),
(91, '58.16.67.119', '中国', '贵州', '贵阳', '', '联通', '26.5783430', '106.7134780', 'Asia/Chongqing', 'UTC+8', 520100, 86, 'CN', 'AP', '2016-07-23 16:08:10'),
(92, '222.85.128.70', '中国', '贵州', '贵阳', '', '电信', '26.5783430', '106.7134780', 'Asia/Chongqing', 'UTC+8', 520100, 86, 'CN', 'AP', '2016-07-23 16:08:10'),
(93, '219.151.4.98', '中国', '贵州', '贵阳', '', '电信', '26.5783430', '106.7134780', 'Asia/Chongqing', 'UTC+8', 520100, 86, 'CN', 'AP', '2016-07-23 18:42:24'),
(94, '211.139.0.19', '中国', '贵州', '贵阳', '', '移动', '26.5783430', '106.7134780', 'Asia/Chongqing', 'UTC+8', 520100, 86, 'CN', 'AP', '2016-07-23 18:49:07'),
(95, '113.207.82.1', '中国', '重庆', '重庆', '', '联通', '29.5331550', '106.5049620', 'Asia/Chongqing', 'UTC+8', 500000, 86, 'CN', 'AP', '2016-07-23 16:08:10'),
(96, '124.172.221.156', '中国', '广东', '广州', 'gzidc.com', '电信', '23.1251780', '113.2806370', 'Asia/Shanghai', 'UTC+8', 440100, 86, 'CN', 'AP', '2016-07-23 16:08:11'),
(97, '203.208.39.208', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:08:11'),
(98, '58.251.61.186', '中国', '广东', '深圳', '', '联通', '22.5470000', '114.0859470', 'Asia/Shanghai', 'UTC+8', 440300, 86, 'CN', 'AP', '2016-07-23 16:08:11'),
(99, '42.62.88.173', '中国', '北京', '北京', 'lenet.com.cn', '电信/联通/移动', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:08:11'),
(100, '115.182.66.19', '中国', '北京', '北京', '', '鹏博士/联通/电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:08:11'),
(101, '123.57.245.41', '中国', '北京', '北京', '', '阿里云/电信/联通/移动/铁通/教育网', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:08:12'),
(102, '23.236.78.10', '美国', '加利福尼亚州', '洛杉矶', '', '', '34.0535010', '-118.2450030', 'America/Los_Angeles', 'UTC-7', 0, 1, 'US', 'NA', '2016-07-23 16:08:12'),
(103, '219.154.72.221', '中国', '河南', '新乡', '', '联通', '35.3026160', '113.8839910', 'Asia/Shanghai', 'UTC+8', 410700, 86, 'CN', 'AP', '2016-07-23 16:08:12'),
(104, '113.207.73.167', '中国', '重庆', '重庆', '', '联通', '29.5331550', '106.5049620', 'Asia/Chongqing', 'UTC+8', 500000, 86, 'CN', 'AP', '2016-07-23 16:08:13'),
(105, '218.60.112.114', '中国', '辽宁', '大连', '', '联通', '38.9145900', '121.6186220', 'Asia/Shanghai', 'UTC+8', 210200, 86, 'CN', 'AP', '2016-07-23 16:08:13'),
(106, '119.145.14.118', '中国', '广东', '深圳', '', '电信', '22.5470000', '114.0859470', 'Asia/Shanghai', 'UTC+8', 440300, 86, 'CN', 'AP', '2016-07-23 16:08:13'),
(108, '60.191.123.44', '中国', '浙江', '杭州', '', '电信', '30.2874590', '120.1535760', 'Asia/Shanghai', 'UTC+8', 330100, 86, 'CN', 'AP', '2016-07-23 16:08:13'),
(123, '58.251.139.211', '中国', '广东', '深圳', '', '联通', '22.5470000', '114.0859470', 'Asia/Shanghai', 'UTC+8', 440300, 86, 'CN', 'AP', '2016-07-23 16:10:42'),
(125, '223.202.26.19', '中国', '北京', '北京', 'chinacache.com', '联通/电信/移动', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:10:42'),
(129, '202.110.125.182', '中国', '河南', '濮阳', '', '联通', '35.7682340', '115.0412990', 'Asia/Shanghai', 'UTC+8', 410900, 86, 'CN', 'AP', '2016-07-23 16:10:43'),
(164, '140.205.153.12', '中国', '上海', '上海', '', '阿里云/电信/联通/移动/铁通/教育网', '31.2317060', '121.4726440', 'Asia/Shanghai', 'UTC+8', 310000, 86, 'CN', 'AP', '2016-07-23 16:25:35'),
(171, '203.208.43.82', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:25:36'),
(172, '163.177.89.191', '中国', '广东', '深圳', '', '联通', '22.5470000', '114.0859470', 'Asia/Shanghai', 'UTC+8', 440300, 86, 'CN', 'AP', '2016-07-23 16:25:36'),
(177, '119.188.155.243', '中国', '山东', '济南', '', '联通', '36.6758070', '117.0009230', 'Asia/Shanghai', 'UTC+8', 370100, 86, 'CN', 'AP', '2016-07-23 16:25:38'),
(194, '203.208.39.210', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 16:31:44'),
(344, '2.2.2.2', '测试', '测试', '测试', '测试', '测试', '0.0000000', '0.0000000', '测试', '测试', 0, 0, '', '', '2016-07-23 00:00:00'),
(345, '12345', '1', '1', '1', '1', '1', '1.0000000', '1.0000000', '1', '1', 1, 1, '1', '1', '2016-09-24 00:00:00'),
(346, '114.114.114.114', '114DNS', '114DNS', '', '', '', '0.0000000', '0.0000000', '', '', 0, 0, '*', '*', '2016-07-23 21:08:50'),
(347, '113.207.69.72', '中国', '重庆', '重庆', '', '联通', '29.5331550', '106.5049620', 'Asia/Chongqing', 'UTC+8', 500000, 86, 'CN', 'AP', '2016-07-23 21:10:03'),
(348, '203.208.43.112', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:10:05'),
(349, '221.12.31.26', '中国', '浙江', '杭州', '', '联通', '30.2874590', '120.1535760', 'Asia/Shanghai', 'UTC+8', 330100, 86, 'CN', 'AP', '2016-07-23 21:10:08'),
(350, '175.154.189.57', '中国', '四川', '南充', '', '联通', '30.7952810', '106.0829740', 'Asia/Chongqing', 'UTC+8', 511300, 86, 'CN', 'AP', '2016-07-23 21:10:08'),
(351, '104.27.150.121', 'CLOUDFLARE', 'CLOUDFLARE', '', '', 'cloudflare.com', '0.0000000', '0.0000000', '', '', 0, 0, '*', '*', '2016-07-23 21:10:08'),
(352, '203.208.39.241', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:14:03'),
(353, '104.27.151.121', 'CLOUDFLARE', 'CLOUDFLARE', '', '', 'cloudflare.com', '0.0000000', '0.0000000', '', '', 0, 0, '*', '*', '2016-07-23 21:14:05'),
(354, '60.212.19.182', '中国', '山东', '烟台', '', '联通', '37.5392970', '121.3913820', 'Asia/Shanghai', 'UTC+8', 370600, 86, 'CN', 'AP', '2016-07-23 21:16:17'),
(355, '203.208.43.84', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:21:08'),
(356, '203.208.43.115', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:28:29'),
(357, '218.58.101.229', '中国', '山东', '淄博', '', '联通', '36.8149390', '118.0476480', 'Asia/Shanghai', 'UTC+8', 370300, 86, 'CN', 'AP', '2016-07-23 21:29:44'),
(358, '203.208.39.244', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:40:42'),
(359, '203.208.39.242', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:44:41'),
(360, '203.208.43.83', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:49:48'),
(361, '203.208.43.81', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:54:33'),
(362, '203.208.39.243', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 21:58:59'),
(363, '222.163.206.244', '中国', '吉林', '延边朝鲜族自治州', '', '联通', '42.9048230', '129.5132280', 'Asia/Harbin', 'UTC+8', 222400, 86, 'CN', 'AP', '2016-07-23 21:59:48'),
(364, '203.208.39.240', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 22:04:16'),
(365, '203.208.43.114', '中国', '北京', '北京', '谷歌公司', '电信', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 22:08:43'),
(366, '61.135.169.121', '中国', '北京', '北京', 'baidu.com', '联通', '39.9049890', '116.4052850', 'Asia/Shanghai', 'UTC+8', 110000, 86, 'CN', 'AP', '2016-07-23 22:14:33'),
(367, '163.177.178.235', '中国', '广东', '中山', '', '联通', '22.5211130', '113.3823910', 'Asia/Shanghai', 'UTC+8', 442000, 86, 'CN', 'AP', '2016-07-24 07:55:16');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
