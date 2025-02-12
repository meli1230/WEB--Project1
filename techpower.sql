-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 05:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `techpower`
--

-- --------------------------------------------------------

--
-- Table structure for table `downloadables`
--

CREATE TABLE `downloadables` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `downloadables`
--

INSERT INTO `downloadables` (`id`, `title`, `link`) VALUES
(1, 'Python Installation', 'attachments/file_1.pdf'),
(2, 'CLion Installation', 'attachments/file_2.pdf'),
(3, 'IntelliJ Installation', 'attachments/file_3.pdf'),
(4, 'Licence JetBrains', 'attachments/file_4.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `event_type` enum('workshop','mentoring','networking','conference') DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`, `event_type`, `max_participants`, `created_by`, `created_at`) VALUES
(15, 'Tech Workshop', 'An introductory workshop on cloud technologies.', '2025-01-02 19:21:00', 'Online', 'workshop', 50, 7, '2024-11-30 14:25:55'),
(16, 'Networking Meetup', 'A networking event for tech enthusiasts.', '2025-01-05 18:00:00', 'Cluj-Napoca', 'networking', 300, 9, '2024-11-30 14:25:55'),
(17, 'AI Mentoring Session', 'Mentoring session focusing on AI career paths.', '2025-01-10 17:00:00', 'Online', 'mentoring', 20, 11, '2024-11-30 14:25:55'),
(18, 'Data Science Conference', 'A conference covering the latest trends in data science.', '2025-01-12 10:00:00', 'Cluj-Napoca', 'conference', 100, 7, '2024-11-30 14:25:55'),
(19, 'Web Development Workshop', 'Learn the basics of web development.', '2025-01-15 14:00:00', 'Online', 'workshop', 40, 9, '2024-11-30 14:25:55'),
(20, 'Startup Networking Event', 'Networking for startup founders and investors.', '2025-01-20 16:00:00', 'Cluj-Napoca', 'networking', 25, 11, '2024-11-30 14:25:55'),
(21, 'Career Mentoring Session', 'One-on-one career mentoring for developers.', '2025-01-22 11:00:00', 'Online', 'mentoring', 15, 7, '2024-11-30 14:25:55'),
(22, 'Frontend Development Bootcamp', 'A hands-on bootcamp on frontend development.', '2025-01-25 09:00:00', 'Cluj-Napoca', 'workshop', 30, 9, '2024-11-30 14:25:55'),
(23, 'Machine Learning Hackathon', 'A competitive hackathon focusing on ML.', '2025-01-28 08:00:00', 'Online', 'conference', 50, 11, '2024-11-30 14:25:55'),
(24, 'Tech Community Meetup', 'A casual meetup for Cluj tech community.', '2025-01-30 19:00:00', 'Cluj-Napoca', 'networking', 20, 7, '2024-11-30 14:25:55');

-- --------------------------------------------------------

--
-- Table structure for table `event_feedback`
--

CREATE TABLE `event_feedback` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_feedback`
--

INSERT INTO `event_feedback` (`id`, `event_id`, `member_id`, `feedback`, `rating`) VALUES
(1, 15, 14, 'asd', 1),
(2, 15, 14, 'asd', 2),
(3, 15, 14, 'sad', 5),
(4, 15, 14, 'asd', 5),
(5, 15, 14, 'asd', 5),
(6, 15, 14, 'asd', 5),
(7, 15, 14, 'asd', 5),
(8, 15, 14, 'asd', 5),
(9, 15, 14, 'asd', 5),
(10, 16, 6, 'good', 4),
(11, 16, 6, 'good', 4),
(12, 15, 6, 'bad', 2),
(13, 15, 6, 'bad', 2),
(14, 15, 12, 'aas', 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('confirmed','waiting','cancelled') DEFAULT 'confirmed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_registrations`
--

INSERT INTO `event_registrations` (`id`, `member_id`, `event_id`, `registration_date`, `status`) VALUES
(30, 14, 15, '2024-11-30 14:45:49', 'confirmed'),
(32, 6, 16, '2024-11-30 16:48:05', 'confirmed'),
(33, 6, 15, '2024-11-30 16:51:38', 'confirmed'),
(35, 12, 15, '2024-12-01 18:08:11', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `type` enum('fulltime','parttime','internship') DEFAULT NULL,
  `experience_level` enum('junior','senior','expert') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `title`, `company`, `type`, `experience_level`) VALUES
(1, 'Software Engineer', 'DataBloom Analytics', 'parttime', 'senior'),
(3, 'Software Engineer', 'DataBloom Analytics', 'fulltime', 'expert'),
(4, 'CyberSecurity Helpdesk', 'NTT Data', 'fulltime', 'senior'),
(5, 'Data Scientist', 'OpenAI Solution', 'fulltime', 'expert'),
(6, 'Frontend Developer', 'Creative Minds Inc.', 'parttime', 'senior'),
(7, 'System Administrator', 'CloudTech Services', 'parttime', 'junior'),
(8, 'IT Support', 'TechAssist Hub', 'internship', 'junior'),
(9, 'Software Engineer', 'NTT Data', 'fulltime', 'senior');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `job_id`, `member_id`) VALUES
(1, 1, 14),
(2, 3, 14),
(3, 4, 14),
(4, 1, 19);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pswd` varchar(255) NOT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `expertise` text DEFAULT NULL,
  `linkedin_profile` varchar(255) DEFAULT NULL,
  `status` enum('member','mentor','admin') DEFAULT 'member',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `studies` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `first_name`, `last_name`, `email`, `pswd`, `profession`, `company`, `expertise`, `linkedin_profile`, `status`, `created_at`, `studies`) VALUES
(6, 'Evelynn', 'Scott', 'evelynscott@example.com', '$2y$10$SfttoiIbTa/o1o7.OCoK7OkOkogMe1CoJHxLb4xNInjOZuUSSSgmi', 'Cybersecurity Specialist', 'SecureNet Systems', 'Network Security, Threat Analysis', 'https://linkedin.com/in/evelynscott', 'member', '2024-11-29 13:40:49', NULL),
(7, 'Charlotte', 'Adams', 'charlotte.adams@example.com', '$2y$10$LWpCWL5nz2K8rB.XWGzntuUTgiJQmfQZ53QD8cymaxAwBU91LAAeC', 'Software Engineer', 'QuantifyTech Inc.', 'Data Visualisation, Statistical Analysis ', 'https://linkedin.com/in/charlotteadams', 'mentor', '2024-11-29 13:42:50', ''),
(8, 'Ava', 'Nguyen', 'ava.nguyen@example.com', '$2y$10$iE4ImSEL/cBC48NsPZH9GuTXl.N5EyvFkdT2.pwcP5wPJSIiCCpMS', 'Marketing Specialist', 'BrightBrand Co.', 'Digital Campaigns, SEO Optimization', 'https://linkedin.com/in/avanguyen', 'member', '2024-11-29 13:44:20', NULL),
(9, 'Mia', 'Patel', 'mia.patel@example.com', '$2y$10$jzkUT2iJ0.rQHGReFeyESeA.uv9cW1E8nrhMukENYLOiGS45VY41i', 'UX Designer', 'NeoTech Solutions', 'Microservices, Scalability   ', 'https://linkedin.com/in/isabellajohnson', 'mentor', '2024-11-29 13:45:17', 'FSEGA'),
(10, 'Sophia ', 'Li', 'sophia.li@example.com', '$2y$10$tgpJF8RUMnpBz2g8ntFDbuxKCLtEjrULIXPDXxRRw3VTVX83xLGoi', 'Software Engineer', 'NeoTech Solutions', 'Full-Stack Development, API Design', 'https://linkedin.com/in/sophiali', 'member', '2024-11-29 13:46:19', NULL),
(11, 'Olivia ', 'Hansen', 'olivia.hansen@example.com', '$2y$10$aG.yPgdyJ3kiGKC9/e6rK.DRdfF3Z9wvnP/VGhe1aibPRmqrChYkK', 'Data Scientist', 'DataBloom Analytics', 'Machine Learning, Predictive Modeling', 'https://linkedin.com/in/oliviahansen', 'mentor', '2024-11-29 13:47:10', NULL),
(12, 'Emma ', 'Carter', 'emma.carter@example.com', '$2y$10$zPT36I2Y/VYeltRES8t8y.215dHKS8aNNet0eErwO7fZ4OH82neJK', 'Software Engineer', 'TechWave Solutions', 'Backend Development, Cloud Architecture   ', 'https://linkedin.com/in/emmacarter', 'member', '2024-11-29 13:48:01', ''),
(13, 'Sophia', 'Martinez', 'sophia.martinez@example.com', '$2y$10$ctX/5MMtVnz1v.6m1aUL4OR8lCMEt9xnZ06qZ/lK6dHrbxuuoTaS2', 'Product Manager', 'Innovate Solutions', 'Agile Methodology, Product Design', 'https://linkedin.com/in/sophiamartinez', 'member', '2024-11-29 14:46:49', NULL),
(14, 'Ava', 'Williams', 'ava.williams@example.com', '$2y$10$dLEod3OJLJJtn6P/Zt8XmuPzztEIkhMVbFtBfJENibvNGEDtiYbHi', 'Marketing Specialist', 'GrowthHack Labs', 'Digital Marketing, Content Strategy ', 'https://linkedin.com/in/avawilliams', 'member', '2024-11-29 14:48:00', 'FSEGA'),
(15, 'Isabella', 'Taylor', 'isabella.taylor@example.com', '$2y$10$61iXuEEUC4eba2KSSuDXhOJTNdkAHRHNxteLYCf0mvpuo8974iY7G', 'Software Developer', 'CodeCraft Technologies', 'Frontend Development, React.js    ', 'https://linkedin.com/in/isabellataylor', 'member', '2024-11-29 14:48:38', 'FSEGA'),
(19, 'Melisa', 'Marian', 'melisa.marian@admin.com', '$2y$10$FHympvUPOxOpN.EUBXqag.0VFT8OQ0qixdcqUA6rZwIdT70DOecgK', '', '', '', 'https://linkedin.com/melisa', 'admin', '2024-12-01 14:26:57', ''),
(20, 'Paula', 'Moldovan', 'paula.moldovan@admin.com', '$2y$10$thTgzThY8EaAIYVTgc5R6eYWx6w.dxU9vkUjmrErz3S9VrfpRXAdS', '', '', '', 'https://linkedin.com/paula', 'admin', '2024-12-01 14:27:57', ''),
(21, 'Alexandra', 'Nanu', 'alexandra.nanu@admin.com', '$2y$10$5RRjJkxtSwf2XCsOeoAcUOvHuNPpw5VkTOUzejDgE7eT/qx3kp1Ve', '', '', '', 'https://linkedin.com/alexandra', 'admin', '2024-12-01 14:28:45', '');

-- --------------------------------------------------------

--
-- Table structure for table `mentorships`
--

CREATE TABLE `mentorships` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `time_slot` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentorships`
--

