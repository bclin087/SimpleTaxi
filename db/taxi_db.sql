-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 建立日期: May 17, 2015, 04:15 AM
-- 伺服器版本: 5.5.9
-- PHP 版本: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫: `taxi_db`
--

-- --------------------------------------------------------

--
-- 資料表格式： `orders`
--

CREATE TABLE `orders` (
  `txnid` int(11) NOT NULL AUTO_INCREMENT,
  `user_mobile` varchar(10) NOT NULL,
  `user_address` varchar(500) NOT NULL,
  `lat` varchar(16) NOT NULL,
  `lon` varchar(16) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `driver_id` varchar(10) DEFAULT NULL,
  `driver_lat` varchar(16) DEFAULT NULL,
  `driver_lon` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`txnid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;
