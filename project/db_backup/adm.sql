-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2017 at 09:39 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adm`
--

-- --------------------------------------------------------

--
-- Table structure for table `alert`
--

CREATE TABLE `alert` (
  `alertID` int(11) UNSIGNED NOT NULL,
  `noticeID` int(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `usertype` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `classesID` int(11) UNSIGNED NOT NULL,
  `classes` varchar(60) NOT NULL,
  `classes_numeric` int(11) NOT NULL,
  `teacherID` int(11) NOT NULL,
  `note` text,
  `create_date` datetime DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `create_userID` int(11) DEFAULT NULL,
  `create_username` varchar(20) DEFAULT NULL,
  `create_usertype` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`classesID`, `classes`, `classes_numeric`, `teacherID`, `note`, `create_date`, `modify_date`, `create_userID`, `create_username`, `create_usertype`) VALUES
(1, 'Class one', 1, 1, '', '2016-11-16 17:11:05', '2016-11-16 17:11:05', 1, 'admin', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `expenseID` int(11) UNSIGNED NOT NULL,
  `create_date` date NOT NULL,
  `date` date NOT NULL,
  `expense` varchar(128) NOT NULL,
  `amount` varchar(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `uname` varchar(60) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `expenseyear` year(4) NOT NULL,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `feetype`
--

CREATE TABLE `feetype` (
  `feetypeID` int(11) UNSIGNED NOT NULL,
  `feetype` varchar(60) NOT NULL,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ini_config`
--

