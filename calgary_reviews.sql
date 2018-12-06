-- phpMyAdmin SQL Dump
-- version 5.0.0-dev
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 06, 2018 at 08:02 AM
-- Server version: 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

SET FOREIGN_KEY_CHECKS = 0;

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

--
-- Dumping data for table `adds_to`
--

INSERT INTO `adds_to` (`user_id`, `list_name`, `list_user`, `r_id`) VALUES
('admin', 'restaurants', 'admin', 1),
('admin', 'restaurants', 'admin', 2);

-- --------------------------------------------------------

--
-- Table structure for table `business_hours`
--

DROP TABLE IF EXISTS `business_hours`;
CREATE TABLE IF NOT EXISTS `business_hours` (
  `r_id` int(255) NOT NULL,
  `day_of_week` int(1) NOT NULL,
  `open_time` time(5) NOT NULL,
  `close_time` time(5) NOT NULL,
  PRIMARY KEY (`r_id`,`day_of_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `business_hours`
--

INSERT INTO `business_hours` (`r_id`, `day_of_week`, `open_time`, `close_time`) VALUES
(1, 1, '11:00:00.00000', '21:00:00.00000'),
(1, 2, '11:00:00.00000', '21:00:00.00000'),
(1, 3, '11:00:00.00000', '21:00:00.00000'),
(1, 4, '00:00:00.00000', '00:00:00.00000'),
(1, 5, '22:00:00.00000', '02:00:00.00000'),
(1, 6, '00:00:00.00000', '00:00:00.00000'),
(1, 7, '00:00:00.00000', '00:00:00.00000'),
(2, 1, '10:00:00.00000', '23:00:00.00000'),
(2, 2, '10:00:00.00000', '23:00:00.00000'),
(2, 3, '10:00:00.00000', '23:00:00.00000'),
(2, 4, '10:00:00.00000', '23:00:00.00000'),
(2, 5, '10:00:00.00000', '23:00:00.00000'),
(2, 6, '10:00:00.00000', '23:00:00.00000'),
(2, 7, '10:00:00.00000', '23:00:00.00000'),
(3, 1, '00:00:00.00000', '00:00:00.00000'),
(3, 2, '00:00:00.00000', '00:00:00.00000'),
(3, 3, '00:00:00.00000', '00:00:00.00000'),
(3, 4, '00:00:00.00000', '00:00:00.00000'),
(3, 5, '00:00:00.00000', '00:00:00.00000'),
(3, 6, '00:00:00.00000', '00:00:00.00000'),
(3, 7, '00:00:00.00000', '00:00:00.00000');

--
-- Triggers `business_hours`
--
DROP TRIGGER IF EXISTS `check_day`;
DELIMITER $$
CREATE TRIGGER `check_day` BEFORE INSERT ON `business_hours` FOR EACH ROW BEGIN
IF new.day_of_week NOT IN (1, 2, 3, 4, 5, 6, 7) THEN
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
  `r_id` int(255) DEFAULT NULL,
  `photoid` int(255) DEFAULT NULL,
  `list_name` varchar(100) DEFAULT NULL,
  `list_user` varchar(100) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `food_rid` int(255) DEFAULT NULL,
  `time_edited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_user`,`time_edited`),
  KEY `r_id` (`r_id`),
  KEY `list_name` (`list_name`,`list_user`),
  KEY `food_name` (`food_name`,`food_rid`),
  KEY `admin_user` (`admin_user`),
  KEY `photoid` (`photoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `deletes`
--

INSERT INTO `deletes` (`admin_user`, `r_id`, `photoid`, `list_name`, `list_user`, `food_name`, `food_rid`, `time_edited`) VALUES
('admin', 1, 8, NULL, NULL, NULL, NULL, '2018-12-04 23:33:04'),
('admin', NULL, 14, NULL, NULL, NULL, NULL, '2018-12-06 02:41:14');

-- --------------------------------------------------------

--
-- Table structure for table `delivers_to`
--

DROP TABLE IF EXISTS `delivers_to`;
CREATE TABLE IF NOT EXISTS `delivers_to` (
  `emp_ssn` int(9) NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`emp_ssn`,`order_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `edits`
--

DROP TABLE IF EXISTS `edits`;
CREATE TABLE IF NOT EXISTS `edits` (
  `edit_user` varchar(100) NOT NULL,
  `r_id` int(255) DEFAULT NULL,
  `photoid` int(255) DEFAULT NULL,
  `list_name` varchar(100) DEFAULT NULL,
  `list_user` varchar(100) DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `food_rid` int(255) DEFAULT NULL,
  `time_edited` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`edit_user`,`time_edited`),
  KEY `r_id` (`r_id`),
  KEY `list_name` (`list_name`,`list_user`),
  KEY `food_name` (`food_name`,`food_rid`),
  KEY `photoid` (`photoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `edits`
--

INSERT INTO `edits` (`edit_user`, `r_id`, `photoid`, `list_name`, `list_user`, `food_name`, `food_rid`, `time_edited`) VALUES
('admin', 1, NULL, NULL, NULL, NULL, NULL, '2018-12-06 02:28:47'),
('admin', 1, NULL, NULL, NULL, NULL, NULL, '2018-12-06 02:28:55'),
('admin', 1, NULL, NULL, NULL, NULL, NULL, '2018-12-06 02:29:24'),
('admin', 1, NULL, NULL, NULL, NULL, NULL, '2018-12-06 02:35:20'),
('admin', 1, NULL, NULL, NULL, NULL, NULL, '2018-12-06 02:45:16'),
('admin', 1, NULL, NULL, NULL, NULL, NULL, '2018-12-06 07:52:24');

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
  `phone_num` varchar(12) NOT NULL,
  PRIMARY KEY (`ssn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`ssn`, `fname`, `lname`, `email`, `phone_num`) VALUES
(123456789, 'John', 'Smith', 'johnsmith@john.com', '123-456-7890');

-- --------------------------------------------------------

--
-- Table structure for table `food_item`
--

DROP TABLE IF EXISTS `food_item`;
CREATE TABLE IF NOT EXISTS `food_item` (
  `food_item_name` varchar(100) NOT NULL,
  `price` float(4,2) NOT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `calories` int(4) DEFAULT NULL,
  `r_id` int(255) NOT NULL,
  PRIMARY KEY (`food_item_name`,`r_id`),
  KEY `r_id` (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `food_item`
--

INSERT INTO `food_item` (`food_item_name`, `price`, `picture_path`, `calories`, `r_id`) VALUES
('Beer', 4.50, 'images\\restaurants\\1\\57.jpg', 400, 1),
('Pizza', 10.00, 'images\\restaurants\\1\\pizza.jpg', 1000, 1);

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
  UNIQUE KEY `user_id_3` (`user_id`,`photoid`),
  UNIQUE KEY `user_id_2` (`user_id`,`list_name`,`list_user`) USING BTREE,
  KEY `list_name` (`list_name`,`list_user`),
  KEY `photoid` (`photoid`),
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`user_id`, `photoid`, `list_name`, `list_user`) VALUES
('admin', 3, NULL, NULL),
('admin', NULL, 'restaurants', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `list`
--

DROP TABLE IF EXISTS `list`;
CREATE TABLE IF NOT EXISTS `list` (
  `name` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `num_restaurants` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`name`,`user_id`),
  UNIQUE KEY `date_created` (`date_created`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `list`
--

INSERT INTO `list` (`name`, `date_created`, `num_restaurants`, `user_id`) VALUES
('restaurants', '2018-12-06 03:43:11', 0, 'admin');

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
  PRIMARY KEY (`food_item_name`,`orderid`),
  KEY `food_item_name` (`food_item_name`,`r_id`),
  KEY `orderid` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `made_from`
--

INSERT INTO `made_from` (`food_item_name`, `orderid`, `r_id`, `quantity`) VALUES
('Beer', 1, 1, 1),
('Pizza', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `time_of_order` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_price` decimal(4,2) NOT NULL,
  `employee_ssn` int(9) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `time_of_order` (`time_of_order`),
  KEY `employee_ssn` (`employee_ssn`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `time_of_order`, `total_price`, `employee_ssn`, `email`, `address`, `user_id`) VALUES
(1, '2018-12-06 02:46:52', '15.00', NULL, 'john@john.ca', '321 boot road', 'admin');

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
  `file_path` varchar(255) NOT NULL,
  PRIMARY KEY (`photo_id`),
  UNIQUE KEY `file_path` (`file_path`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `photo`
--

INSERT INTO `photo` (`photo_id`, `title`, `date_posted`, `category`, `file_path`) VALUES
(2, 'o.jpg', '2018-11-27 08:19:14', 'decor', 'images\\restaurants\\2\\o.jpg'),
(3, 'menu.jpg', '2018-11-27 08:19:14', 'menu', 'images\\restaurants\\2\\menu.jpg'),
(4, 'sushi.jpg', '2018-11-27 08:19:14', 'food', 'images\\restaurants\\2\\sushi.jpg'),
(5, 'pizza.jpg', '2018-11-30 05:13:01', 'food', 'images\\restaurants\\1\\pizza.jpg'),
(6, 'brewery.jpg', '2018-11-30 05:13:01', 'decor', 'images\\restaurants\\1\\brewery.jpg'),
(7, 'download.jpg', '2018-11-30 06:06:35', 'decor', 'images\\restaurants\\3\\download.jpg'),
(8, 'metal.png', '2018-12-02 03:53:41', 'decor', 'images\\restaurants\\1\\metal.png'),
(14, '57.jpg', '2018-12-06 02:35:20', 'decor', 'images\\restaurants\\1\\57.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

DROP TABLE IF EXISTS `restaurant`;
CREATE TABLE IF NOT EXISTS `restaurant` (
  `r_id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `wifi` tinyint(1) DEFAULT NULL,
  `delivery` tinyint(1) DEFAULT NULL,
  `alcohol` tinyint(1) DEFAULT NULL,
  `phone_num` varchar(15) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `pending` binary(1) NOT NULL,
  PRIMARY KEY (`r_id`),
  UNIQUE KEY `location` (`location`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `restaurant`
--

INSERT INTO `restaurant` (`r_id`, `name`, `location`, `wifi`, `delivery`, `alcohol`, `phone_num`, `website`, `pending`) VALUES
(1, 'Minhas Micro Brewery', '1314 44 Avenue NE', 1, 0, 0, '231231321', 'http://minhasbrewery.com/minhas-micro-brewery-calgary', 0x30),
(2, 'Kinjo Sushi', '5005 Dalhousie Drive NW Unit 415', 0, 0, 0, '(403) 452-8389', 'https://www.kinjosushiandgrill.com/', 0x30),
(3, 'Iced tea hut', 'kelowna', 1, 1, 1, 'heheh', '', 0x30);

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

--
-- Dumping data for table `restaurant_category`
--

INSERT INTO `restaurant_category` (`r_id`, `category`) VALUES
(1, 'Beer'),
(1, 'Pizza'),
(2, 'Sushi');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE IF NOT EXISTS `review` (
  `content` text NOT NULL,
  `rating` enum('0','1','2','3','4','5') NOT NULL,
  `date_posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `r_id` int(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `cost` enum('1','2','3') NOT NULL,
  PRIMARY KEY (`date_posted`,`r_id`,`user_id`),
  KEY `r_id` (`r_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`content`, `rating`, `date_posted`, `r_id`, `user_id`, `cost`) VALUES
('Fun tour! You see the entire production process from grain to can! Absolutely loved the samples we were given and was blown away by how delicious the beers were. I\'m not too much of a beer person, but I think Minhas is going to change that real soon.', '2', '2018-11-25 02:23:08', 1, 'abcd', '3'),
('It was alright', '4', '2018-11-30 05:57:14', 1, 'abcd', '1'),
('Best iced tea low price', '4', '2018-11-30 06:07:22', 3, 'ThebigJob', '1'),
('11', '5', '2018-11-30 06:11:11', 2, 'ThebigJob', '1'),
('sadasd', '0', '2018-11-30 06:15:51', 2, 'admin', '1'),
('sada', '3', '2018-11-30 06:18:27', 2, 'admin', '1'),
('dfg', '5', '2018-11-30 06:18:58', 2, 'ThebigJob', '3'),
('sgfgdg', '5', '2018-11-30 06:27:37', 3, 'ThebigJob', '3');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
CREATE TABLE IF NOT EXISTS `uploads` (
  `user_id` varchar(100) NOT NULL,
  `photoid` int(255) NOT NULL,
  `r_id` int(255) NOT NULL,
  PRIMARY KEY (`photoid`) USING BTREE,
  KEY `r_id` (`r_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`user_id`, `photoid`, `r_id`) VALUES
('admin', 3, 2),
('admin', 4, 2),
('admin', 5, 1),
('admin', 6, 1),
('ThebigJob', 7, 3);

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
('abcd', '81dc9bdb52d04dc20036dbd8313ed055', '2018-11-25 02:22:33', 'user', 'ab', 'cd'),
('admin', 'ed38c5a8ab903bee65878fe334a5e9ec', '2018-11-19 04:32:38', 'admin', NULL, NULL),
('idenc', '758251d6a1f4ea9abc8b872d4917b231', '2018-11-19 03:02:53', 'user', '', ''),
('ThebigJob', '42f749ade7f9e195bf475f37a44cafcb', '2018-11-30 05:56:47', 'user', '', '');

--
-- Constraints for dumped tables
--
SET FOREIGN_KEY_CHECKS = 1;
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
  ADD CONSTRAINT `delivers_to_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`);

--
-- Constraints for table `edits`
--
ALTER TABLE `edits`
  ADD CONSTRAINT `edits_ibfk_1` FOREIGN KEY (`edit_user`) REFERENCES `user` (`username`),
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
