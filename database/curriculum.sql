-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-07-31 17:14:30
-- 服务器版本： 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `curriculum`
--

-- --------------------------------------------------------

--
-- 表的结构 `adminuser`
--

CREATE TABLE `adminuser` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `role` tinyint(1) NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `adminuser`
--

INSERT INTO `adminuser` (`id`, `username`, `nickname`, `role`, `auth_key`, `password_hash`, `password_reset_token`, `email`) VALUES
(1, '123456', '系主任', 1, '0', '$2y$13$aFeWPhJczbicqSYCXja7n.8xHc1BkjVvrWS8sY33FMnKghsLHQBHe', NULL, NULL),
(2, '654321', '辅导员7', 4, 'WlXNqyfRKPSQUjh8lRRS7UIcnyXUVCfm', '$2y$13$0Ev2Au7F2TsTC8EIogK0def5RWW2REogFeVaoNcJkRW3VXqVVgxjy', NULL, NULL),
(4, '111111', '辅导员1', 4, '3t2YCu-NwJ1YS2sF1X4y0ggcxW3Zw7yQ', '$2y$13$O7IQ4EW9vmzFT0e5Ebbi3eE9czPrJ92OLfeMgw3NsPczbkxxR5LKK', NULL, NULL),
(5, '222222', '实验中心', 3, 'xpLnQ-0-gzoSDsl5YT6-g9MOXFdwJv0W', '$2y$13$aIK3bvU9ktJiKFmlNpmNGeoN8uxrm2FOQP/KaWHSuJrOnifjiT2H6', NULL, NULL),
(6, '333333', '副院长', 2, '7-xJ1H0gOqqka29RzlR_zCYHwaM2YPsU', '$2y$13$xsSruaHG7E453.m5qkWLduAbXYsN8.geolnCcXhSqq/B0xBSbzxMO', NULL, NULL),
(7, '123457', '辅导员2', 4, 'FzAOZPForf_b_ZBKdbH7hh6MaPVxGIo3', '$2y$13$yWtu7zp4.ga.GYs5D41oh.Ra1y2piNHruYHYEMb6x0kOYZeiEDvJa', NULL, NULL),
(9, '1566156', '辅导员3', 4, 'A854pQZwLFX2KBb-zu2bQcvpBIQuql6T', '$2y$13$///SH2i9X0zwDQIu4tf2OeEPpVNeNYLBevFoUfYifpWfxptFJfLte', NULL, NULL),
(10, '1516654', '辅导员4', 4, 'eiCNh97cXydd-CLJgUg2JkOAQD17dQFf', '$2y$13$96jBqfmiZrIWlFNNLpGRG./3ZuvZb47guptAM54madkxZLVdTtEk.', NULL, NULL),
(11, '946516', '辅导员5', 4, '65IP1i4jJlVPqVfSU4oSYaZ0JqALHJnc', '$2y$13$PgyozATLEQ3Wq6Acq8tOcOsUpwUmf.HHOmx3bWaABrDU5bqwAewLW', NULL, NULL),
(12, '5916191', '辅导员8', 4, 'hsD5LJRd8YwGihg5FJRhb7D3btnrFOuv', '$2y$13$OoVnS1MCZSG97I2xDjnMJO0rS9gbSyH08h2Rvr7A2hZuJFDeXLOZu', NULL, NULL),
(13, '654195', '辅导员6', 4, 'pBUn8vKR4Ip4eRKBHVPjSMp-5e1EEM3q', '$2y$13$wdBsYVJ/dGT3LGvpuJVJbeD7yYcYVGGtUvNS12DYOIKdLd4Sh2.0u', NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `application`
--

CREATE TABLE `application` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `apply_at` int(11) NOT NULL,
  `apply_week` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `adjust_week` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `adjust_day` tinyint(1) NOT NULL,
  `adjust_sec` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `classroom_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `audit`
--

CREATE TABLE `audit` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `adminuser_id` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `remark` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `adminuser_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `classes`
--

INSERT INTO `classes` (`id`, `number`, `name`, `adminuser_id`) VALUES
(1, 0, '教师', 1),
(2, 942165, '智能B14-1', 4),
(4, 984512, '网络B14-1', 4),
(5, 515616, '计科B14-1', 4),
(6, 626615, '网络B14-2', 7),
(7, 641613, '计科B14-2', 9);

-- --------------------------------------------------------

--
-- 表的结构 `classroom`
--

CREATE TABLE `classroom` (
  `id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(1) NOT NULL,
  `amount` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `classroom`
--

INSERT INTO `classroom` (`id`, `number`, `name`, `type`, `amount`) VALUES
(2, 6320, '计算中心320', 1, 1),
(3, 3120, '三教120', 0, 1),
(4, 3108, '三教108', 0, 1),
(5, 3222, '三教222', 0, 1),
(6, 3104, '三教104', 0, 1),
(7, 3102, '三教102', 0, 1),
(8, 3106, '三教106', 0, 1),
(9, 3110, '三教110', 0, 1),
(10, 3112, '三教112', 0, 1),
(11, 3114, '三教114', 0, 1),
(12, 3116, '三教116', 0, 1),
(13, 3118, '三教118', 0, 1),
(14, 3220, '三教220', 0, 1),
(15, 3202, '三教202', 0, 1),
(16, 3204, '三教204', 0, 1),
(17, 3206, '三教206', 0, 1),
(18, 3208, '三教208', 0, 1),
(19, 3210, '三教210', 0, 1),
(20, 3302, '三教302', 0, 1),
(21, 3304, '三教304', 0, 1),
(22, 3306, '三教306', 0, 1),
(23, 3308, '三教308', 0, 1),
(24, 3310, '三教310', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `day` tinyint(1) NOT NULL,
  `sec` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `week` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `classroom_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `course`
--

INSERT INTO `course` (`id`, `number`, `name`, `user_id`, `day`, `sec`, `week`, `classroom_id`) VALUES
(1, 654651, '线性代数', 5, 5, '3,4,5,6,7', '3,4,5,6,7', 2),
(3, 166512, '随便', 6, 1, '1,2,3,4,5', '1,2,3,4,5,8', 2),
(4, 65416, '65162', 5, 3, '3,4,5', '2,3,4,5', 13),
(5, 984660, '课程1', 6, 5, '7,8,9,10', '7,8,9,10,11', 2),
(7, 65161, '课程5', 5, 1, '1,2,3,4', '1,2,3,4', 9),
(8, 1, '1', 5, 7, '2', '2', 2),
(9, 2, '2', 5, 7, '12', '15', 2),
(10, 3, '3', 6, 7, '9', '9', 6),
(11, 55555, '测试', 6, 7, '10', '10', 7),
(14, 5151, '星期五', 6, 5, '1,2,12', '3,16', 7);

-- --------------------------------------------------------

--
-- 表的结构 `course_relationship`
--

CREATE TABLE `course_relationship` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `course_relationship`
--

INSERT INTO `course_relationship` (`id`, `class_id`, `course_id`) VALUES
(3, 2, 1),
(4, 4, 1),
(5, 2, 3),
(6, 2, 4),
(25, 2, 10),
(26, 2, 11),
(27, 4, 11),
(28, 6, 11),
(33, 2, 14),
(34, 4, 14),
(35, 6, 14),
(36, 5, 14),
(37, 7, 14);

-- --------------------------------------------------------

--
-- 表的结构 `elective`
--

CREATE TABLE `elective` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `elective`
--

INSERT INTO `elective` (`id`, `user_id`, `course_id`) VALUES
(1, 3, 5),
(2, 3, 7),
(3, 3, 8),
(5, 4, 10),
(6, 4, 11);

-- --------------------------------------------------------

--
-- 表的结构 `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1499582027),
('m130524_201442_init', 1499582041);

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `class_id` int(11) NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `username`, `nickname`, `class_id`, `auth_key`, `password_hash`, `password_reset_token`) VALUES
(3, '1421101', '学生1', 2, 'KbPyTZdFXVciAHikNpCOoIsbZmghw7zD', '$2y$13$.W7sOg9NQcPVgUGCddakXODfD74qUiHY29ic00GoxeZAtu1ZoGpSO', NULL),
(4, '1421102', '学生2', 2, 'rqCZPEgXnyWnHZXr0xol8PnH_jXZWrNE', '$2y$13$7Zdl.tSuJ.1otv0lyNuV3OLs/sNVnaBpbLFMKQXSDeIh6JkKgOhEy', NULL),
(5, '1421121', '教师1', 1, 'x8PtuDl-EY4W00pnUjT8IHdgyjOqDhzK', '$2y$13$p8SzF3mDNguiptmOHxqcsuEtDM.nr9Ogniuy6aM5Ig7mL0XWNb1c6', NULL),
(6, '1421122', '教师2', 1, 'PpAaCeR-PLaAowzB3BjTaC5xQrxMF2pD', '$2y$13$1jzMKW/pSFZMz/Zi8Xd3Deh11pyJuCldBydU0w.ZHgxioRo.001fi', NULL),
(7, '1421103', '学生3', 2, 'G8LE8boxXDnvAktPrMSY1sXAETLtPiZO', '$2y$13$F.VcqfdP1zKvMObUzMyg6O6cPuMSPdCcewhrGC7RnOOxO0Y79C1rW', NULL),
(8, '1421104', '学生4', 2, 'jk692oOuS86a8gXGjD-9mNGBJ3TSst5q', '$2y$13$qmIDVbaxOg/knZ2G3GXUCelA4A9ttoMyv/M6xzG5AFPDvvCkKVUjm', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminuser`
--
ALTER TABLE `adminuser`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Indexes for table `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `classroom_id` (`classroom_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `audit`
--
ALTER TABLE `audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `adminuser_id` (`adminuser_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number` (`number`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `adminuser_id` (`adminuser_id`);

--
-- Indexes for table `classroom`
--
ALTER TABLE `classroom`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number` (`number`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classroom_id` (`classroom_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `course_relationship`
--
ALTER TABLE `course_relationship`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `elective`
--
ALTER TABLE `elective`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`),
  ADD KEY `class_id` (`class_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `adminuser`
--
ALTER TABLE `adminuser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- 使用表AUTO_INCREMENT `application`
--
ALTER TABLE `application`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `audit`
--
ALTER TABLE `audit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- 使用表AUTO_INCREMENT `classroom`
--
ALTER TABLE `classroom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- 使用表AUTO_INCREMENT `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- 使用表AUTO_INCREMENT `course_relationship`
--
ALTER TABLE `course_relationship`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- 使用表AUTO_INCREMENT `elective`
--
ALTER TABLE `elective`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- 限制导出的表
--

--
-- 限制表 `application`
--
ALTER TABLE `application`
  ADD CONSTRAINT `application_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `application_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `application_ibfk_3` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `application_ibfk_4` FOREIGN KEY (`teacher_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `audit`
--
ALTER TABLE `audit`
  ADD CONSTRAINT `audit_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `application` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `audit_ibfk_2` FOREIGN KEY (`adminuser_id`) REFERENCES `adminuser` (`id`) ON UPDATE CASCADE;

--
-- 限制表 `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`adminuser_id`) REFERENCES `adminuser` (`id`) ON UPDATE CASCADE;

--
-- 限制表 `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `course_ibfk_2` FOREIGN KEY (`classroom_id`) REFERENCES `classroom` (`id`) ON UPDATE CASCADE;

--
-- 限制表 `course_relationship`
--
ALTER TABLE `course_relationship`
  ADD CONSTRAINT `course_relationship_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_relationship_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `elective`
--
ALTER TABLE `elective`
  ADD CONSTRAINT `elective_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `elective_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