CREATE TABLE `ini_config` (
  `configID` int(11) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `config_key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ini_config`
--

INSERT INTO `ini_config` (`configID`, `type`, `config_key`, `value`) VALUES
(1, 'paypal', 'paypal_api_username', 'rid_api1.rid.com'),
(2, 'paypal', 'paypal_api_password', 'Y2R292RFWC3JSR3M'),
(3, 'paypal', 'paypal_api_signature', 'AFcWxV21C7fd0v3bYYYRCpSSRl31ACtAvPn-FtB3zZPUFjhDgcJeS0Ei'),
(4, 'paypal', 'paypal_email', 'rid@rid.com'),
(5, 'paypal', 'paypal_demo', 'TRUE'),
(6, 'stripe', 'stripe_private_key', ''),
(7, 'stripe', 'stripe_public_key', '');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoiceID` int(11) UNSIGNED NOT NULL,
  `classesID` int(11) NOT NULL,
  `classes` varchar(128) NOT NULL,
  `studentID` int(11) NOT NULL,
  `student` varchar(128) NOT NULL,
  `roll` varchar(128) NOT NULL,
  `feetype` varchar(128) NOT NULL,
  `amount` varchar(20) NOT NULL,
  `paidamount` varchar(11) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `usertype` varchar(20) DEFAULT NULL,
  `uname` varchar(60) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `paymenttype` varchar(128) DEFAULT NULL,
  `date` date NOT NULL,
  `paiddate` date DEFAULT NULL,
  `year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoiceID`, `classesID`, `classes`, `studentID`, `student`, `roll`, `feetype`, `amount`, `paidamount`, `userID`, `usertype`, `uname`, `status`, `paymenttype`, `date`, `paiddate`, `year`) VALUES
(1, 1, 'Class one', 1, 'Rid Islam', '01', 'admission fee', '500', NULL, NULL, NULL, NULL, 0, NULL, '2015-10-01', NULL, 2015);

-- --------------------------------------------------------

--
-- Table structure for table `issue`
--

CREATE TABLE `issue` (
  `issueID` int(11) UNSIGNED NOT NULL,
  `lID` varchar(128) NOT NULL,
  `bookID` int(11) NOT NULL,
  `book` varchar(60) NOT NULL,
  `author` varchar(100) NOT NULL,
  `serial_no` varchar(40) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `fine` varchar(11) DEFAULT NULL,
  `note` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mailandsms`
--

CREATE TABLE `mailandsms` (
  `mailandsmsID` int(11) UNSIGNED NOT NULL,
  `users` varchar(15) NOT NULL,
  `type` varchar(10) NOT NULL,
  `message` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mailandsmstemplate`
--

CREATE TABLE `mailandsmstemplate` (
  `mailandsmstemplateID` int(11) UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `user` varchar(15) NOT NULL,
  `type` varchar(10) NOT NULL,
  `template` text NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mailandsmstemplatetag`
--

CREATE TABLE `mailandsmstemplatetag` (
  `mailandsmstemplatetagID` int(11) UNSIGNED NOT NULL,
  `usersID` int(11) NOT NULL,
  `name` varchar(15) NOT NULL,
  `tagname` varchar(128) NOT NULL,
  `mailandsmstemplatetag_extra` varchar(255) DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `mailandsmstemplatetag`
--

INSERT INTO `mailandsmstemplatetag` (`mailandsmstemplatetagID`, `usersID`, `name`, `tagname`, `mailandsmstemplatetag_extra`, `create_date`) VALUES
(1, 1, 'student', '[student_name]', NULL, '2015-06-30 18:44:10'),
(2, 1, 'student', '[student_class]', NULL, '2015-06-30 18:43:56'),
(3, 1, 'student', '[student_roll]', NULL, '2015-06-30 18:44:21'),
(4, 1, 'student', '[student_dob]', NULL, '2015-06-30 18:45:24'),
(5, 1, 'student', '[student_gender]', NULL, '2015-06-30 18:47:01'),
(6, 1, 'student', '[student_religion]', NULL, '2015-06-30 18:47:01'),
(7, 1, 'student', '[student_email]', NULL, '2015-06-30 18:47:40'),
(8, 1, 'student', '[student_phone]', NULL, '2015-06-30 18:47:40'),
(9, 1, 'student', '[student_section]', NULL, '2015-06-30 18:48:47'),
(10, 1, 'student', '[student_username]', NULL, '2015-06-30 18:48:47'),
(11, 2, 'parents', '[guardian_name]', NULL, '2015-07-06 10:09:16'),
(12, 2, 'parents', '[father''s_name]', NULL, '2015-07-06 10:11:42'),
(13, 2, 'parents', '[mother''s_name]', NULL, '2015-07-06 10:11:42'),
(14, 2, 'parents', '[father''s_profession]', NULL, '2015-07-06 10:14:32'),
(15, 2, 'parents', '[mother''s_profession]', NULL, '2015-07-06 10:14:32'),
(16, 2, 'parents', '[parents_email]', NULL, '2015-07-06 10:20:37'),
(17, 2, 'parents', '[parents_phone]', NULL, '2015-07-06 10:20:44'),
(18, 2, 'parents', '[parents_address]', NULL, '2015-07-06 10:20:53'),
(19, 2, 'parents', '[parents_username]', NULL, '2015-07-06 10:21:00'),
(20, 3, 'teacher', '[teacher_name]\r\n', NULL, '2015-07-06 10:41:13'),
(21, 3, 'teacher', '[teacher_designation]', NULL, '2015-07-06 10:41:13'),
(22, 3, 'teacher', '[teacher_dob]', NULL, '2015-07-06 10:41:13'),
(23, 3, 'teacher', '[teacher_gender]', NULL, '2015-07-06 10:41:13'),
(24, 3, 'teacher', '[teacher_religion]', NULL, '2015-07-06 10:41:13'),
(25, 3, 'teacher', '[teacher_email]', NULL, '2015-07-06 10:41:13'),
(26, 3, 'teacher', '[teacher_phone]\r\n', NULL, '2015-07-06 10:41:13'),
(27, 3, 'teacher', '[teacher_address]', NULL, '2015-07-06 10:41:13'),
(28, 3, 'teacher', '[teacher_jod]', NULL, '2015-07-06 12:25:07'),
(29, 3, 'teacher', '[teacher_username]', NULL, '2015-07-06 10:41:13'),
(30, 4, 'librarian', '[librarian_name]', NULL, '2015-07-06 11:05:44'),
(31, 4, 'librarian', '[librarian_dob]', NULL, '2015-07-06 11:05:48'),
(32, 4, 'librarian', '[librarian_gender]', NULL, '2015-07-06 11:05:52'),
(33, 4, 'librarian', '[librarian_religion]', NULL, '2015-07-06 11:05:55'),
(34, 4, 'librarian', '[librarian_email]', NULL, '2015-07-06 11:05:59'),
(35, 4, 'librarian', '[librarian_phone]', NULL, '2015-07-06 11:06:20'),
(36, 4, 'librarian', '[librarian_address]', NULL, '2015-07-06 11:06:27'),
(37, 4, 'librarian', '[librarian_jod]', NULL, '2015-07-06 12:25:17'),
(38, 4, 'librarian', '[librarian_username]', NULL, '2015-07-06 11:06:36'),
(39, 5, 'accountant', '[accountant_name]', NULL, '2015-07-06 11:06:59'),
(40, 5, 'accountant', '[accountant_dob]', NULL, '2015-07-06 11:07:02'),
(41, 5, 'accountant', '[accountant_gender]', NULL, '2015-07-06 11:07:04'),
(42, 5, 'accountant', '[accountant_religion]', NULL, '2015-07-06 11:07:07'),
(43, 5, 'accountant', '[accountant_email]', NULL, '2015-07-06 11:07:10'),
(44, 5, 'accountant', '[accountant_phone]', NULL, '2015-07-06 11:07:13'),
(45, 5, 'accountant', '[accountant_address]', NULL, '2015-07-06 11:07:15'),
(46, 5, 'accountant', '[accountant_jod]', NULL, '2015-07-06 12:25:24'),
(47, 5, 'accountant', '[accountant_username]', NULL, '2015-07-06 11:07:21'),
(48, 1, 'student', '[student_result_table]', NULL, '2015-09-08 04:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `mediaID` int(11) UNSIGNED NOT NULL,
  `userID` int(11) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `mcategoryID` int(11) NOT NULL DEFAULT '0',
  `file_name` varchar(255) NOT NULL,
  `file_name_display` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `media_category`
--

CREATE TABLE `media_category` (
  `mcategoryID` int(11) UNSIGNED NOT NULL,
  `userID` int(11) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `folder_name` varchar(255) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `media_share`
--

CREATE TABLE `media_share` (
  `shareID` int(11) UNSIGNED NOT NULL,
  `classesID` int(11) NOT NULL,
  `public` int(11) NOT NULL,
  `file_or_folder` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `messageID` int(11) UNSIGNED NOT NULL,
  `email` varchar(128) NOT NULL,
  `receiverID` int(11) NOT NULL,
  `receiverType` varchar(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `attach` text,
  `attach_file_name` text,
  `userID` int(11) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `useremail` varchar(40) NOT NULL,
  `year` year(4) NOT NULL,
  `date` date NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `read_status` tinyint(1) NOT NULL,
  `from_status` int(11) NOT NULL,
  `to_status` int(11) NOT NULL,
  `fav_status` tinyint(1) NOT NULL,
  `fav_status_sent` tinyint(1) NOT NULL,
  `reply_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `version` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`version`) VALUES
(47);

-- --------------------------------------------------------

--
-- Table structure for table `notice`
--

CREATE TABLE `notice` (
  `noticeID` int(11) UNSIGNED NOT NULL,
  `title` varchar(128) NOT NULL,
  `notice` text NOT NULL,
  `year` year(4) NOT NULL,
  `date` date NOT NULL,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `parentID` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `father_name` varchar(60) NOT NULL,
  `mother_name` varchar(60) NOT NULL,
  `father_profession` varchar(40) NOT NULL,
  `mother_profession` varchar(40) NOT NULL,
  `email` varchar(40) DEFAULT NULL,
  `phone` tinytext,
  `address` text,
  `photo` varchar(200) DEFAULT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(128) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `create_userID` int(11) DEFAULT NULL,
  `create_username` varchar(20) DEFAULT NULL,
  `create_usertype` varchar(20) DEFAULT NULL,
  `parentactive` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`parentID`, `name`, `father_name`, `mother_name`, `father_profession`, `mother_profession`, `email`, `phone`, `address`, `photo`, `username`, `password`, `usertype`, `create_date`, `modify_date`, `create_userID`, `create_username`, `create_usertype`, `parentactive`) VALUES
(1, 'Isable Kaif', 'Salman Khan', 'Katrina Kaif', 'Business', 'House wife', 'isable@gmail.com', '+88016766677', '1/1 G Mirpur 1, Dhaka 1216', 'defualt.png', 'isable', 'f966a7cd1c31461d4fc26dbff6e2f8c510b1078b3c170678676ffb07de15d2e121309698f4afc0dec6844301d150ce26422e36a8d17d53adfebcac3453922e3e', 'Parent', '2016-11-16 17:11:05', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1),
(4, 'Delwar Hossain', 'Delwar Hossain', 'Hazera Begum', 'Business', 'House wife', 'delwarhossain@gmail.com', '1716225133', 'Dhaka', NULL, 'hossain', 'f966a7cd1c31461d4fc26dbff6e2f8c510b1078b3c170678676ffb07de15d2e121309698f4afc0dec6844301d150ce26422e36a8d17d53adfebcac3453922e3e', 'Parent', '2016-11-16 17:11:05', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1),
(5, 'Arun kumar', 'Arun Kumar', 'Misses halder', 'Business', 'House wife', 'arunkumar@gmail.com', '1777154555', 'Pabna', NULL, 'kumar', 'f966a7cd1c31461d4fc26dbff6e2f8c510b1078b3c170678676ffb07de15d2e121309698f4afc0dec6844301d150ce26422e36a8d17d53adfebcac3453922e3e', 'Parent', '2016-11-16 17:11:05', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1),
(6, 'Delwar Hossain', 'Delwar Hossain', 'Hazera Begum', 'Business', 'House wife', 'delwarhossainaha@gmail.com', '1716225133', 'Dhaka', NULL, 'dhossain', 'f966a7cd1c31461d4fc26dbff6e2f8c510b1078b3c170678676ffb07de15d2e121309698f4afc0dec6844301d150ce26422e36a8d17d53adfebcac3453922e3e', 'Parent', '2016-11-16 17:11:05', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1),
(7, 'Maria', 'hayat', 'haay', 'hjj', 'jhbj', 'mmariahayat109@gmail.com', '212121212', 'er43645yhrth', '9d3a4893677f93ca7767bb0a1097c8dbcdbd59de253c7e635deab7c273a050077088b3d012eadb64bc6510f20778ce502739677c69eb61ff41d99981ab82b27a.jpg', 'mariahayat1100', '1433ddb807c2f0c7ec93adee1e31e3ed60fa8857ea61bb0e275c138e04df0bd62806d73af95e49cdfde76441fc9ae99d85f994226545d3c58630530c2ef08902', 'Parent', '2017-09-30 09:55:04', '2017-09-30 09:55:04', 2, 'librarian', 'Librarian', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentID` int(11) UNSIGNED NOT NULL,
  `invoiceID` int(11) NOT NULL,
  `studentID` int(11) NOT NULL,
  `paymentamount` varchar(20) NOT NULL,
  `paymenttype` varchar(128) NOT NULL,
  `paymentdate` date NOT NULL,
  `paymentmonth` varchar(10) NOT NULL,
  `paymentyear` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `promotionsubject`
--

CREATE TABLE `promotionsubject` (
  `promotionSubjectID` int(11) UNSIGNED NOT NULL,
  `classesID` int(11) NOT NULL,
  `subjectID` int(11) NOT NULL,
  `subjectCode` tinytext NOT NULL,
  `subjectMark` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reply_msg`
--

CREATE TABLE `reply_msg` (
  `replyID` int(11) UNSIGNED NOT NULL,
  `messageID` int(11) NOT NULL,
  `reply_msg` text NOT NULL,
  `status` int(11) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reset`
--

CREATE TABLE `reset` (
  `resetID` int(11) UNSIGNED NOT NULL,
  `keyID` varchar(128) NOT NULL,
  `email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `school_sessions`
--

CREATE TABLE `school_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `school_sessions`
--

INSERT INTO `school_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('783243f19da47eaccd662d121b0b265e', '0.0.0.0', 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:38.0) Gecko/20100101 Firefox/38.0', 1561580923, 'a:8:{s:9:"user_data";s:0:"";s:4:"name";s:5:"Dipok";s:5:"email";s:16:"info@inilabs.net";s:8:"usertype";s:5:"Admin";s:8:"username";s:5:"admin";s:5:"photo";s:8:"site.png";s:4:"lang";s:7:"english";s:8:"loggedin";b:1;}');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `sectionID` int(11) UNSIGNED NOT NULL,
  `section` varchar(60) NOT NULL,
  `category` varchar(128) NOT NULL,
  `classesID` int(11) NOT NULL,
  `teacherID` int(11) NOT NULL,
  `note` text,
  `create_date` datetime DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `create_userID` int(11) DEFAULT NULL,
  `create_username` varchar(20) DEFAULT NULL,
  `create_usertype` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`sectionID`, `section`, `category`, `classesID`, `teacherID`, `note`, `create_date`, `modify_date`, `create_userID`, `create_username`, `create_usertype`) VALUES
(1, 'A', 'Good', 1, 1, '', '2016-11-16 17:11:05', '2016-11-16 17:11:05', 1, 'admin', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `settingID` int(11) UNSIGNED NOT NULL,
  `sname` text,
  `phone` tinytext,
  `address` text,
  `email` varchar(40) DEFAULT NULL,
  `automation` int(11) DEFAULT NULL,
  `currency_code` varchar(11) DEFAULT NULL,
  `currency_symbol` text,
  `footer` text,
  `photo` varchar(128) DEFAULT NULL,
  `purchase_code` varchar(255) DEFAULT NULL,
  `language` varchar(50) NOT NULL DEFAULT 'english',
  `theme` varchar(250) NOT NULL DEFAULT 'Basic',
  `fontorbackend` int(11) NOT NULL DEFAULT '1',
  `updateversion` text NOT NULL,
  `attendance` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`settingID`, `sname`, `phone`, `address`, `email`, `automation`, `currency_code`, `currency_symbol`, `footer`, `photo`, `purchase_code`, `language`, `theme`, `fontorbackend`, `updateversion`, `attendance`) VALUES
(1, 'Admission System', '03334713883', '123\r\n', 'mubasharahmad_pk@hotmail.com', 5, 'PKR', 'Rs.', NULL, 'site.png', 'f541d688-9d40-40db-99fb-65e6f80692ab', 'english', 'Basic', 1, '1', 'day');

-- --------------------------------------------------------

--
-- Table structure for table `smssettings`
--

CREATE TABLE `smssettings` (
  `smssettingsID` int(11) UNSIGNED NOT NULL,
  `types` varchar(255) DEFAULT NULL,
  `field_names` varchar(255) DEFAULT NULL,
  `field_values` varchar(255) DEFAULT NULL,
  `smssettings_extra` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `smssettings`
--

INSERT INTO `smssettings` (`smssettingsID`, `types`, `field_names`, `field_values`, `smssettings_extra`) VALUES
(1, 'clickatell', 'clickatell_username', '', NULL),
(2, 'clickatell', 'clickatell_password', '', NULL),
(3, 'clickatell', 'clickatell_api_key', '', NULL),
(4, 'twilio', 'twilio_accountSID', '', NULL),
(5, 'twilio', 'twilio_authtoken', '', NULL),
(6, 'twilio', 'twilio_fromnumber', '', NULL),
(7, 'bulk', 'bulk_username', '', NULL),
(8, 'bulk', 'bulk_password', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `studentID` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `dob` date NOT NULL,
  `sex` varchar(10) NOT NULL,
  `religion` varchar(25) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `phone` tinytext,
  `address` text,
  `classesID` int(11) NOT NULL,
  `roll` tinytext NOT NULL,
  `library` int(11) NOT NULL,
  `hostel` int(11) NOT NULL,
  `transport` int(11) NOT NULL,
  `create_date` date NOT NULL,
  `totalamount` varchar(128) DEFAULT NULL,
  `paidamount` varchar(128) DEFAULT NULL,
  `photo` varchar(200) DEFAULT NULL,
  `parentID` int(11) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(128) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `modify_date` datetime DEFAULT NULL,
  `create_userID` int(11) DEFAULT NULL,
  `create_username` varchar(20) DEFAULT NULL,
  `create_usertype` varchar(20) DEFAULT NULL,
  `studentactive` int(11) DEFAULT NULL,
  `fhome` varchar(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`studentID`, `name`, `dob`, `sex`, `religion`, `email`, `phone`, `address`, `classesID`, `roll`, `library`, `hostel`, `transport`, `create_date`, `totalamount`, `paidamount`, `photo`, `parentID`, `year`, `username`, `password`, `usertype`, `modify_date`, `create_userID`, `create_username`, `create_usertype`, `studentactive`, `fhome`) VALUES
(1, 'Rid Islam', '2010-02-09', 'Male', 'Islam', 'rid.islam@gmail.com', '+88016766677758', '1/1 G Mirpur 1, Dhaka 1216', 1, '01', 0, 0, 0, '2016-11-16', '500', '0', 'defualt.png', 1, 2015, 'ridislam', 'f966a7cd1c31461d4fc26dbff6e2f8c510b1078b3c170678676ffb07de15d2e121309698f4afc0dec6844301d150ce26422e36a8d17d53adfebcac3453922e3e', 'Student', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1, ''),
(2, 'rakib', '2010-02-02', 'Male', 'Islam', 'newemail@email.com', '8801676667726', 'Dhaka', 1, '52', 0, 0, 0, '2016-11-16', NULL, NULL, NULL, 5, 2015, 'rakib', 'f966a7cd1c31461d4fc26dbff6e2f8c510b1078b3c170678676ffb07de15d2e121309698f4afc0dec6844301d150ce26422e36a8d17d53adfebcac3453922e3e', 'Student', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1, ''),
(3, 'newemail', '2010-02-02', 'Male', 'hindu', 'rakib@gmail.com', '8845454654', 'Pabna', 1, '25', 0, 0, 0, '2016-11-16', NULL, NULL, NULL, 1, 2015, 'newuser', 'f966a7cd1c31461d4fc26dbff6e2f8c510b1078b3c170678676ffb07de15d2e121309698f4afc0dec6844301d150ce26422e36a8d17d53adfebcac3453922e3e', 'Student', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subjectID` int(11) UNSIGNED NOT NULL,
  `classesID` int(11) NOT NULL,
  `teacherID` int(11) NOT NULL,
  `subject` varchar(60) NOT NULL,
  `subject_author` varchar(100) DEFAULT NULL,
  `subject_code` tinytext NOT NULL,
  `teacher_name` varchar(60) NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `modify_date` datetime DEFAULT NULL,
  `create_userID` int(11) DEFAULT NULL,
  `create_username` varchar(20) DEFAULT NULL,
  `create_usertype` varchar(20) DEFAULT NULL,
  `subjectactive` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `systemadmin`
--

CREATE TABLE `systemadmin` (
  `systemadminID` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `dob` date NOT NULL,
  `sex` varchar(10) NOT NULL,
  `religion` varchar(25) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `phone` tinytext,
  `address` text,
  `jod` date NOT NULL,
  `photo` varchar(200) DEFAULT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(128) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `create_date` datetime NOT NULL,
  `modify_date` datetime NOT NULL,
  `create_userID` int(11) NOT NULL,
  `create_username` varchar(40) NOT NULL,
  `create_usertype` varchar(20) NOT NULL,
  `systemadminactive` int(11) NOT NULL,
  `systemadminextra1` varchar(128) DEFAULT NULL,
  `systemadminextra2` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `systemadmin`
--

INSERT INTO `systemadmin` (`systemadminID`, `name`, `dob`, `sex`, `religion`, `email`, `phone`, `address`, `jod`, `photo`, `username`, `password`, `usertype`, `create_date`, `modify_date`, `create_userID`, `create_username`, `create_usertype`, `systemadminactive`, `systemadminextra1`, `systemadminextra2`) VALUES
(1, 'Mubashar', '2011-01-01', 'Male', 'Unknown', 'mubasharahmad_pk@hotmail.com', '03334713883', '123\r\n', '2016-11-16', 'defualt.png', 'admin', '6a12834fd4f0448520b824ee2e8c17ac4dab6ac38ed94201dd1f669078d193d7499471c5e1e7f71548d41df8a0c8cdd3e872a26230ffb987759a092650048d9d', 'Admin', '2016-11-16 04:14:36', '2016-11-16 04:14:36', 0, 'admin', 'Admin', 1, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `dob` date NOT NULL,
  `sex` varchar(10) NOT NULL,
  `religion` varchar(25) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `phone` tinytext,
  `address` text,
  `jod` date NOT NULL,
  `photo` varchar(200) DEFAULT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(128) NOT NULL,
  `usertype` varchar(20) NOT NULL,
  `modify_date` datetime NOT NULL,
  `create_date` datetime DEFAULT NULL,
  `create_userID` int(11) DEFAULT NULL,
  `create_username` varchar(20) DEFAULT NULL,
  `create_usertype` varchar(20) DEFAULT NULL,
  `useractive` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `name`, `dob`, `sex`, `religion`, `email`, `phone`, `address`, `jod`, `photo`, `username`, `password`, `usertype`, `modify_date`, `create_date`, `create_userID`, `create_username`, `create_usertype`, `useractive`) VALUES
(1, 'Rid', '2010-02-02', 'Male', 'Islam', 'rid@gmail.com', '8801676667726', 'Dhaka', '2010-02-02', NULL, 'admin', '6a12834fd4f0448520b824ee2e8c17ac4dab6ac38ed94201dd1f669078d193d7499471c5e1e7f71548d41df8a0c8cdd3e872a26230ffb987759a092650048d9d', 'Accountant', '2016-11-16 17:11:05', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1),
(2, 'Visitor', '2010-02-02', 'Male', 'hindu', 'dipok@gmail.com', '8845454654', 'Pabna', '2010-02-02', NULL, 'visitor', '6a12834fd4f0448520b824ee2e8c17ac4dab6ac38ed94201dd1f669078d193d7499471c5e1e7f71548d41df8a0c8cdd3e872a26230ffb987759a092650048d9d', 'Visitor', '2016-11-16 17:11:05', '2016-11-16 17:11:05', 1, 'admin', 'Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `visitorinfo`
--

CREATE TABLE `visitorinfo` (
  `visitorID` bigint(12) UNSIGNED NOT NULL,
  `name` varchar(60) DEFAULT NULL,
  `email_id` varchar(128) DEFAULT NULL,
  `phone` text NOT NULL,
  `photo` varchar(128) DEFAULT NULL,
  `company_name` varchar(128) DEFAULT NULL,
  `coming_from` varchar(128) DEFAULT NULL,
  `to_meet` varchar(128) DEFAULT NULL,
  `representing` varchar(128) DEFAULT NULL,
  `to_meet_personID` int(11) NOT NULL,
  `to_meet_person_usertype` varchar(40) NOT NULL,
  `check_in` timestamp NULL DEFAULT NULL,
  `check_out` timestamp NULL DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `visitorinfo`
--

INSERT INTO `visitorinfo` (`visitorID`, `name`, `email_id`, `phone`, `photo`, `company_name`, `coming_from`, `to_meet`, `representing`, `to_meet_personID`, `to_meet_person_usertype`, `check_in`, `check_out`, `status`) VALUES
(1, 'Muradul Islam', 'muradul@gmail.com', '01718194496', 'visitor64121421939.jpeg', 'Bangladesh Coast guird', 'Dhaka', 'Rid Islam', 'family', 1, 'Student', '2015-12-01 20:02:56', NULL, 0),
(2, 'Rid Islam', 'i.ridislam@gmail.com', '123456', 'visitor58666707017.jpeg', 'inilabs', 'dhaka', 'rakib', 'friend', 2, 'Student', '2016-02-16 07:50:51', '2016-02-16 07:57:20', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alert`
--
ALTER TABLE `alert`
  ADD PRIMARY KEY (`alertID`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`classesID`);

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`expenseID`);

--
-- Indexes for table `feetype`
--
ALTER TABLE `feetype`
  ADD PRIMARY KEY (`feetypeID`);

--
-- Indexes for table `ini_config`
--
ALTER TABLE `ini_config`
  ADD PRIMARY KEY (`configID`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoiceID`);

--
-- Indexes for table `issue`
--
ALTER TABLE `issue`
  ADD PRIMARY KEY (`issueID`);

--
-- Indexes for table `mailandsms`
--
ALTER TABLE `mailandsms`
  ADD PRIMARY KEY (`mailandsmsID`);

--
-- Indexes for table `mailandsmstemplate`
--
ALTER TABLE `mailandsmstemplate`
  ADD PRIMARY KEY (`mailandsmstemplateID`);

--
-- Indexes for table `mailandsmstemplatetag`
--
ALTER TABLE `mailandsmstemplatetag`
  ADD PRIMARY KEY (`mailandsmstemplatetagID`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`mediaID`);

--
-- Indexes for table `media_category`
--
ALTER TABLE `media_category`
  ADD PRIMARY KEY (`mcategoryID`);

--
-- Indexes for table `media_share`
--
ALTER TABLE `media_share`
  ADD PRIMARY KEY (`shareID`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`messageID`);

--
-- Indexes for table `notice`
--
ALTER TABLE `notice`
  ADD PRIMARY KEY (`noticeID`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`parentID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentID`);

--
-- Indexes for table `promotionsubject`
--
ALTER TABLE `promotionsubject`
  ADD PRIMARY KEY (`promotionSubjectID`);

--
-- Indexes for table `reply_msg`
--
ALTER TABLE `reply_msg`
  ADD PRIMARY KEY (`replyID`);

--
-- Indexes for table `reset`
--
ALTER TABLE `reset`
  ADD PRIMARY KEY (`resetID`);

--
-- Indexes for table `school_sessions`
--
ALTER TABLE `school_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`sectionID`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`settingID`);

--
-- Indexes for table `smssettings`
--
ALTER TABLE `smssettings`
  ADD PRIMARY KEY (`smssettingsID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`studentID`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subjectID`);

--
-- Indexes for table `systemadmin`
--
ALTER TABLE `systemadmin`
  ADD PRIMARY KEY (`systemadminID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `visitorinfo`
--
ALTER TABLE `visitorinfo`
  ADD PRIMARY KEY (`visitorID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alert`
--
ALTER TABLE `alert`
  MODIFY `alertID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `classesID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `expenseID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `feetype`
--
ALTER TABLE `feetype`
  MODIFY `feetypeID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ini_config`
--
ALTER TABLE `ini_config`
  MODIFY `configID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoiceID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `issue`
--
ALTER TABLE `issue`
  MODIFY `issueID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mailandsms`
--
ALTER TABLE `mailandsms`
  MODIFY `mailandsmsID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mailandsmstemplate`
--
ALTER TABLE `mailandsmstemplate`
  MODIFY `mailandsmstemplateID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `mailandsmstemplatetag`
--
ALTER TABLE `mailandsmstemplatetag`
  MODIFY `mailandsmstemplatetagID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `mediaID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `media_category`
--
ALTER TABLE `media_category`
  MODIFY `mcategoryID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `media_share`
--
ALTER TABLE `media_share`
  MODIFY `shareID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `messageID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notice`
--
ALTER TABLE `notice`
  MODIFY `noticeID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `parentID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `promotionsubject`
--
ALTER TABLE `promotionsubject`
  MODIFY `promotionSubjectID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `reply_msg`
--
ALTER TABLE `reply_msg`
  MODIFY `replyID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `reset`
--
ALTER TABLE `reset`
  MODIFY `resetID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `sectionID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `settingID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `smssettings`
--
ALTER TABLE `smssettings`
  MODIFY `smssettingsID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `studentID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subjectID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `systemadmin`
--
ALTER TABLE `systemadmin`
  MODIFY `systemadminID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `visitorinfo`
--
ALTER TABLE `visitorinfo`
  MODIFY `visitorID` bigint(12) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
