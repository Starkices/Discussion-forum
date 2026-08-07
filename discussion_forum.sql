-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 07, 2026 at 05:56 PM
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
-- Database: `discussion_forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `last_login`, `created_at`) VALUES
(1, 'dave', '$2y$10$GUNYpk8nW53Q04OYjjv2/eGJF2km8rnKXfLhQZV4Ryh7Fu1EE594W', '2025-11-03 12:12:38', '2025-06-23 16:30:22'),
(4, 'NELLY', '$2y$12$kah4HTSQnlMpPB.Wj36G3.4eOcjB9wXyEK35iqE8c83Qfvfs0AIAu', '2026-08-07 16:47:00', '2026-08-07 16:34:02');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `comment`, `parent_id`, `created_at`) VALUES
(1, 4, 'dave', 'hgc', NULL, '2025-07-23 20:39:48'),
(2, 4, 'dave', 'dgvhdv', 1, '2025-07-24 12:49:43'),
(3, 4, 'dave', 'hello', NULL, '2025-07-24 12:50:23'),
(6, 4, 'dave', 'nb ', 1, '2025-07-24 13:04:34'),
(7, 5, 'dave', 'eah', NULL, '2025-07-24 21:46:17'),
(8, 5, 'dave', 'ss', NULL, '2025-07-24 21:46:55'),
(9, 5, 'dave', 'ss', 7, '2025-07-24 21:47:05'),
(10, 5, 'dave', ' h  hc', NULL, '2025-08-10 18:38:19'),
(11, 4, 'dave', 'hvubh', NULL, '2025-08-10 18:38:28'),
(12, 4, 'dave', 'gvhhb', NULL, '2025-08-10 18:42:28'),
(13, 4, 'dave', 'ugvugb', NULL, '2025-08-10 18:43:34'),
(14, 6, 'dave', 'hbhjb', NULL, '2025-08-10 18:44:16'),
(15, 8, 'dave', 'sss', NULL, '2025-09-23 12:24:17'),
(16, 8, 'dave', 'sss', 15, '2025-09-23 12:24:29'),
(17, 4, 'dave', 'damn', NULL, '2025-09-29 12:20:17'),
(18, 8, 'johndoe', 'gfutgfy', NULL, '2026-08-07 12:51:45'),
(19, 8, 'johndoe', 'hvhvg', 18, '2026-08-07 12:52:00'),
(20, 8, 'johndoe', 'jkbj', NULL, '2026-08-07 14:05:40');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `group_description` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `max_members` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_name`, `group_description`, `created_by`, `created_at`, `password`, `max_members`) VALUES
(2, 'Technical talk ', 'solved problems here ', 18, '2025-07-25 08:49:55', NULL, 7),
(4, 'Study Group', 'Collaborate on assignments.', 18, '2025-07-25 09:15:20', NULL, NULL),
(5, 'phplesson8', 'phplesson8. learn php', 18, '2025-07-26 11:44:32', '$2y$10$eqAqZfMqmFyFgY2IFVxKYexOXhPjGrMxjxluDfezz4/dox2IhPx/O', 19),
(6, 'dave', 'personal', 18, '2025-07-26 12:28:26', '$2y$10$mjyw7aVNrgc7jBgm13DwB.F8VulO6uBy0F5yrpkDvEmeerBKnWlKW', 333),
(7, 'davew', 'dcghvjhbu', 18, '2025-10-13 21:14:03', '$2y$10$ostaHGLO/fD.RXO6xqptEOTXLMLLCylkonEXHUlHnr.8.kOrYVMgy', NULL),
(9, 'daveff', 'djjj', 18, '2025-10-31 12:36:39', '$2y$10$boASwaHFRIASNR7N89WZU.nY6zWB5ANVu0UPT89q3wwxbKzhH99f2', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `group_invites`
--

CREATE TABLE `group_invites` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `invite_code` varchar(64) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` datetime DEFAULT current_timestamp(),
  `role` enum('member','admin') DEFAULT 'member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `user_id`, `joined_at`, `role`) VALUES
(1, 2, 18, '2025-07-25 09:14:12', 'member'),
(8, 2, 20, '2025-07-26 09:14:56', 'admin'),
(9, 5, 18, '2025-07-26 11:50:26', 'admin'),
(10, 4, 20, '2025-07-27 21:20:29', 'member'),
(11, 7, 18, '2025-10-13 21:50:12', 'member');

-- --------------------------------------------------------

--
-- Table structure for table `group_messages`
--

CREATE TABLE `group_messages` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp(),
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_messages`
--

INSERT INTO `group_messages` (`id`, `group_id`, `user_id`, `message`, `sent_at`, `attachment`) VALUES
(1, 2, 18, 'cccc', '2025-07-25 13:13:48', NULL),
(2, 2, 18, 'dgasca', '2025-07-26 08:54:55', NULL),
(3, 2, 20, 'hello', '2025-07-26 09:15:09', NULL),
(4, 2, 18, 'bu', '2025-07-26 09:19:30', NULL),
(5, 2, 18, 'vg', '2025-07-26 09:19:36', NULL),
(6, 2, 20, 'fff', '2025-07-26 09:20:03', NULL),
(7, 2, 18, 'dd', '2025-07-26 10:33:01', NULL),
(8, 2, 18, 'ghgv', '2025-07-26 10:33:49', 'uploads/6884a0fd5342e.png'),
(10, 4, 20, 'xdx', '2025-07-26 11:05:55', NULL),
(12, 5, 18, 'hello everyone', '2025-07-26 11:51:20', NULL),
(13, 5, 18, ':⚫', '2025-07-26 11:58:51', NULL),
(14, 5, 18, '👌', '2025-07-26 11:59:04', NULL),
(15, 5, 18, 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eligendi distinctio officia eveniet iste earum? Provident vero laborum eligendi accusamus quia? Temporibus quaerat labore nemo provident deserunt nihil, repellendus rem porro obcaecati libero a tempore facilis sunt dicta doloremque eius placeat recusandae pariatur cupiditate velit itaque nulla? Dolor maiores enim quas?', '2025-07-27 20:16:47', NULL),
(16, 2, 20, 'dfcvgbjnk i ukb oi  uv iu  vvg   vj', '2025-07-27 22:07:34', 'uploads/68869516090fc.jpg'),
(17, 2, 18, 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Numquam aliquam quod deleniti. Neque natus hic assumenda cupiditate, consequuntur minus, laudantium ducimus, aliquam quis necessitatibus nulla eius a delectus? Illum, libero harum placeat beatae sequi quibusdam alias dicta possimus neque nobis aperiam molestiae ducimus consequuntur eos qui amet quis. Alias, laborum.', '2025-07-28 12:00:06', NULL),
(18, 2, 18, '', '2025-08-24 20:39:06', 'uploads/68ab6a5acd5d3.png'),
(19, 2, 18, 'dxfgcgfcg', '2025-09-23 12:23:39', NULL),
(20, 2, 18, 'hello', '2025-10-13 10:45:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `group_message_attachments`
--

CREATE TABLE `group_message_attachments` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_message_reactions`
--

CREATE TABLE `group_message_reactions` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reaction` varchar(32) NOT NULL,
  `reacted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_settings`
--

CREATE TABLE `group_settings` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `category`, `image`, `created_at`, `updated_at`) VALUES
(4, 18, 'vghj', 'gkvhjlbnj', 'General Discussion', 'uploads/20241014_135305.jpg', '2025-07-23 20:33:42', NULL),
(5, 18, 'code', 'what is this', 'Programming Support', '', '2025-07-24 19:42:43', NULL),
(6, 20, 'ha', 'Created', 'General Discussion', '', '2025-07-27 21:58:59', NULL),
(7, 18, 'hjjh', ' gkhbblj', 'Programming Support', '', '2025-08-10 18:57:42', NULL),
(8, 18, 'ddd', 'sxbjkbxkjwqbjxwqkbxhqwx', 'Assignment updates', '', '2025-08-24 20:35:32', NULL),
(9, 20, 'Laravel is the best PHP Framework', 'Laravel is widely considered the best PHP framework because it offers an unmatched \"batteries-included\" ecosystem, elegant syntax, and robust built-in tools that dramatically accelerate web development. By handling repetitive tasks out of the box, it allows developers to focus on building features rather than configuring boilerplate code.\r\n\r\nKey Technical Features:\r\n\r\n* Eloquent ORM: Interacts with databases using fluent PHP syntax instead of complex SQL queries.\r\n* Artisan CLI: Automates repetitive tasks, generates boilerplate code, and manages database migrations instantly.\r\n* Blade Templating: Renders lightweight, compiled frontend layouts smoothly without adding system overhead.\r\n* Built-in Authentication: Secures applications instantly with ready-made login, registration, and password reset structures.\r\n* Queue Management: Offloads heavy tasks like emails to background processes to keep user experiences fast.\r\n\r\nStrategic Business Benefits\r\n\r\n* Rapid Time-to-Market: Speeds up development times, making it ideal for launching Minimum Viable Products (MVPs) quickly.\r\n* Enterprise Security: Protects code automatically against SQL injection, cross-site scripting, and CSRF attacks.\r\n* Massive Community Support: Provides endless learning resources, active forums, and a vast talent pool for hiring.\r\n* Unmatched Ecosystem: Integrates seamlessly with official first-party tools for deployment, billing, and API authentication.', 'General Discussion', 'uploads/images (1).png', '2026-08-07 15:11:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `post_id`, `reported_by`, `reason`, `status`, `created_at`) VALUES
(2, 8, 20, 'dasss', 'open', '2025-10-31 14:09:38');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `maintenance_mode` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `admin_email`, `maintenance_mode`) VALUES
(1, 'NIITDF', 'wisdomoghe@gmail.com', 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings_changes`
--

CREATE TABLE `settings_changes` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `change_description` varchar(255) NOT NULL,
  `changed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(15) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `cover_photo` varchar(255) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `status`, `profile_pic`, `cover_photo`, `email`, `fullname`) VALUES
(15, 'agranyal', '$2y$10$x7plFnhmENVZWQr3e4ins.fU4TbS6B6kOZ7a8wrm7QYJyE4g8w5du', '2025-06-19 07:43:59', NULL, './uploads/profile_agranyal.jpg', './uploads/cover_agranyal.jpg', 'baterenruth3@gmail.com', ''),
(18, 'dave', '$2y$10$RZwv0nmE16NmjLZxP53uFe3ojlYAw4/6fLsUB7zu15Ld9pNI2MCha', '2025-06-19 20:10:52', NULL, './uploads/profile_dave.jpg', './uploads/cover_dave.jpg', 'wisd@g.com', ''),
(20, 'johndoe', '$2y$10$0o9nJVj8D8rcvruue8yy1eaJhp5hpgioZ/yWBoNH0LpV4FiHCthB2', '2025-06-22 19:04:20', NULL, './uploads/profile_johndoe.jpg', './uploads/cover_johndoe.jpg', 'wlcxwale@gmail.com', ''),
(22, 'johndoes', '$2y$10$ZOX8yW8FWF6EnwszSQcFtugV1IqbEtv/4ONQHatvLdy1vU7ADUFey', '2025-06-23 19:45:06', NULL, NULL, NULL, 'wisd@g.com', ''),
(23, 'johndo', '$2y$10$TnEzBHzXrbx5Ix4Gum5gJOieboMpQ/FM2TpPDD9pRjIvaF7iDsv12', '2025-06-24 14:34:11', NULL, NULL, NULL, 'wisd@g.com', ''),
(24, 'johndoede', '$2y$10$RjCR3xxyK5hb87WphH.htuTMKcVzaXwkgVjgQ658YgjyxSfLtDYB.', '2025-06-28 09:13:54', NULL, NULL, NULL, 'wlcxwale@gmail.com', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `group_invites`
--
ALTER TABLE `group_invites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invite_code` (`invite_code`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_id` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_message_attachments`
--
ALTER TABLE `group_message_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `group_message_reactions`
--
ALTER TABLE `group_message_reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `message_id` (`message_id`,`user_id`,`reaction`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_settings`
--
ALTER TABLE `group_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_id` (`group_id`,`setting_key`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `reported_by` (`reported_by`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings_changes`
--
ALTER TABLE `settings_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `group_invites`
--
ALTER TABLE `group_invites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `group_messages`
--
ALTER TABLE `group_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `group_message_attachments`
--
ALTER TABLE `group_message_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_message_reactions`
--
ALTER TABLE `group_message_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `group_settings`
--
ALTER TABLE `group_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings_changes`
--
ALTER TABLE `settings_changes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_invites`
--
ALTER TABLE `group_invites`
  ADD CONSTRAINT `group_invites_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_invites_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_messages`
--
ALTER TABLE `group_messages`
  ADD CONSTRAINT `group_messages_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_messages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_message_attachments`
--
ALTER TABLE `group_message_attachments`
  ADD CONSTRAINT `group_message_attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `group_messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_message_reactions`
--
ALTER TABLE `group_message_reactions`
  ADD CONSTRAINT `group_message_reactions_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `group_messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_message_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_settings`
--
ALTER TABLE `group_settings`
  ADD CONSTRAINT `group_settings_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `settings_changes`
--
ALTER TABLE `settings_changes`
  ADD CONSTRAINT `settings_changes_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
