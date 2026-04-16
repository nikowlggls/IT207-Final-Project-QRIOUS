-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql207.byethost8.com
-- Generation Time: Apr 16, 2026 at 08:13 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `b8_41189529_Online_Quiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` varchar(50) DEFAULT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_answer` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`) VALUES
(1, 2, '1 + 1', 'Multiple Choice', '2', 'two', 'dalawa', 'dos', 'A'),
(34, 49, 'totoo?', 'True or False', NULL, NULL, NULL, NULL, 'False'),
(33, 49, 'ano daw', 'Fill in the Blanks', '', '', '', '', ''),
(32, 48, 'f', 'Multiple Choice', '3', '4', 'r', 'e', 'e'),
(27, 42, 'e', 'Multiple Choice', 'e', 'w', 'f', 'e', 'b'),
(28, 43, 'w', 'Short Answer', NULL, NULL, NULL, NULL, 'True'),
(29, 43, 'w', 'Short Answer', NULL, NULL, NULL, NULL, 'e'),
(23, 41, 'w', 'Multiple Choice', 'w', 'w', 'w', 'w', 'a'),
(22, 39, 'q', 'Short Answer', NULL, NULL, NULL, NULL, 'q'),
(24, 41, 'w', 'Short Answer', NULL, NULL, NULL, NULL, 'True'),
(25, 41, 'w', 'Short Answer', NULL, NULL, NULL, NULL, 'e'),
(26, 41, 'w', 'Short Answer', NULL, NULL, NULL, NULL, 'w'),
(30, 43, 'e', 'Short Answer', NULL, NULL, NULL, NULL, 'e'),
(31, 47, 'w', 'Short Answer', '', '', '', '', ''),
(124, 122, '1 + 1 = _', 'Fill in the Blanks', '', '', '', '', '11'),
(36, 51, 'tama', 'Short Answer', '', '', '', '', ''),
(123, 122, 'ilang taon kana?', 'Fill in the Blanks', '', '', '', '', '23'),
(122, 122, 'umiinom kaba?', 'Fill in the Blanks', '', '', '', '', 'True'),
(41, 57, 'MAMA', 'Multiple Choice', 'AA', 'DD', 'CCDD', 'SS', 'A'),
(43, 59, 'k', 'Multiple Choice', 'j', 'e', 'e', 'd', 'B'),
(44, 59, 'k', 'Short Answer', '', '', '', '', 'True'),
(45, 60, 'w', 'Short Answer', '', '', '', '', 'w'),
(46, 67, 'w', 'Multiple Choice', 's', 'd', 'd', 's', 'A'),
(69, 94, 'asd', 'Multiple Choice', 'asd', 'asda', 'sdas', 'dasd', 'A'),
(49, 79, 'asds', 'Multiple Choice', 's', 'd', 's', 's', 'A'),
(50, 79, 's', 'Short Answer', '', '', '', '', 'True'),
(51, 79, 'd', 'Short Answer', '', '', '', '', 'a'),
(70, 94, 'asd', 'Short Answer', '', '', '', '', 'True'),
(72, 94, 'asd', 'Short Answer', '', '', '', '', 'asd'),
(71, 94, 'asd', 'Short Answer', '', '', '', '', 'asd'),
(117, 119, '3edr', 'Multiple Choice', '4rf', '4rf', '8ki', '45tf', 'C'),
(120, 121, 'asdmk', 'Multiple Choice', 'wefj', 'wnfr', 'wef', 'waed', 'A'),
(119, 120, 'anajaerf', 'Fill in the Blanks', '', '', '', '', 'yes'),
(121, 122, 'sino ka?', 'Multiple Choice', 'allena', 'fiona', 'allenaur', 'villarama', 'B'),
(83, 99, 'we', 'Multiple Choice', 'wef', 'wefwa', 'edwa', 'edaw', 'A'),
(84, 99, 'we', 'Short Answer', '', '', '', '', 'True'),
(85, 99, 'we', 'Short Answer', '', '', '', '', 'wef'),
(116, 118, 'e4rf', 'Short Answer', '', '', '', '', 'True'),
(115, 117, 'how are u', 'Multiple Choice', 'im fine', 'yes', 'ok lang', 'oo', 'A'),
(106, 114, 'rv', 'Short Answer', '', '', '', '', 'vr'),
(105, 114, 'rb', 'Short Answer', '', '', '', '', 'rtg'),
(104, 114, 'rb', 'Short Answer', '', '', '', '', 'True'),
(103, 114, 'rvv', 'Multiple Choice', 'rb', 'rgb', 'rgb', 'rgb', 'A'),
(102, 112, 'swd', 'Multiple Choice', 'QWD', 'QADW', 'QAWD', 'QAWD', 'A'),
(111, 116, 'asdswefw', 'Multiple Choice', 'aewd', 'aed', 'aed', 'aed', 'Aadqiwu'),
(109, 115, 'trvv', 'Short Answer', '', '', '', '', 'rtg'),
(110, 115, 'rtb_', 'Short Answer', '', '', '', '', 'rtg'),
(113, 116, 'wed', 'Short Answer', '', '', '', '', 'awed'),
(125, 124, 'hatdog kaba', 'Multiple Choice', 'oo', 'yuf', 'defo', 'pakyu', 'D'),
(126, 124, 'inom, ano tara?', 'Fill in the Blanks', '', '', '', '', 'True'),
(127, 124, 'sino ayaw mong prof', 'Fill in the Blanks', '', '', '', '', 'ads'),
(128, 124, 'i hate _', 'Fill in the Blanks', '', '', '', '', 'web'),
(129, 125, 'wae', 'Multiple Choice', 'wfe', 'wef', 'ef', 'aef', 'B'),
(130, 125, 'wrgwsrg', 'Fill in the Blanks', '', '', '', '', 'True'),
(131, 125, 'esfrvg', 'Fill in the Blanks', '', '', '', '', 'eryh'),
(132, 126, 'who r u', 'Multiple Choice', 'w', 'e', 'd', 'f', 'C'),
(133, 126, 's', 'Short Answer', '', '', '', '', 'False'),
(134, 126, 'sd', 'Short Answer', '', '', '', '', 'sd'),
(135, 126, 'sdsdffsrr', 'Fill in the Blanks', '', '', '', '', 'sd'),
(136, 126, 'who r u', 'Multiple Choice', 'w', 'e', 'd', 'f', 'C'),
(137, 126, 's', 'Short Answer', '', '', '', '', 'False'),
(138, 126, 'sd', 'Short Answer', '', '', '', '', 'sd'),
(139, 126, 'sdsdffsrr-', 'Fill in the Blanks', '', '', '', '', 'sd'),
(140, 127, 'wf', 'Multiple Choice', 'wsrf', 'wrg', 'eth4', 'th', 'C'),
(141, 128, 'erg', 'Multiple Choice', 'th', 'ergy', 'etyh', 'eth', 'B'),
(142, 128, 'erg', 'Multiple Choice', 'th', 'ergy', 'etyh', 'eth', 'B'),
(143, 128, 'rth', 'Short Answer', '', '', '', '', 'False'),
(144, 128, 'eg', 'Short Answer', '', '', '', '', 'tyj'),
(145, 128, 'wrgt_', 'Fill in the Blanks', '', '', '', '', 'ehe'),
(146, 129, 'wf', 'Multiple Choice', 'wf', 'wr', 'fw', 'rgw', 'D'),
(147, 129, 'erg', 'Multiple Choice', 'rg', 'rg', 'rg', 'rg', 'D'),
(148, 129, 'gr', 'Short Answer', '', '', '', '', 'False'),
(149, 129, 'gr', 'Short Answer', '', '', '', '', 'True'),
(150, 129, 'erg', 'Short Answer', '', '', '', '', 'r3g'),
(151, 129, 'erh', 'Short Answer', '', '', '', '', 'het'),
(152, 129, 'erger_', 'Fill in the Blanks', '', '', '', '', 'ergr]'),
(153, 129, 'ergerg_', 'Fill in the Blanks', '', '', '', '', 'g'),
(154, 130, 'dg', 'Multiple Choice', 'eg', 'etg', 'et', 'ged', 'A'),
(155, 130, 'wrg', 'Short Answer', '', '', '', '', 'True'),
(156, 130, 'wrg', 'Short Answer', '', '', '', '', 'wsg'),
(157, 130, 'rth_', 'Short Answer', '', '', '', '', 'eth'),
(169, 133, 'last ba', 'Multiple Choice', 'oo', 'hindi', 'maybe', 'no', 'A'),
(168, 132, 'tama daw', 'True/False', '', '', '', '', 'True'),
(184, 137, 'no', 'True or False', '', '', '', '', 'False'),
(185, 137, 'short', 'Short Answer', '', '', '', '', 'nye'),
(183, 137, 'ayaw', 'Multiple Choice', 'ko', 'na', 'yes', 'no', 'A'),
(182, 136, 'kakai_', 'Fill in the Blanks', '', '', '', '', 'babe'),
(180, 136, 'fuck this', 'True or False', '', '', '', '', 'True'),
(181, 136, 'ayaw ko naaa', 'Short Answer', '', '', '', '', 'oo'),
(179, 136, 'hi', 'Multiple Choice', 'hello', 'bro bro', 'konichiwa\'', 'meh', 'A'),
(178, 135, 'shift na, ano_?', 'Fill in the Blanks', '', '', '', '', 'tara'),
(177, 135, 'ayoko na', 'Short Answer', '', '', '', '', 'yes'),
(170, 133, 'last na ba talaga', 'True/False', '', '', '', '', 'True'),
(176, 135, 'mabait kaba', 'True or False', '', '', '', '', 'False'),
(174, 134, 'maganda ba q?', 'True or False', '', '', '', '', 'True'),
(175, 135, '1+1', 'Multiple Choice', '2', '3', '4', '5', 'A'),
(173, 134, 'edi', 'Multiple Choice', 'wow', 'nye', 'mama', 'mo', 'A'),
(171, 133, 'lipat na ba ako course', 'Identification', '', '', '', '', 'oo'),
(172, 133, 'le __ che', 'Fill Blanks', '', '', '', '', 'is'),
(186, 137, 'mama_', 'Fill in the Blanks', '', '', '', '', 'mo'),
(189, 136, 'rf', 'MC', 'erg', 'ergt', 'regt', '', 'er'),
(190, 139, 'ayko', 'Multiple Choice', 'wd', 'wf', 'wrf', 'werf', 'A'),
(191, 140, 'rf', 'Multiple Choice', 'erf', 'erf', 'ef', 'erf', 'A'),
(192, 141, 'meh', 'True/False', '', '', '', '', 'True'),
(193, 143, 'jm', 'Multiple Choice', 'dc', 'ec', 'ece', 'rcf', 'A'),
(253, 166, 'hi', 'Multiple Choice', 'hello', 'ni hao', 'annyeong', 'hoy', 'A'),
(252, 162, 'ert', 'Multiple Choice', 'e4yg', '4ty', 't45y4', '5ty', 'A'),
(250, 161, 'ytjyuyu', 'Short Answer', '', '', '', '', 'yu'),
(251, 161, 'yt', 'Fill in the Blanks', '', '', '', '', 'tjyutu'),
(249, 161, 'tgh', 'True or False', '', '', '', '', 'True'),
(246, 160, 'erg', 'Multiple Choice', 'erf', 'erf', 'erf', 'erf', 'A'),
(247, 160, 'erfe', 'True or False', '', '', '', '', 'True'),
(248, 161, 'erf', 'Multiple Choice', 'erf', 'ewf', 'ewrfe', 'rfer', 'A'),
(206, 147, 'feve', 'Multiple Choice', 'drfg', 'ergt', 'grg', 'rtg', 'D'),
(207, 148, 'drgb', 'Multiple Choice', 'rdtgh', 'rgth', 'rgh', 'rgth', 'D'),
(208, 148, 'rbh', 'True or False', '', '', '', '', 'False'),
(209, 148, 'rtby', 'Short Answer', '', '', '', '', 'rgtbhy'),
(210, 148, 'rthbr_', 'Fill in the Blanks', '', '', '', '', 'rbhr'),
(263, 168, '3+3=4', 'Short Answer', '', '', '', '', 'yes'),
(255, 166, '1+1=', 'Short Answer', '', '', '', '', '2'),
(254, 166, 'true or no', 'True or False', '', '', '', '', 'True'),
(264, 168, '4+4=_', 'Fill in the Blanks', '', '', '', '', '8'),
(256, 166, '2+2=_', 'Fill in the Blanks', '', '', '', '', '4'),
(257, 167, '3+3', 'Multiple Choice', '2', '3', '4', '6', 'D'),
(258, 167, '1+1=3', 'True or False', '', '', '', '', 'False'),
(259, 167, 'anoo', 'Short Answer', '', '', '', '', 'no'),
(260, 167, 'aeff', 'Fill in the Blanks', '', '', '', '', 'srfrsf'),
(261, 168, '1+1', 'Multiple Choice', '2', '3', '4', '5', 'A'),
(262, 168, '2+2=4?', 'True or False', '', '', '', '', 'True'),
(240, 158, 'erf', 'Multiple Choice', 'er', '4r5t', '4r5t', 'erf', 'A'),
(241, 158, 'ferf', 'True or False', '', '', '', '', 'True'),
(242, 158, 'erg', 'Short Answer', '', '', '', '', 'e4gr'),
(243, 158, 'ergt', 'Fill in the Blanks', '', '', '', '', 'e4trg');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `passing_score` int(11) DEFAULT 75,
  `time_limit` int(11) DEFAULT 30,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `mc_count` int(11) DEFAULT 0,
  `tf_count` int(11) DEFAULT 0,
  `sa_count` int(11) DEFAULT 0,
  `fb_count` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `quiz_title`, `description`, `category`, `passing_score`, `time_limit`, `created_by`, `created_at`, `mc_count`, `tf_count`, `sa_count`, `fb_count`) VALUES