INSERT INTO `mentorships` (`id`, `mentor_id`, `member_id`, `time_slot`) VALUES
(2, 7, 15, '2024-12-23 17:18:00'),
(3, 7, 6, '2024-12-31 17:18:00'),
(7, 11, 15, '2024-12-11 18:49:00'),
(8, 9, 15, '2024-12-18 16:23:00');

-- --------------------------------------------------------

--
-- Table structure for table `mentorship_feedback`
--

CREATE TABLE `mentorship_feedback` (
  `id` int(11) NOT NULL,
  `mentorship_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentorship_feedback`
--

INSERT INTO `mentorship_feedback` (`id`, `mentorship_id`, `member_id`, `feedback`, `rating`) VALUES
(1, 2, 15, 'very good!', 5),
(2, 3, 6, 'good', 5),
(3, 2, 6, 'good', 4),
(4, 2, 6, 'ok', 3),
(5, 2, 6, 'ok', 3),
(6, 2, 6, 'ok', 3),
(7, 2, 6, 'ok', 3),
(10, 7, 15, 'very good', 4);

-- --------------------------------------------------------

--
-- Table structure for table `mentorship_progress`
--

CREATE TABLE `mentorship_progress` (
  `id` int(11) NOT NULL,
  `mentorship_id` int(11) NOT NULL,
  `task_description` text NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentorship_progress`
--

INSERT INTO `mentorship_progress` (`id`, `mentorship_id`, `task_description`, `is_completed`) VALUES
(9, 3, 'asd', 1),
(10, 3, 'asd', 1),
(11, 7, 'task 1', 1),
(12, 8, 'task1', 1),
(13, 8, 'task2', 1);

-- --------------------------------------------------------

--
-- Table structure for table `podcasts`
--

CREATE TABLE `podcasts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `podcasts`
--

INSERT INTO `podcasts` (`id`, `title`, `link`) VALUES
(1, 'From Frying Chicken to Working at Google with Danny Thompson', 'https://learntocodewith.me/podcast/from-frying-chicken-to-working-at-google-danny-thompson/'),
(2, 'Linux Unplugged', 'https://linuxunplugged.com/15'),
(3, 'What does an ethical approach to AI look like', 'https://mission.org/it-visionaries/what-does-an-ethical-approach-to-a-i-look-like/'),
(4, 'Using GenAI for DevSecOps', 'https://www.thecloudcast.net/');

-- --------------------------------------------------------

--
-- Table structure for table `tutorials`
--

CREATE TABLE `tutorials` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tutorials`
--

INSERT INTO `tutorials` (`id`, `title`, `link`) VALUES
(1, 'Installation guide: PHPStorm', 'https://www.jetbrains.com/help/phpstorm/installation-guide.html'),
(2, 'Installation guide: PyCharm', 'https://www.jetbrains.com/help/pycharm/installation-guide.html'),
(3, 'Installation guide: IntelliJ IDEA', 'https://www.jetbrains.com/help/idea/installation-guide.html'),
(4, 'Installation guide: CLion', 'https://www.jetbrains.com/help/clion/installation-guide.html');

-- --------------------------------------------------------

--
-- Table structure for table `video_materials`
--

CREATE TABLE `video_materials` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_materials`
--

INSERT INTO `video_materials` (`id`, `title`, `link`) VALUES
(1, 'Cybersecurity for beginners', 'https://youtu.be/_DVVNOGYtmU?si=HaICQ2JbPPtInrTf'),
(2, 'Object Oriented Programming (OOP)', 'https://youtu.be/wN0x9eZLix4?si=mCfhewhRAGJvjPSl'),
(3, 'RAG Chatbot - build and deployment', 'https://youtu.be/d-VKYF4Zow0?si=HMszyV69xA29970a'),
(4, 'IT Career Paths', 'https://youtu.be/XmWkcePhf84?si=PWlILEsgvcrJvrxM');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `downloadables`
--
ALTER TABLE `downloadables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `event_feedback`
--
ALTER TABLE `event_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `mentorships`
--
ALTER TABLE `mentorships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `mentorship_feedback`
--
ALTER TABLE `mentorship_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentorship_id` (`mentorship_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `mentorship_progress`
--
ALTER TABLE `mentorship_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentorship_id` (`mentorship_id`);

--
-- Indexes for table `podcasts`
--
ALTER TABLE `podcasts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tutorials`
--
ALTER TABLE `tutorials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_materials`
--
ALTER TABLE `video_materials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `downloadables`
--
ALTER TABLE `downloadables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `event_feedback`
--
ALTER TABLE `event_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `mentorships`
--
ALTER TABLE `mentorships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mentorship_feedback`
--
ALTER TABLE `mentorship_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `mentorship_progress`
--
ALTER TABLE `mentorship_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `podcasts`
--
ALTER TABLE `podcasts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tutorials`
--
ALTER TABLE `tutorials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `video_materials`
--
ALTER TABLE `video_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `members` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `event_feedback`
--
ALTER TABLE `event_feedback`
  ADD CONSTRAINT `event_feedback_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_feedback_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mentorships`
--
ALTER TABLE `mentorships`
  ADD CONSTRAINT `mentorships_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `mentorships_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints for table `mentorship_feedback`
--
ALTER TABLE `mentorship_feedback`
  ADD CONSTRAINT `mentorship_feedback_ibfk_1` FOREIGN KEY (`mentorship_id`) REFERENCES `mentorships` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentorship_feedback_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mentorship_progress`
--
ALTER TABLE `mentorship_progress`
  ADD CONSTRAINT `mentorship_progress_ibfk_1` FOREIGN KEY (`mentorship_id`) REFERENCES `mentorships` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
