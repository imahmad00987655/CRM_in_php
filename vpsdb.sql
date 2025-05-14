-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2018 at 12:52 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vpsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `city_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `city_name`) VALUES
(1, 'Islamabad'),
(2, 'Lahore'),
(3, 'Wazirabad');

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- countries
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`) VALUES
(1, 'Canada'),
(2, 'Australia'),
(3, 'Hong Kong'),
(4, 'Ireland'),
(5, 'Japan'),
(6, 'Morocco'),
(7, 'New Zealand'),
(8, 'Schengen'),
(9, 'South Africa'),
(10, 'South Korea'),
(11, 'Turkey'),
(12, 'UK'),
(13, 'USA');

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--   documents

-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `document` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `parent_id`, `document`) VALUES
(1, 1, 'Passport'),
(2, 1, 'CNIC (BOTH SIDES)'),
(3, 1, 'FRC (FAMILY REGISTRATION CARD)'),
(4, 1, 'Parent’s date of birth'),
(5, 1, 'MRC (MARRIAGE CERTIFICATE)'),
(6, 1, 'NTN (NATIONAL TAX NUMBER)'),
(7, 1, 'Tax returns 1 year'),
(8, 1, 'Property documents'),
(9, 1, 'Bank Statement 4 months'),
(10, 1, 'Account maintenance letter'),
(11, 1, 'Credit card statement'),
(12, 1, 'Vehicle registration'),
(13, 1, 'Email id'),
(14, 1, 'Contact Number'),
(15, 1, '10 Years Work History'),
(16, 1, 'Educational History'),
(17, 1, '5 Years Travel History'),
(18, 1, 'Refusal. Country Name and Year'),
(19, 2, 'Passport (scan visa and stamp pages)'),
(20, 2, 'CNIC (Both Sides)'),
(21, 2, 'FRC (Family Registration card)'),
(22, 2, 'Parent’s date of birth'),
(23, 2, 'MRC (Marriage Certificate)'),
(24, 2, 'NTN (National Tax Number)'),
(25, 2, 'Tax returns 1 year'),
(26, 2, 'Property documents'),
(27, 2, 'Bank Statement 4 months'),
(28, 2, 'Account Maintenance letter'),
(29, 2, 'Credit card statement'),
(30, 2, 'Vehicle registration'),
(31, 2, 'Email ID'),
(32, 2, 'Contact Number'),
(33, 2, '10 Years Work History'),
(34, 2, 'Educational History'),
(35, 2, '5 Years Travel History'),
(36, 2, 'Refusal Country Name and Year'),
(37, 2, '(Chamber Membership) for Business Man'),
(38, 2, '(LetterHead) For Business Man'),
(39, 2, '(NOC and Salary Slips) for Job Person'),
(40, 2, 'Digital photo with White background'),
(41, 2, 'If you stay more than 3 months any visiting country'),
(42, 2, 'Polio Certificate'),
(43, 3, 'Passport (scan visa and stamp pages)'),
(44, 3, 'CNIC (Both Sides)'),
(45, 3, 'FRC (Family Registration card)'),
(46, 3, 'Digital photo with White background Picture'),
(47, 3, 'MRC (Marriage Certificate)'),
(48, 3, 'NTN (National Tax Number)'),
(49, 3, 'Tax returns 2 year'),
(50, 3, 'Property documents'),
(51, 3, 'Bank Statement 4 months'),
(52, 3, 'Account Maintenance letter'),
(53, 3, 'Credit card statement'),
(54, 3, 'Vehicle registration'),
(55, 3, 'Email ID'),
(56, 3, 'Contact Number'),
(57, 3, '(Chamber Membership) for Business Man'),
(58, 3, '(LetterHead) For Business Man'),
(59, 3, '(NOC Letter and 6 Salary Slips) for Job Person'),
(60, 4, 'Passport (scan visa and stamp pages)'),
(61, 4, 'CNIC (Both Sides)'),
(62, 4, 'FRC (Family Registration card)'),
(63, 4, 'White background picture (size 35x45)'),
(64, 4, 'Refusal Country Name and Date'),
(65, 4, 'NTN (National Tax Number)'),
(66, 4, 'Tax returns 2 year'),
(67, 4, 'Property documents'),
(68, 4, 'Bank Statement 6 months'),
(69, 4, 'Account Maintenance letter'),
(70, 4, 'Credit card statement'),
(71, 4, 'Vehicle registration'),
(72, 4, 'Email ID'),
(73, 4, 'Contact Number'),
(74, 4, '(Chamber Membership) for Business Man'),
(75, 4, '(LetterHead) For Business Man'),
(76, 4, '(NOC Letter and 6 Salary Slips) for Job Person'),
(77, 5, 'Passport (First and Second page)'),
(78, 5, 'CNIC (Both Sides)'),
(79, 5, 'Updated FRC not older than 3 months'),
(80, 5, 'MRC (Marriage Certificate)'),
(81, 5, 'NTN (National Tax Number)'),
(82, 5, 'Tax returns 1 year'),
(83, 5, 'Property documents'),
(84, 5, 'Bank Statement 3 months'),
(85, 5, 'Account Maintenance letter'),
(86, 5, 'Credit card statement'),
(87, 5, 'Vehicle registration'),
(88, 5, 'Email ID'),
(89, 5, 'Contact Number'),
(90, 5, '(Chamber Membership) for Business Man'),
(91, 5, '(LetterHead) For Business Man'),
(92, 5, '(NOC Letter and 6 Salary Slips) for Job Person'),
(93, 6, 'Passport (scan visa and stamp pages)'),
(94, 6, 'CNIC (Both Sides)'),
(95, 6, 'FRC (Family Registration card)'),
(96, 6, 'Birth Certificate'),
(97, 6, 'MRC (Marriage Certificate)'),
(98, 6, 'NTN (National Tax Number)'),
(99, 6, 'Tax returns 2 year'),
(100, 6, 'Property documents'),
(101, 6, 'Bank Statement 6 months'),
(102, 6, 'Account Maintenance letter'),
(103, 6, 'Credit card statement'),
(104, 6, 'Vehicle registration'),
(105, 6, 'Email ID'),
(106, 6, 'Contact Number'),
(107, 6, 'Blue background picture (size 35x45)'),
(108, 6, '(Chamber Membership) for Business Man'),
(109, 6, '(LetterHead) For Business Man'),
(110, 6, '(NOC Letter and 6 Salary Slips) for Job Person'),
(111, 7, 'Passport (scan visa and stamp pages)'),
(112, 7, 'CNIC (Both Sides)'),
(113, 7, 'Two FRC (one with wife and childs) and (second with parents)'),
(114, 7, 'Parent’s date of birth'),
(115, 7, 'MRC (Marriage Certificate)'),
(116, 7, 'NTN (National Tax Number)'),
(117, 7, 'Tax returns 1 year'),
(118, 7, 'Property documents'),
(119, 7, 'Bank Statement 6 months'),
(120, 7, 'Account Maintenance letter'),
(121, 7, 'Credit card statement'),
(122, 7, 'Vehicle registration'),
(123, 7, 'Email ID'),
(124, 7, 'Contact Number'),
(125, 7, '10 Years Work History'),
(126, 7, 'Educational History'),
(127, 7, '5 Years Travel History'),
(128, 7, 'Refusal Country Name and Year'),
(129, 7, '(Chamber Membership) for Business Man'),
(130, 7, '(LetterHead) For Business Man'),
(131, 7, '(NOC and Salary Slips) for Job Person'),
(132, 7, 'Digital Photo with White background'),
(133, 8, 'Passport (scan visa and stamp pages)'),
(134, 8, 'CNIC (Both Sides)'),
(135, 8, 'FRC (Family Registration card)'),
(136, 8, 'In last three years (refusal in Schengen)'),
(137, 8, 'MRC (Marriage Certificate)'),
(138, 8, 'NTN (National Tax Number)'),
(139, 8, 'Tax returns 2 year'),
(140, 8, 'Property documents'),
(141, 8, 'Bank Statement 6 months'),
(142, 8, 'Account Maintenance letter'),
(143, 8, 'Credit card statement'),
(144, 8, 'Vehicle registration'),
(145, 8, 'Email ID'),
(146, 8, 'Contact Number'),
(147, 8, '(Chamber Membership) for Business Man'),
(148, 8, '(LetterHead) For Business Man'),
(149, 8, '(NOC Letter and 6 Salary Slips) for Job Person'),
(150, 8, 'White background picture (size 35x45)'),
(151, 9, 'Passport (scan visa and stamp pages)'),
(152, 9, 'CNIC (Both Sides)'),
(153, 9, 'FRC (Family Registration card)'),
(154, 9, '2 pics 35x45 with White background'),
(155, 9, 'MRC (Marriage Certificate)'),
(156, 9, 'NTN (National Tax Number)'),
(157, 9, 'Tax returns 2 year'),
(158, 9, 'Property documents'),
(159, 9, 'Bank Statement 6 months'),
(160, 9, 'Account Maintenance letter'),
(161, 9, 'Credit card statement'),
(162, 9, 'Vehicle registration'),
(163, 9, 'Email ID'),
(164, 9, 'Contact Number'),
(165, 9, '10 Years Work History'),
(166, 9, '(Chamber Membership) for Business Man'),
(167, 9, '(LetterHead) For Business Man'),
(168, 9, '(NOC Letter and 6 Salary Slips) for Job Person'),
(169, 10, 'Passport (scan visa and stamp pages)'),
(170, 10, 'CNIC (Both Sides)'),
(171, 10, 'FRC (Family Registration card)'),
(172, 10, '2 pics 35x45 Mat Finish with White background'),
(173, 10, 'MRC (Marriage Certificate)'),
(174, 10, 'NTN (National Tax Number)'),
(175, 10, 'Tax returns 2 year'),
(176, 10, 'Property documents'),
(177, 10, 'Bank Statement 6 months'),
(178, 10, 'Account Maintenance letter'),
(179, 10, 'Credit card statement'),
(180, 10, 'Vehicle registration'),
(181, 10, 'Email ID'),
(182, 10, 'Contact Number'),
(183, 10, 'Educational History'),
(184, 10, '5 Years Travel History'),
(185, 10, '(Chamber Membership) for Business Man'),
(186, 10, '(LetterHead) For Business Man'),
(187, 10, '(NOC Letter and 6 Salary Slips) for Job Person'),
(188, 11, 'Passport (scan visa and stamp pages)'),
(189, 11, 'CNIC (Both Sides)'),
(190, 11, 'FRC (Family Registration card)'),
(191, 11, 'Email ID'),
(192, 11, 'Contact Number'),
(193, 11, 'NTN (National Tax Number)'),
(194, 11, 'Tax returns 2 year'),
(195, 11, 'Property documents'),
(196, 11, 'Bank Statement 3 months'),
(197, 11, 'Account Maintenance letter'),
(198, 11, 'Credit card statement'),
(199, 11, 'Vehicle registration'),
(200, 11, '(Chamber Membership) for Business Man'),
(201, 11, 'White background Picture (size 50x60)'),
(202, 11, '(LetterHead) For Business Man'),
(203, 11, '(NOC Letter and 3 Salary Slips) for Job Person'),
(204, 12, 'Passport (scan visa and stamp pages)'),
(205, 12, 'CNIC (Both Sides)'),
(206, 12, 'FRC (Family Registration card)'),
(207, 12, 'Parent’s date of birth'),
(208, 12, 'MRC (Marriage Certificate)'),
(209, 12, 'NTN (National Tax Number)'),
(210, 12, 'Tax returns 1 year'),
(211, 12, 'Property documents'),
(212, 12, 'Bank Statement 6 months'),
(213, 12, 'Account Maintenance letter'),
(214, 12, 'Vehicle registration'),
(215, 12, 'Email ID'),
(216, 12, 'Contact Number'),
(217, 12, '10 Years Travel History'),
(218, 12, 'Refusal Country Name and Date'),
(219, 12, '(Chamber Membership) for Business Man'),
(220, 12, '(LetterHead) For Business Man'),
(221, 12, '(NOC Letter and 6 Salary Slips) for Job Person'),
(222, 13, 'Passport (scan visa and stamp pages)'),
(223, 13, 'CNIC (Both Sides)'),
(224, 13, 'FRC (Family Registration card)'),
(225, 13, 'Parent’s date of birth'),
(226, 13, 'All Social Media Links (like Facebook, Instagram, etc.)'),
(227, 13, 'NTN (National Tax Number)'),
(228, 13, 'Email ID’s used in last 5 years'),
(229, 13, 'Phone numbers used in last 5 years'),
(230, 13, 'Digital Photo White background (HD) size (2x2)'),
(231, 13, 'Wife’s place of birth (Only for Married Persons)'),
(232, 13, 'USA Refusal - yes/no'),
(233, 13, 'All Contact Number used in the last 5 years'),
(234, 13, 'Work History'),
(235, 13, 'Educational History'),
(236, 13, '5 Years Travel History');

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--   login_attempts
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attempts` int(11) NOT NULL,
  `last_attempt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--   notices
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `notice_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `notices` MODIFY `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--   users
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_role` text COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `reg_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` text COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` enum('Male', 'Female', 'Other') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` text COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_number` text COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cnic` text COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--   user role
-- Table structure for table `user_role`
--
-- Insert default user
INSERT INTO `users` (username, email, user_role, password)
VALUES (
  'admin',
  'admin@admin.com',
  '1',
  '$2y$10$ZR0DVvaJavvwXCHRCz6OXuvwsIqorvocJhm/nHXIG3eMVYsA5Nkpe' -- Not bcrypt, but a hash function in MySQL
);


--
-- Table structure for table `user_role`
-- Create user_role table
--
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_roles` (`id`, `role_name`) VALUES
(1, 'admin'),
(2, 'visa_agent'),
(3, 'data_entry_agent');
--
-- Indexes for table `user_role`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--   visa applications
-- Table structure for the new table
--

CREATE TABLE `visa_applications` (
  `id` int(11) NOT NULL,
  `application_country_id` int NOT NULL,
  `application_city` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `persons` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `visa_agent` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `data_entry_agent` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `applicant_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `total_amount` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `advance_amount` int DEFAULT NULL,
  `applicant_cnic` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `application_limit` int DEFAULT NULL,
  `passport_number` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `proceed_to_agent` tinyint(1) NOT NULL DEFAULT 0,
  `applicant_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `person_1` json DEFAULT NULL,
  `person_2` json DEFAULT NULL,
  `person_3` json DEFAULT NULL,
  `person_4` json DEFAULT NULL,
  `extra_info` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `traveling_plan` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for the new table
--
ALTER TABLE `visa_applications`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for the new table
--
ALTER TABLE `visa_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--   visa_charges
-- Table structure for table `visa_charges`
--

CREATE TABLE `visa_charges` (
  `id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `single_person` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `couple` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `family_3` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `family_4` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for table `visa_charges`
--
ALTER TABLE `visa_charges`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `visa_charges`
--
ALTER TABLE `visa_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;