(166, 'WEB', NULL, 'IT / CS', 75, 30, '1', '2026-04-13 12:11:19', 1, 1, 1, 1),
(136, 'hey shawty', NULL, 'IT / CS', 75, 30, '1', '2026-04-03 19:35:09', 1, 1, 1, 0),
(137, 'YAWQNA', NULL, 'IT / CS', 75, 30, '1', '2026-04-03 19:52:40', 1, 1, 1, 0),
(167, 'web2', NULL, 'IT / CS', 75, 30, '1', '2026-04-13 12:15:43', 1, 1, 1, 1),
(139, 'jusq', NULL, 'IT / CS', 75, 30, 'zai', '2026-04-03 22:13:32', 1, 0, 0, 0),
(140, 'erf', NULL, 'IT / CS', 75, 30, '1', '2026-04-03 22:33:43', 1, 0, 0, 0),
(141, 'bro', NULL, 'IT / CS', 75, 30, 'zai', '2026-04-03 22:36:13', 0, 1, 0, 0),
(143, 'n', NULL, 'IT / CS', 75, 30, 'zai', '2026-04-03 22:51:37', 1, 0, 0, 0),
(160, 'erfgf', NULL, 'IT / CS', 75, 30, '6', '2026-04-10 20:19:46', 1, 1, 0, 0),
(145, 'ef', NULL, 'IT / CS', 75, 30, '1', '2026-04-03 23:05:38', 1, 1, 1, 1),
(147, 'ewrf', NULL, 'IT / CS', 75, 30, '1', '2026-04-03 23:30:54', 1, 0, 0, 0),
(148, 'rtb', NULL, 'IT / CS', 75, 30, '1', '2026-04-03 23:31:18', 1, 1, 1, 1),
(164, 'wed', NULL, 'IT / CS', 75, 30, '1', '2026-04-12 18:44:35', 1, 1, 1, 1),
(161, 'serf', NULL, 'IT / CS', 75, 30, '6', '2026-04-10 20:21:18', 1, 1, 1, 1),
(162, 'wefr', NULL, 'IT / CS', 75, 30, '6', '2026-04-10 20:26:44', 1, 0, 0, 0),
(163, 'kaka', NULL, 'IT / CS', 75, 30, '1', '2026-04-12 18:44:17', 1, 1, 1, 1),
(158, 'ase', NULL, 'IT / CS', 75, 30, '1', '2026-04-10 20:12:18', 1, 1, 1, 1),
(165, 'ergt', NULL, 'IT / CS', 75, 30, '1', '2026-04-12 18:47:44', 1, 1, 1, 1),
(168, 'web sys', NULL, 'IT / CS', 75, 30, '1', '2026-04-13 13:41:14', 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_assignments`
--

CREATE TABLE `quiz_assignments` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `deadline` datetime NOT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `assigned_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `quiz_assignments`
--

INSERT INTO `quiz_assignments` (`id`, `quiz_id`, `section_name`, `deadline`, `assigned_by`, `status`, `assigned_at`) VALUES
(1, 49, 'BSIT 3H', '0000-00-00 00:00:00', NULL, 'Active', '2026-03-29 09:30:05'),
(2, 49, 'BSIT 3A', '0000-00-00 00:00:00', NULL, 'Active', '2026-03-29 09:32:02'),
(3, 49, 'jkasnajdh', '0000-00-00 00:00:00', NULL, 'Active', '2026-03-29 09:37:23'),
(4, 96, 'asd', '2026-03-31 20:38:00', 6, 'Active', '2026-03-29 12:38:38'),
(5, 109, 'asd', '2026-03-06 21:18:00', 6, 'Active', '2026-03-29 13:18:53'),
(6, 111, 'aqd', '2026-03-06 21:20:00', 6, 'Active', '2026-03-29 13:20:08'),
(7, 120, 'ugkuh', '2026-03-31 19:29:00', 6, 'Active', '2026-03-30 11:29:15'),
(8, 133, 'BSIT 2H', '2026-04-05 02:15:00', 6, 'Active', '2026-04-03 18:15:21'),
(9, 135, 'BSIT 2H', '2026-04-05 03:15:00', 1, 'active', '2026-04-03 19:15:43'),
(10, 136, 'BSIT 11', '2026-04-05 03:36:00', 1, 'active', '2026-04-03 19:37:20'),
(11, 136, 'bsit', '2026-04-05 03:50:00', 1, 'active', '2026-04-03 19:51:20'),
(12, 136, '11', '2026-04-05 03:50:00', 1, 'active', '2026-04-03 19:51:20'),
(13, 136, '2h', '2026-04-05 03:50:00', 1, 'active', '2026-04-03 19:51:20'),
(14, 137, 'bsit', '2026-04-07 03:53:00', 1, 'active', '2026-04-03 19:53:40'),
(15, 137, '11', '2026-04-07 03:53:00', 1, 'active', '2026-04-03 19:53:40'),
(16, 137, '2h', '2026-04-07 03:53:00', 1, 'active', '2026-04-03 19:53:40'),
(17, 137, 'ba', '2026-04-07 03:53:00', 1, 'active', '2026-04-03 19:53:40'),
(18, 138, 'BSIT 2H', '2026-04-05 04:07:00', 6, 'active', '2026-04-03 20:07:46'),
(19, 138, 'BSIT 11', '2026-04-05 05:54:00', 6, 'active', '2026-04-03 21:54:25'),
(20, 138, 'BSIT 3H', '2026-04-05 18:14:00', 6, 'active', '2026-04-03 22:14:11'),
(21, 137, 'BSIT 3H', '2026-04-05 18:26:00', 6, 'active', '2026-04-03 22:27:01'),
(22, 139, 'bsit', '2026-04-11 06:31:00', 1, 'active', '2026-04-03 22:31:28'),
(23, 139, '3h', '2026-04-11 06:31:00', 1, 'active', '2026-04-03 22:31:28'),
(24, 139, 'bsit3h', '2026-04-11 06:27:00', 1, 'active', '2026-04-03 22:27:53'),
(25, 140, 'BSIT 3H', '2026-04-11 06:35:00', 1, 'active', '2026-04-03 22:35:22'),
(26, 139, 'BSIT 3H', '2026-04-11 06:35:00', 1, 'active', '2026-04-03 22:35:33'),
(27, 144, 'BSIT 3H', '2026-04-05 06:53:00', 6, 'active', '2026-04-03 22:53:48'),
(28, 150, 'BSIT 2H', '2026-04-05 07:42:00', 6, 'active', '2026-04-03 23:42:21'),
(29, 151, 'BSIT 2H', '2026-04-05 18:00:00', 6, 'active', '2026-04-05 09:16:54'),
(30, 152, 'BSIT 2H', '2026-04-05 18:30:00', 6, 'active', '2026-04-05 09:55:49'),
(31, 153, 'BSIT 2H', '2026-04-05 22:30:00', 6, 'active', '2026-04-05 13:12:24'),
(32, 154, 'BSIT 2H', '2026-04-25 02:05:00', 6, 'active', '2026-04-10 18:06:00'),
(33, 155, 'BSIT 2H', '2026-04-18 02:33:00', 6, 'active', '2026-04-10 18:33:22'),
(34, 156, 'BSIT 2H', '2026-04-11 04:06:00', 6, 'active', '2026-04-10 20:04:46'),
(35, 157, 'BSIT 2H', '2026-04-11 05:10:00', 6, 'active', '2026-04-10 20:10:18'),
(36, 158, 'BSIT 2H', '2026-04-18 07:12:00', 1, 'active', '2026-04-10 20:12:49'),
(37, 160, 'BSIT 2H', '2026-04-16 04:20:00', 6, 'active', '2026-04-10 20:20:09'),
(38, 161, 'BSIT 2H', '2026-04-16 04:21:00', 6, 'active', '2026-04-10 20:21:39'),
(39, 162, 'BSIT 2H', '2026-04-11 04:50:00', 6, 'active', '2026-04-10 20:27:23'),
(40, 166, 'BSIT 2H', '2026-04-13 20:30:00', 1, 'active', '2026-04-13 12:13:33'),
(41, 167, 'BSIT 2H', '2026-04-13 21:00:00', 1, 'active', '2026-04-13 12:20:47'),
(42, 168, 'BSIT 2H', '2026-04-13 22:30:00', 1, 'active', '2026-04-13 13:42:39');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `attempt_date` timestamp NULL DEFAULT current_timestamp(),
  `score` int(11) DEFAULT 0,
  `total_questions` int(11) DEFAULT 0,
  `status` varchar(20) DEFAULT 'Failed',
  `completed_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `user_id`, `quiz_id`, `attempt_date`, `score`, `total_questions`, `status`, `completed_at`) VALUES
(1, 14, 118, '2026-04-03 13:27:39', 1, 1, 'Passed', '2026-04-03 13:27:39'),
(2, 14, 117, '2026-04-03 13:50:27', 1, 1, 'Passed', '2026-04-03 13:50:27'),
(3, 14, 131, '2026-04-03 14:24:04', 8, 10, 'Passed', '2026-04-03 14:24:04'),
(4, 14, 131, '2026-04-03 14:31:44', 6, 10, 'Passed', '2026-04-03 14:31:44'),
(5, 14, 131, '2026-04-03 14:31:45', 6, 10, 'Passed', '2026-04-03 14:31:45'),
(6, 13, 129, '2026-04-03 14:40:36', 0, 8, 'Failed', '2026-04-03 14:40:36'),
(7, 14, 132, '2026-04-03 14:48:00', 0, 1, 'Failed', '2026-04-03 14:48:00'),
(8, 14, 126, '2026-04-03 14:56:13', 0, 8, 'Failed', '2026-04-03 14:56:13'),
(9, 14, 133, '2026-04-03 15:00:47', 3, 4, 'Passed', '2026-04-03 15:00:47'),
(10, 13, 133, '2026-04-03 15:03:49', 3, 4, 'Passed', '2026-04-03 15:03:49'),
(11, 13, 132, '2026-04-03 15:07:57', 0, 1, 'Failed', '2026-04-03 15:07:57'),
(12, 13, 132, '2026-04-03 15:08:52', 0, 1, 'Failed', '2026-04-03 15:08:52'),
(13, 13, 128, '2026-04-03 15:10:34', 1, 5, 'Failed', '2026-04-03 15:10:34'),
(14, 13, 123, '2026-04-03 15:12:48', 0, 0, 'Failed', '2026-04-03 15:12:48'),
(15, 13, 118, '2026-04-03 15:13:02', 0, 1, 'Failed', '2026-04-03 15:13:02'),
(16, 13, 134, '2026-04-03 15:15:33', 2, 2, 'Passed', '2026-04-03 15:15:33'),
(17, 12, 133, '2026-04-03 15:27:45', 4, 4, 'Passed', '2026-04-03 15:27:45'),
(18, 13, 135, '2026-04-03 19:31:50', 4, 4, 'Passed', '2026-04-03 19:31:50'),
(19, 13, 138, '2026-04-03 20:17:33', 1, 2, 'Passed', '2026-04-03 20:17:33'),
(20, 17, 136, '2026-04-03 21:51:57', 4, 5, 'Passed', '2026-04-03 21:51:57'),
(21, 6, 138, '2026-04-03 21:57:47', 2, 2, 'Passed', '2026-04-03 21:57:47'),
(22, 17, 138, '2026-04-03 21:58:07', 2, 2, 'Passed', '2026-04-03 21:58:07'),
(23, 18, 138, '2026-04-03 22:16:08', 2, 2, 'Passed', '2026-04-03 22:16:08'),
(24, 13, 150, '2026-04-03 23:42:50', 4, 4, 'Passed', '2026-04-03 23:42:50'),
(25, 16, 136, '2026-04-03 23:50:19', 3, 5, 'Passed', '2026-04-03 23:50:19'),
(26, 13, 152, '2026-04-05 09:52:06', 4, 4, 'Passed', '2026-04-05 09:52:06'),
(27, 13, 152, '2026-04-05 09:52:07', 4, 4, 'Passed', '2026-04-05 09:52:07'),
(28, 14, 151, '2026-04-05 09:54:20', 4, 4, 'Passed', '2026-04-05 09:54:20'),
(29, 14, 152, '2026-04-05 09:56:21', 3, 4, 'Passed', '2026-04-05 09:56:21'),
(30, 15, 152, '2026-04-05 11:50:43', 3, 4, 'Passed', '2026-04-05 11:50:43'),
(31, 15, 151, '2026-04-05 12:00:56', 4, 4, 'Passed', '2026-04-05 12:00:56'),
(32, 19, 151, '2026-04-05 12:45:24', 2, 4, 'Passed', '2026-04-05 12:45:24'),
(33, 19, 152, '2026-04-05 13:08:58', 1, 4, 'Failed', '2026-04-05 13:08:58'),
(34, 19, 153, '2026-04-05 13:13:03', 2, 2, 'Passed', '2026-04-05 13:13:03'),
(35, 14, 153, '2026-04-05 13:14:37', 0, 2, 'Failed', '2026-04-05 13:14:37'),
(36, 13, 153, '2026-04-05 13:23:46', 0, 2, 'Failed', '2026-04-05 13:23:46'),
(37, 23, 154, '2026-04-10 18:06:49', 2, 4, 'Passed', '2026-04-10 18:06:49'),
(38, 23, 155, '2026-04-10 18:33:51', 1, 4, 'Failed', '2026-04-10 18:33:51'),
(39, 24, 155, '2026-04-10 19:53:15', 2, 4, 'Passed', '2026-04-10 19:53:15'),
(40, 24, 154, '2026-04-10 19:54:51', 3, 4, 'Passed', '2026-04-10 19:54:51'),
(41, 24, 156, '2026-04-10 20:09:12', 1, 1, 'Passed', '2026-04-10 20:09:12'),
(42, 24, 160, '2026-04-10 20:20:41', 2, 2, 'Passed', '2026-04-10 20:20:41'),
(43, 24, 161, '2026-04-10 20:22:05', 2, 4, 'Passed', '2026-04-10 20:22:05'),
(44, 24, 162, '2026-04-10 20:28:53', 1, 1, 'Passed', '2026-04-10 20:28:53'),
(45, 24, 167, '2026-04-13 12:28:38', 3, 4, 'Passed', '2026-04-13 12:28:38'),
(46, 24, 168, '2026-04-13 13:56:10', 4, 4, 'Passed', '2026-04-13 13:56:10');

-- --------------------------------------------------------

--
-- Table structure for table `student_submissions`
--

CREATE TABLE `student_submissions` (
  `submission_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `submitted_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `student_submissions`
--

INSERT INTO `student_submissions` (`submission_id`, `quiz_id`, `student_name`, `score`, `total_questions`, `submitted_at`) VALUES
(1, 121, 'hey', 1, 1, '2026-03-30 15:32:10'),
(2, 121, 'kat', 1, 1, '2026-03-30 15:38:49'),
(3, 121, 'kat', 1, 1, '2026-03-30 15:39:03'),
(4, 118, 'kat', 0, 1, '2026-03-30 15:39:35'),
(5, 117, 'kat', 0, 1, '2026-03-30 15:39:58'),
(6, 67, 'kat', 0, 1, '2026-03-30 15:40:43'),
(7, 121, 'kat', 0, 1, '2026-03-30 15:41:02'),
(8, 121, 'kat', 0, 1, '2026-03-30 15:41:04'),
(9, 118, 'kat', 0, 1, '2026-03-30 15:41:20'),
(10, 121, 'kat', 0, 1, '2026-03-30 15:42:48'),
(11, 121, 'kat', 0, 1, '2026-03-30 15:43:10'),
(12, 67, 'kat', 0, 1, '2026-03-30 15:43:34'),
(13, 122, 'kat', 0, 0, '2026-03-30 15:45:11'),
(14, 122, 'kat', 0, 4, '2026-03-30 15:51:44'),
(15, 122, 'kat', 0, 4, '2026-03-30 15:53:05'),
(16, 122, 'kat', 1, 1, '2026-03-30 15:57:11'),
(17, 122, 'kat', 1, 1, '2026-03-30 15:58:12'),
(18, 122, 'kat', 0, 0, '2026-03-30 16:01:01'),
(19, 122, 'kat', 1, 4, '2026-03-30 16:03:28'),
(20, 122, 'kat', 0, 3, '2026-03-30 16:05:01'),
(21, 122, 'kat', 1, 4, '2026-03-30 16:08:05'),
(22, 122, 'kat', 0, 3, '2026-03-30 16:14:48'),
(23, 122, 'kat', 0, 3, '2026-03-30 16:17:10'),
(24, 122, 'kat', 0, 3, '2026-03-30 16:18:25'),
(25, 122, 'kat', 2, 4, '2026-03-30 16:20:18'),
(26, 122, 'kat', 0, 3, '2026-03-30 16:22:49'),
(27, 124, 'kat', 3, 4, '2026-03-30 16:36:28'),
(28, 122, 'kat', 0, 3, '2026-03-30 16:40:05'),
(29, 124, 'kat', 0, 4, '2026-04-03 10:48:00'),
(30, 124, 'kat', 0, 4, '2026-04-03 10:48:32'),
(31, 126, 'kat', 1, 8, '2026-04-03 11:07:25'),
(32, 125, 'kat', 0, 3, '2026-04-03 11:07:47'),
(33, 122, 'kat', 0, 4, '2026-04-03 11:08:12'),
(34, 129, 'Keithrine Rose', 0, 8, '2026-04-03 11:58:52'),
(35, 130, 'kat', 0, 4, '2026-04-03 12:36:47'),
(36, 130, 'hey', 1, 4, '2026-04-03 13:06:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Instructor','Participant') NOT NULL,
  `section` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `birthday` date DEFAULT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `role`, `section`, `created_at`, `birthday`, `contact_no`, `bio`, `email`) VALUES
(1, 'Kate', 'admin1', 'admin', 'Admin', NULL, '2026-03-20 13:20:08', NULL, NULL, NULL, NULL),
(9, 'Megan', 'megan_admin', 'admin123', 'Admin', NULL, '2026-03-20 14:38:20', NULL, NULL, NULL, NULL),
(8, 'nikol', 'nics', 'nikol', 'Instructor', NULL, '2026-03-20 14:23:13', NULL, NULL, NULL, NULL),
(7, 'fiona', 'fiona', 'fiona', 'Instructor', NULL, '2026-03-20 14:23:00', NULL, NULL, NULL, NULL),
(10, 'Allena', 'allena_admin', 'admin123', 'Admin', NULL, '2026-03-20 14:38:20', NULL, NULL, NULL, NULL),
(11, 'Nichole', 'nichole_admin', 'admin123', 'Admin', NULL, '2026-03-20 14:38:20', NULL, NULL, NULL, NULL),
(12, 'rose soriano', 'rose', 'rose', 'Participant', NULL, '2026-03-22 10:19:15', NULL, NULL, NULL, NULL),
(13, 'kat', 'kat', 'kat', 'Participant', 'BSIT 2H', '2026-03-27 06:59:52', NULL, NULL, NULL, NULL),
(14, 'hey', 'heyy', 'hey', 'Participant', 'BSIT 2H', '2026-03-30 15:11:48', NULL, NULL, NULL, NULL),
(15, 'nichole', 'nicholeski', '12345', 'Participant', 'BSIT 2H', '2026-04-01 02:34:27', NULL, NULL, NULL, NULL),
(16, 'bakla', 'bakla', 'bakla', 'Participant', 'bsit 11', '2026-04-03 19:38:54', NULL, NULL, NULL, NULL),
(17, 'zai', 'zai', 'zai', 'Participant', 'bsit 11', '2026-04-03 19:54:20', NULL, NULL, NULL, NULL),
(18, 'kei', 'kei', 'kei', 'Participant', 'BSIT 3H', '2026-04-03 22:14:29', NULL, NULL, NULL, NULL),
(19, 'Anne Logs', 'Ann3', '123', 'Participant', 'BSIT 2H', '2026-04-05 12:43:51', NULL, NULL, NULL, NULL),
(20, 'keirose', 'keirose', 'keirose', 'Participant', NULL, '2026-04-10 17:50:34', NULL, NULL, NULL, NULL),
(21, 'erf', 'erf', 'erf', 'Participant', 'BSIT11', '2026-04-10 17:54:09', NULL, NULL, NULL, NULL),
(22, 'luffy', 'luffy', 'luffy', 'Participant', 'BSIT2H', '2026-04-10 18:03:10', NULL, NULL, NULL, NULL),
(23, 'zoro', 'zoro', 'zoro', 'Participant', 'BSIT 2H', '2026-04-10 18:04:41', NULL, NULL, NULL, NULL),
(24, 'nami', 'nami', 'nami', 'Participant', 'BSIT 2H', '2026-04-10 19:07:56', '0000-00-00', '', 'erf', NULL),
(25, 'ussop', 'ussop', 'ussop', 'Participant', 'BSIT 211', '2026-04-10 19:14:36', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_assignments`
--
ALTER TABLE `quiz_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_submissions`
--
ALTER TABLE `student_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=265;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `quiz_assignments`
--
ALTER TABLE `quiz_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `student_submissions`
--
ALTER TABLE `student_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
