-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2026 at 01:47 PM
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
-- Database: `motion_ph`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `steps_completed` int(11) NOT NULL,
  `calories_burned` float NOT NULL,
  `routine_name` varchar(255) NOT NULL,
  `time_consumed` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `steps_completed`, `calories_burned`, `routine_name`, `time_consumed`) VALUES
(1, 19, 500, 20, '', 0),
(2, 18, 500, 20, '', 0),
(3, 17, 500, 20, 'Senior Rhythm Flow', 0),
(4, 16, 600, 24, 'Zumba Merengue', 0),
(5, 15, 500, 20, 'Zumba Merengue', 0),
(6, 14, 500, 20, 'Zumba Merengue', 0),
(7, 13, 500, 20, 'Zumba Merengue', 0),
(8, 12, 500, 20, 'Zumba Merengue', 0),
(9, 11, 500, 20, 'Zumba Merengue', 0),
(10, 10, 500, 20, 'The Salsa Side-Step', 0),
(11, 9, 600, 24, 'Zumba Merengue', 0),
(12, 4, 600, 24, 'Zumba Merengue', 0),
(13, 1, 600, 24, 'Zumba Merengue', 0),
(14, 14, 600, 24, 'Zumba Merengue', 0),
(15, 15, 600, 24, 'The Salsa Side-Step', 0),
(16, 16, 600, 24, 'Reggaeton Stomp', 0),
(17, 17, 800, 32, 'Zumba Merengue', 0),
(18, 18, 800, 32, 'Zumba Merengue', 0),
(19, 19, 800, 32, 'Zumba Merengue', 0),
(20, 20, 800, 32, 'The Salsa Side-Step', 0),
(21, 21, 0, 0, 'Zumba Merengue', 0),
(22, 22, 0, 0, 'Zumba Merengue', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dance_genres`
--

CREATE TABLE `dance_genres` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dance_steps`
--

CREATE TABLE `dance_steps` (
  `id` int(11) NOT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `step_name` varchar(100) NOT NULL,
  `step_type` enum('basic','advance') NOT NULL,
  `description` text DEFAULT NULL,
  `video_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dance_steps`
--

INSERT INTO `dance_steps` (`id`, `genre_id`, `step_name`, `step_type`, `description`, `video_file`, `created_at`) VALUES
(1, NULL, 'Zumba Merengue', 'basic', 'Alright Divas! Let’s start with a steady, rhythmic march... feel that beat! Now, shake those hips! Keep your hands moving naturally, you’re doing great!', NULL, '2026-03-31 08:02:34'),
(2, NULL, 'The Salsa Side-Step', '', 'Time for some Salsa! Step out to the side... and back to center. Give me a shoulder shake! Stretch those arms out for balance, keep it moving!', NULL, '2026-03-31 08:02:34'),
(3, NULL, 'Reggaeton Stomp', '', 'Let’s turn it up! Give me a wide march with a knee lift... Stomp! Shake your core and keep those hands up! Pulse with the rhythm, stay strong!', NULL, '2026-03-31 08:02:34'),
(4, NULL, 'Cumbia Sleepy Leg', '', 'Alright Parc Divas, let’s get moving! Keep one foot planted while the other steps back. Stretch your arm forward and Shake your wrist. Switch legs and repeat. You are doing amazing! Keep that heart rate up!', NULL, '2026-03-31 08:02:34'),
(5, NULL, 'The Zumba V-Step', '', 'Alright Parc Divas, let’s get moving! Step forward-wide, then back-together. Stretch your arms up as you step forward. Finish with a quick clap of your Hands. You are doing amazing! Keep that heart rate up!', NULL, '2026-03-31 08:02:34'),
(6, NULL, 'Zumba Cool Down', '', 'Alright Parc Divas, let’s get moving! A very slow, deep-breathing March. Stretch your arms high above your head. Slowly rotate your Hands to lower your heart rate. You are doing amazing! Keep that heart rate up!', NULL, '2026-03-31 08:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `age` int(11) DEFAULT 30,
  `membership_type` varchar(50) DEFAULT 'Free',
  `created_at` timestamp NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `age`, `membership_type`, `created_at`) VALUES
(1, 'Chacha@gmail.com', '', '$2y$10$.vPsEI7juRcJGPkOyD/zj.h.pbTDZGRb2I5aAd.RUBipUFxySmA22', 'user', 30, 'Premium', '2025-10-25 02:00:00'),
(4, 'venus', 'venus@gmail.com', '$2y$10$H3mUuYrRCvgtjb4rGCjAs./UwavuwygGYnjlAeaDEHBODLHIIkgve', 'user', 0, 'Free', '2025-11-22 02:00:00'),
(9, 'yvette', 'admin@parcdivas.com', '$2y$10$NYfgOIzUzp1bmhzi3zkWUODPWCQrzWTSkXxFUlIoKCG7T5EwtwjJy', 'admin', 25, 'Premium', '2025-11-22 02:00:00'),
(10, 'melissa', 'melissaG@gmail.com', '$2y$10$UEAa5bnpmbl5mXeSCHHMyOel2X6cGnSxUGZvzw.nvlrPZIb23TdqK', 'user', 18, 'Free', '2025-11-22 02:00:00'),
(11, 'christian', 'christianF@gmail.com', '$2y$10$V7u009NqJp1vBRQJxpPyFubR1R/64IgrPowc8cNcvO73WJuazPTU2', 'user', 0, 'Free', '2025-11-22 02:00:00'),
(12, 'karen', 'karenE@gmail.com', '$2y$10$diX8jx91sCGKTuRk6cD3uusFiJZhDZ43DbIjFdK4lutG54SVDa97a', 'user', 0, 'Free', '2025-11-22 02:00:00'),
(13, 'Trixiemay', 'Trixie_may@gmail.com', '$2y$10$FqFNkwzYPWQgD0EY9vXnmuWi3lur1JlMP/IVrJaLF8bZem0Geo9py', 'user', 0, 'Free', '2025-12-02 02:00:00'),
(14, 'Carlita', 'CarlitaS_05@gmail.com', '$2y$10$35j2t.R67UwwApFoveEWMu6vfjb/NqbLWxfLzlqF/HlXeqIfKfu.q', 'user', 0, 'Free', '2025-12-02 02:00:00'),
(15, 'sheila', 'SheilaFern86@gmail.com', '$2y$10$4b/foXqYZS9MqRWN9gXSW.gwjSOeLw1X2vGN0OpL3lFbW7D6dLAdW', 'user', 0, 'Free', '2026-01-20 02:00:00'),
(16, 'DeniceFrankie', 'DeniceFF14@gmail.com', '$2y$10$BwC.fX51xQKP1TlRf8BzW.zjD0xil86SbLVCkuaA/uQchNmLmJ6fu', 'user', 0, 'Premium', '2026-01-20 02:00:00'),
(17, 'Stephaniegwenn', 'Stephanie_gwenn@gmail.com', '$2y$10$T/HeLbv6bafvGGqpqEpp/OXPlLSgW1q4PD8x9pKrKaQoNoitn/Z6y', 'user', 0, 'Free', '2026-01-20 02:00:00'),
(18, 'Tristan', 'Tristanandrew04@gmail.com', '$2y$10$XMc4VXhqYv4xUwbPAdi.Se2b7bbpnZu.lFmghYhg1tHyhJLgXUUba', 'user', 0, 'Free', '2026-02-05 02:00:00'),
(19, 'Ann', 'Ann_01@gmail.com', '$2y$10$EQSdyCvA/3.y0bqwqQ312uyzD737/nyl2V6IJwDz0dGzUPj6ZYCJa', 'user', 0, 'Free', '2026-04-09 02:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_age_group`
--

CREATE TABLE `user_age_group` (
  `user_id` int(11) NOT NULL,
  `age_group` enum('Child','Adult','Senior') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_age_group`
--

INSERT INTO `user_age_group` (`user_id`, `age_group`) VALUES
(1, 'Adult');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `genre_id` int(11) DEFAULT NULL,
  `genre_name` varchar(100) DEFAULT NULL,
  `steps_completed` int(11) DEFAULT 0,
  `total_steps` int(11) DEFAULT 0,
  `time_consumed` int(11) DEFAULT 0,
  `date_completed` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `dance_genres`
--
ALTER TABLE `dance_genres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dance_steps`
--
ALTER TABLE `dance_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_age_group`
--
ALTER TABLE `user_age_group`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `dance_genres`
--
ALTER TABLE `dance_genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dance_steps`
--
ALTER TABLE `dance_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dance_steps`
--
ALTER TABLE `dance_steps`
  ADD CONSTRAINT `dance_steps_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `dance_genres` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_age_group`
--
ALTER TABLE `user_age_group`
  ADD CONSTRAINT `user_age_group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
