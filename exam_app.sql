-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 06, 2026 at 02:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `exam_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`) VALUES
(1, 'Software Engineering class');

-- --------------------------------------------------------

--
-- Table structure for table `class_courses`
--

CREATE TABLE `class_courses` (
  `class_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_courses`
--

INSERT INTO `class_courses` (`class_id`, `course_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `description`, `fee`) VALUES
(1, 'Software engneering course', 'Software engneering course', 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 60,
  `tab_switch_limit` int(11) NOT NULL DEFAULT 1,
  `tab_switch_action` enum('cancel_immediately','warn_then_cancel') NOT NULL DEFAULT 'warn_then_cancel',
  `status` enum('draft','active','closed') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `course_id`, `teacher_id`, `title`, `duration_minutes`, `tab_switch_limit`, `tab_switch_action`, `status`, `created_at`) VALUES
(1, 1, 1, 'Html exam', 1, 3, 'warn_then_cancel', 'active', '2026-01-04 09:54:08');

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `submit_time` timestamp NULL DEFAULT NULL,
  `tab_switch_count` int(11) DEFAULT 0,
  `status` enum('in_progress','submitted','canceled') DEFAULT 'in_progress',
  `calculated_score` int(11) DEFAULT 0,
  `final_score` int(11) DEFAULT 0,
  `teacher_comment` text DEFAULT NULL,
  `is_released` tinyint(1) DEFAULT 0,
  `markup_deducted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_attempts`
--

INSERT INTO `exam_attempts` (`id`, `student_id`, `exam_id`, `start_time`, `submit_time`, `tab_switch_count`, `status`, `calculated_score`, `final_score`, `teacher_comment`, `is_released`, `markup_deducted`) VALUES
(15, 2, 1, '2026-01-06 10:20:17', '2026-01-06 10:21:28', 1, 'submitted', 0, 5, 'you absoluty good and have got goodmarks ', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `read_at`, `created_at`) VALUES
(1, 1, 2, 'hello student se me in office', 1, '2026-01-06 11:33:22', '2026-01-05 11:44:50'),
(2, 2, 1, 'Tab switching is strictly monitored. Switching tabs will trigger an automatic log and potential cancellation.', 1, '2026-01-06 11:32:57', '2026-01-05 11:47:24'),
(3, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:00:01'),
(4, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:00:02'),
(5, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:00:02'),
(6, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:00:02'),
(7, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:00:10'),
(8, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:00:16'),
(9, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:00:23'),
(10, 1, 2, 'good', 1, '2026-01-06 11:33:22', '2026-01-05 12:00:43'),
(11, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:07:54'),
(12, 1, 2, 'good waye', 1, '2026-01-06 11:33:22', '2026-01-05 12:08:29'),
(13, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:08:42'),
(14, 2, 1, 'muuse', 1, '2026-01-06 11:32:57', '2026-01-05 12:13:42'),
(15, 2, 1, 'ustaadi examka se waye', 1, '2026-01-06 11:32:57', '2026-01-05 12:16:32'),
(16, 1, 2, 'ustadi nwa dhacday na dhaf', 1, '2026-01-06 11:33:22', '2026-01-06 11:18:16'),
(17, 1, 2, 'hello', 1, '2026-01-06 11:33:22', '2026-01-06 11:33:04'),
(18, 2, 1, 'sethy', 1, '2026-01-06 11:33:52', '2026-01-06 11:33:45'),
(19, 1, 2, 'ustaadi examka se waye', 1, '2026-01-06 11:34:18', '2026-01-06 11:34:05'),
(20, 2, 1, 'Macalinkww', 1, '2026-01-06 12:42:10', '2026-01-06 12:41:53'),
(21, 1, 2, 'he cmlin', 1, '2026-01-06 12:42:24', '2026-01-06 12:42:19'),
(22, 1, 2, 'muse', 1, '2026-01-06 12:49:06', '2026-01-06 12:48:54'),
(23, 2, 1, 'Muse', 1, '2026-01-06 13:01:02', '2026-01-06 12:49:02'),
(24, 2, 1, 'Hi', 1, '2026-01-06 13:01:02', '2026-01-06 13:00:48'),
(25, 1, 2, 'hello', 1, '2026-01-06 13:02:22', '2026-01-06 13:01:12'),
(26, 2, 1, 'hello', 1, '2026-01-06 13:12:35', '2026-01-06 13:12:29'),
(27, 2, 1, 'muuse', 1, '2026-01-06 13:15:54', '2026-01-06 13:15:35'),
(28, 2, 1, 'Hello', 0, NULL, '2026-01-06 13:21:19');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-01-06-112502', 'App\\Database\\Migrations\\AddIsReadToMessages', 'default', 'App', 1767698738, 1);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('mcq','dropdown','checkbox','written') NOT NULL,
  `points` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `exam_id`, `question_text`, `question_type`, `points`) VALUES
(11, 1, 'what is html', 'written', 1),
(12, 1, 'what is arduinio', 'mcq', 2),
(13, 1, 'captiloo ', 'mcq', 1),
(14, 1, 'what is mlo', 'written', 1),
(15, 1, 'jamalo', 'written', 1);

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_options`
--

INSERT INTO `question_options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(1, 2, 'editor', 0),
(2, 2, 'ewrww', 0),
(3, 2, 'jkl', 0),
(4, 2, 'others', 0),
(5, 3, 'Option 1', 0),
(6, 3, 'Option 2', 0),
(7, 3, 'reptiop', 0),
(8, 7, 'editor', 0),
(9, 7, 'ewrww', 0),
(10, 7, 'jkl', 0),
(11, 7, 'others', 0),
(12, 8, 'Option 1', 0),
(13, 8, 'Option 2', 0),
(14, 8, 'reptiop', 0),
(15, 12, 'editor', 0),
(16, 12, 'ewrww', 0),
(17, 12, 'jkl', 0),
(18, 12, 'others', 0),
(19, 13, 'Option 1', 0),
(20, 13, 'Option 2', 0),
(21, 13, 'reptiop', 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `description`) VALUES
(1, 'maintenance_reason', 'Our team is currently updating the system. We will be back online shortly.', 'Shown to students during lockout'),
(2, 'tab_switch_limit', '2', 'Max times a student can leave the exam tab (1-3)'),
(3, 'tab_switch_warning', 'WARNING: Tab switching detected! Your exam will be CANCELED if you do this again.', 'Alert shown when a student switches tabs'),
(4, 'tab_switch_kick', 'EXAM CANCELED: You have exceeded the tab switch limit.', 'Message shown when exam is closed');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `written_answer` text DEFAULT NULL,
  `marks_awarded` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`id`, `attempt_id`, `question_id`, `written_answer`, `marks_awarded`) VALUES
(27, 15, 11, 'Html is …..\n', 1),
(28, 15, 12, NULL, 1),
(29, 15, 13, NULL, 1),
(30, 15, 14, 'Mlo', 1),
(31, 15, 11, 'Html is …..\r\n', 0),
(32, 15, 12, '18', 0),
(33, 15, 13, '21', 0),
(34, 15, 14, 'Mlo', 0),
(35, 15, 15, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_answer_options`
--

CREATE TABLE `student_answer_options` (
  `answer_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answer_options`
--

INSERT INTO `student_answer_options` (`answer_id`, `option_id`) VALUES
(28, 18),
(29, 21),
(32, 18),
(33, 21);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `phone`, `address`, `photo`, `created_at`, `last_seen`) VALUES
(1, 'teacher1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mohamed', 'teacher', NULL, NULL, NULL, '2026-01-04 11:01:01', '2026-01-06 14:17:29'),
(2, 'student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mohamed Ahmed', 'student', '617937851', 'hodan', NULL, '2026-01-04 11:01:01', '2026-01-06 14:37:32'),
(3, 'Mohamed ', '$2y$10$vxP5L.4wGC9qyB0LdkeWU.yYO98flMT.m1Mu81w78CR90MONkLCKm', 'Mohamed Yusuf Ahmed', 'student', '252617937851', 'hodan', NULL, '2026-01-04 09:24:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_classes`
--

CREATE TABLE `user_classes` (
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_classes`
--

INSERT INTO `user_classes` (`user_id`, `class_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_courses`
--
ALTER TABLE `class_courses`
  ADD PRIMARY KEY (`class_id`,`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_answer_options`
--
ALTER TABLE `student_answer_options`
  ADD PRIMARY KEY (`answer_id`,`option_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_classes`
--
ALTER TABLE `user_classes`
  ADD PRIMARY KEY (`user_id`,`class_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
