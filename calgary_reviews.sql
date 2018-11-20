-- phpMyAdmin SQL Dump
-- version 5.0.0-dev
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 20, 2018 at 03:01 AM
-- Server version: 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `calgary_reviews`
--

-- --------------------------------------------------------

--
-- Table structure for table `adds_to`
--

DROP TABLE IF EXISTS `adds_to`;
CREATE TABLE IF NOT EXISTS `adds_to` (
  `user_id` varchar(100) NOT NULL,
  `list_name` varchar(100) NOT NULL,
  `list_user` varchar(100) NOT NULL,
  `r_id` int(255) NOT NULL,
  PRIMARY KEY (`user_id`,`list_name`,`r_id`),
  KEY `list_name` (`list_name`,`list_user`),
  KEY `r_id` (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `business_hours`
--

DROP TABLE IF EXISTS `business_hours`;
CREATE TABLE IF NOT EXISTS `business_hours` (
  `r_id` int(255) NOT NULL,
  `day_of_week` int(1) NOT NULL,
  `open_time` varchar(7) NOT NULL,
  `close_time` varchar(7) NOT NULL,
  PRIMARY KEY (`r_id`,`day_of_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `business_hours`
--

INSERT INTO `business_hours` (`r_id`, `day_of_week`, `open_time`, `close_time`) VALUES
(1, 0, '8:30 AM', '5:00 PM');

--
-- Triggers `business_hours`
--
DROP TRIGGER IF EXISTS `check_day`;
DELIMITER $$
CREATE TRIGGER `check_day` BEFORE INSERT ON `business_hours` FOR EACH ROW BEGIN
IF new.day_of_week NOT IN (0, 1, 2, 3, 4, 5, 6) THEN
	SIGNAL SQLSTATE '45000'
    	SET MESSAGE_TEXT = 'Invalid day of week (range from 0-6)';
END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `user_id` varchar(100) NOT NULL,
  `photoid` int(255) DEFAULT NULL,
  `list_name` varchar(100) DEFAULT NULL,
  `list_user` varchar(100) DEFAULT NULL,
  KEY `list_name` (`list_name`,`list_user`),
  KEY `user_id` (`user_id`),
  KEY `photoid` (`photoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `deletes`
--

DROP TABLE IF EXISTS `deletes`;
CREATE TABLE IF NOT EXISTS `deletes` (
  `admin_user` varchar(100) NOT NULL,
  `r_id` int(255) NOT NULL,
  `photoid` int(255) NOT NULL,
  `list_name` varchar(100) NOT NULL,
  `list_user` varchar(100) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `food_rid` int(255) NOT NULL,
  KEY `r_id` (`r_id`),
  KEY `list_name` (`list_name`,`list_user`),
  KEY `food_name` (`food_name`,`food_rid`),
  KEY `admin_user` (`admin_user`),
  KEY `photoid` (`photoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `delivers_to`
--

DROP TABLE IF EXISTS `delivers_to`;
CREATE TABLE IF NOT EXISTS `delivers_to` (
  `user_id` varchar(100) NOT NULL,
  `emp_ssn` int(9) NOT NULL,
  KEY `emp_ssn` (`emp_ssn`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `edits`
--

DROP TABLE IF EXISTS `edits`;
CREATE TABLE IF NOT EXISTS `edits` (
  `admin_user` varchar(100) NOT NULL,
  `r_id` int(255) NOT NULL,
  `photoid` int(255) NOT NULL,
  `list_name` varchar(100) NOT NULL,
  `list_user` varchar(100) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `food_rid` int(255) NOT NULL,
  KEY `admin_user` (`admin_user`),
  KEY `r_id` (`r_id`),
  KEY `list_name` (`list_name`,`list_user`),
  KEY `food_name` (`food_name`,`food_rid`),
  KEY `photoid` (`photoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `ssn` int(9) NOT NULL,
  `fname` varchar(100) NOT NULL,
  `lname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_num` varchar(10) NOT NULL,
  PRIMARY KEY (`ssn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `food_item`
--

DROP TABLE IF EXISTS `food_item`;
CREATE TABLE IF NOT EXISTS `food_item` (
  `food_item_name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `calories` int(4) DEFAULT NULL,
  `r_id` int(255) NOT NULL,
  PRIMARY KEY (`food_item_name`,`r_id`),
  KEY `r_id` (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
  `user_id` varchar(100) NOT NULL,
  `photoid` int(255) DEFAULT NULL,
  `list_name` varchar(100) DEFAULT NULL,
  `list_user` varchar(100) DEFAULT NULL,
  KEY `user_id` (`user_id`),
  KEY `list_name` (`list_name`,`list_user`),
  KEY `photoid` (`photoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `list`
--

DROP TABLE IF EXISTS `list`;
CREATE TABLE IF NOT EXISTS `list` (
  `name` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `num_likes` int(11) NOT NULL,
  `num_restaurants` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`name`,`user_id`),
  UNIQUE KEY `date_created` (`date_created`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `made_from`
--

DROP TABLE IF EXISTS `made_from`;
CREATE TABLE IF NOT EXISTS `made_from` (
  `food_item_name` varchar(100) NOT NULL,
  `orderid` int(11) NOT NULL,
  `r_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  KEY `food_item_name` (`food_item_name`,`r_id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `time_of_order` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_price` decimal(5,0) NOT NULL,
  `employee_ssn` int(9) NOT NULL,
  `contact_info` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `time_of_order` (`time_of_order`),
  KEY `employee_ssn` (`employee_ssn`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `photo`
--

DROP TABLE IF EXISTS `photo`;
CREATE TABLE IF NOT EXISTS `photo` (
  `photo_id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category` set('decor','restaurant','menu','food','other') NOT NULL,
  `num_likes` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `file_path` varchar(255) NOT NULL,
  PRIMARY KEY (`photo_id`),
  UNIQUE KEY `date_posted` (`date_posted`),
  UNIQUE KEY `file_path` (`file_path`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `photo`
--

INSERT INTO `photo` (`photo_id`, `title`, `date_posted`, `category`, `num_likes`, `file_path`) VALUES
(1, 'Minha\'s outside', '2018-11-19 00:11:17', 'restaurant', 0, 'C:\\wamp64\\www\\test\\images\\restaurants\\1\\minhasmicrobrewery.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

DROP TABLE IF EXISTS `restaurant`;
CREATE TABLE IF NOT EXISTS `restaurant` (
  `r_id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  PRIMARY KEY (`r_id`),
  UNIQUE KEY `location` (`location`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `restaurant`
--

INSERT INTO `restaurant` (`r_id`, `name`, `location`) VALUES
(1, 'Minhas Micro Brewery', '1314 44 Avenue NE');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_category`
--

DROP TABLE IF EXISTS `restaurant_category`;
CREATE TABLE IF NOT EXISTS `restaurant_category` (
  `r_id` int(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  PRIMARY KEY (`r_id`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `content` text NOT NULL,
  `rating` enum('0','1','2','3','4','5') NOT NULL,
  `date_posted` date NOT NULL,
  `r_id` int(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `cost` enum('1','2','3') NOT NULL,
  PRIMARY KEY (`date_posted`,`r_id`,`user_id`),
  KEY `r_id` (`r_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
CREATE TABLE IF NOT EXISTS `uploads` (
  `user_id` varchar(100) NOT NULL,
  `photoid` int(255) NOT NULL,
  `r_id` int(255) NOT NULL,
  KEY `user_id` (`user_id`),
  KEY `r_id` (`r_id`),
  KEY `photoid` (`photoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `date_joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_type` varchar(5) NOT NULL,
  `fname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`, `date_joined`, `user_type`, `fname`, `lname`) VALUES
('admin', 'ed38c5a8ab903bee65878fe334a5e9ec', '2018-11-19 04:32:38', 'admin', NULL, NULL),
('idenc', '758251d6a1f4ea9abc8b872d4917b231', '2018-11-19 03:02:53', 'user', '', '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adds_to`
--
ALTER TABLE `adds_to`
  ADD CONSTRAINT `adds_to_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`),
  ADD CONSTRAINT `adds_to_ibfk_2` FOREIGN KEY (`list_name`,`list_user`) REFERENCES `list` (`name`, `user_id`),
  ADD CONSTRAINT `adds_to_ibfk_3` FOREIGN KEY (`r_id`) REFERENCES `restaurant` (`r_id`);

--
-- Constraints for table `business_hours`
--
ALTER TABLE `business_hours`
  ADD CONSTRAINT `business_hours_ibfk_1` FOREIGN KEY (`r_id`) REFERENCES `restaurant` (`r_id`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`list_name`,`list_user`) REFERENCES `list` (`name`, `user_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`photoid`) REFERENCES `photo` (`photo_id`);

--
-- Constraints for table `deletes`
--
ALTER TABLE `deletes`
  ADD CONSTRAINT `deletes_ibfk_1` FOREIGN KEY (`admin_user`) REFERENCES `user` (`username`),
  ADD CONSTRAINT `deletes_ibfk_2` FOREIGN KEY (`r_id`) REFERENCES `restaurant` (`r_id`),
  ADD CONSTRAINT `deletes_ibfk_4` FOREIGN KEY (`list_name`,`list_user`) REFERENCES `list` (`name`, `user_id`),
  ADD CONSTRAINT `deletes_ibfk_5` FOREIGN KEY (`food_name`,`food_rid`) REFERENCES `food_item` (`food_item_name`, `r_id`),
  ADD CONSTRAINT `deletes_ibfk_6` FOREIGN KEY (`photoid`) REFERENCES `photo` (`photo_id`);

--
-- Constraints for table `delivers_to`
--
ALTER TABLE `delivers_to`
  ADD CONSTRAINT `delivers_to_ibfk_1` FOREIGN KEY (`emp_ssn`) REFERENCES `employee` (`ssn`),
  ADD CONSTRAINT `delivers_to_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`);

--
-- Constraints for table `edits`
--
ALTER TABLE `edits`
  ADD CONSTRAINT `edits_ibfk_1` FOREIGN KEY (`admin_user`) REFERENCES `user` (`username`),
  ADD CONSTRAINT `edits_ibfk_2` FOREIGN KEY (`r_id`) REFERENCES `restaurant` (`r_id`),
  ADD CONSTRAINT `edits_ibfk_3` FOREIGN KEY (`list_name`,`list_user`) REFERENCES `list` (`name`, `user_id`),
  ADD CONSTRAINT `edits_ibfk_4` FOREIGN KEY (`food_name`,`food_rid`) REFERENCES `food_item` (`food_item_name`, `r_id`),
  ADD CONSTRAINT `edits_ibfk_5` FOREIGN KEY (`photoid`) REFERENCES `photo` (`photo_id`);

--
-- Constraints for table `food_item`
--
ALTER TABLE `food_item`
  ADD CONSTRAINT `food_item_ibfk_1` FOREIGN KEY (`r_id`) REFERENCES `restaurant` (`r_id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`list_name`,`list_user`) REFERENCES `list` (`name`, `user_id`),
  ADD CONSTRAINT `likes_ibfk_3` FOREIGN KEY (`photoid`) REFERENCES `photo` (`photo_id`);

--
-- Constraints for table `list`
--
ALTER TABLE `list`
  ADD CONSTRAINT `list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`);

--
-- Constraints for table `made_from`
--
ALTER TABLE `made_from`
  ADD CONSTRAINT `made_from_ibfk_1` FOREIGN KEY (`food_item_name`,`r_id`) REFERENCES `food_item` (`food_item_name`, `r_id`),
  ADD CONSTRAINT `made_from_ibfk_2` FOREIGN KEY (`orderid`) REFERENCES `order` (`order_id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`employee_ssn`) REFERENCES `employee` (`ssn`),
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`);

--
-- Constraints for table `restaurant_category`
--
ALTER TABLE `restaurant_category`
  ADD CONSTRAINT `restaurant_category_ibfk_1` FOREIGN KEY (`r_id`) REFERENCES `restaurant` (`r_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`r_id`) REFERENCES `restaurant` (`r_id`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`);

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`username`),
  ADD CONSTRAINT `uploads_ibfk_3` FOREIGN KEY (`r_id`) REFERENCES `restaurant` (`r_id`),
  ADD CONSTRAINT `uploads_ibfk_4` FOREIGN KEY (`photoid`) REFERENCES `photo` (`photo_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
