-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 02, 2014 at 01:13 PM
-- Server version: 5.5.37
-- PHP Version: 5.4.30-1~dotdeb.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `paradisco`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE IF NOT EXISTS `attendance` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(50) COLLATE latin1_swedish_ci NOT NULL,
  `date` date NOT NULL,
  `type` varchar(10) COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
AUTO_INCREMENT=277 ;

--
-- Dumping data for table `attendance`
--
-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE IF NOT EXISTS `semester` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(140) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `from` date NOT NULL,
  `to` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`ID`, `title`, `from`, `to`) VALUES
(1, 'Semesterferien Sommer 2014', '2014-07-12', '2014-10-19'),
(2, 'Wintersemester 2014/2015', '2014-10-20', '2015-02-13');

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE IF NOT EXISTS `training` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `day` int(11) NOT NULL,
  `semesterID` int(11) NOT NULL,
  `from` time NOT NULL,
  `to` time NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci AUTO_INCREMENT=7
;

--
-- Dumping data for table `training`
--

INSERT INTO `training` (`ID`, `day`, `semesterID`, `from`, `to`) VALUES
(1, 2, 1, '18:00:00', '20:00:00'),
(2, 4, 1, '18:00:00', '20:00:00'),
(3, 2, 2, '20:00:00', '22:00:00'),
(5, 3, 2, '20:45:00', '23:00:00'),
(6, 1, 2, '21:30:00', '23:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `name` varchar(50) COLLATE latin1_swedish_ci NOT NULL,
  `attributes` text COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
