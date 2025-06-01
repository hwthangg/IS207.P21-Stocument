-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 31, 2025 lúc 04:49 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `web_sharing`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookmarks`
--

CREATE TABLE `bookmarks` (
  `user_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `bookmarked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comment`
--

CREATE TABLE `comment` (
  `com_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `context` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contact_details`
--

CREATE TABLE `contact_details` (
  `sr_no` int(11) NOT NULL,
  `address` varchar(100) NOT NULL,
  `gmap` varchar(100) NOT NULL,
  `pn1` varchar(30) NOT NULL,
  `pn2` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fb` varchar(100) NOT NULL,
  `insta` varchar(100) NOT NULL,
  `tw` varchar(100) NOT NULL,
  `iframe` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `contact_details`
--

INSERT INTO `contact_details` (`sr_no`, `address`, `gmap`, `pn1`, `pn2`, `email`, `fb`, `insta`, `tw`, `iframe`) VALUES
(1, 'Khu phố 6, Linh Trung, Thủ Đức, HCMa', 'https://maps.google.com', '0913665524', '0947375574', '23520527@uit.edu.vn', 'https://facebook.com/nguyen.hoang.654462', 'https://instagram.com/ng_hoang24', 'https://twitter.com/HoangNg29473824', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3918.2284299123517!2d106.80002117427276!3d10.870222907458189!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317527587e9ad5bf%3A0xafa66f9c8be3c91!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBDw7RuZyBuZ2jhu4cgVGjDtG5nIHRpbiAtIMSQSFFHIFRQLkhDTQ!5e0!3m2!1svi!2s!4v1748514374394!5m2!1svi!2s');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file_path_subject` varchar(255) NOT NULL,
  `file_path_username` varchar(255) NOT NULL,
  `download_count` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subject_id` int(11) NOT NULL,
  `major_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `majors`
--

CREATE TABLE `majors` (
  `major_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `majors`
--

INSERT INTO `majors` (`major_id`, `name`) VALUES
(7, 'An toàn Thông tin'),
(1, 'Công nghệ Thông tin'),
(2, 'Hệ thống Thông tin'),
(9, 'Khoa học Dữ liệu'),
(3, 'Khoa học Máy tính'),
(5, 'Kỹ thuật Máy tính'),
(4, 'Kỹ thuật Phần mềm'),
(6, 'Mạng máy tính và Truyền thông dữ liệu'),
(11, 'Thiết kế Vi mạch'),
(8, 'Thương mại điện tử'),
(10, 'Trí tuệ Nhân tạo'),
(12, 'Truyền thông Đa phương tiện');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `sr_no` int(11) NOT NULL,
  `site_title` varchar(50) NOT NULL,
  `site_about` varchar(500) NOT NULL,
  `shutdown` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`sr_no`, `site_title`, `site_about`, `shutdown`) VALUES
(1, 'Stocument', 'Nền tảng của chúng tôi được phát triển nhằm hỗ trợ sinh viên trong việc chia sẻ và truy cập tài liệu học tập một cách dễ dàng và hiệu quả. Với mục tiêu tạo ra một không gian học tập trực tuyến, nền tảng này cung cấp cho sinh viên cơ hội để chia sẻ các tài liệu học thuật, bao gồm sách, bài giảng, tài liệu tham khảo và các tài liệu khác có giá trị học tập.', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `major_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `subjects`
--

INSERT INTO `subjects` (`subject_id`, `name`, `major_id`) VALUES
(23, 'NT015 - Giới thiệu ngành ATTT', 7),
(24, 'NT106 - Lập trình mạng căn bản', 7),
(25, 'NT140 - An toàn mạng', 7),
(26, 'NT230 - Cơ chế hoạt động của mã độc', 7),
(27, 'NT132 - Quản trị mạng và hệ thống', 7),
(28, 'NT219 - Mật mã học', 7),
(29, 'NT209 - Lập trình hệ thống', 7),
(30, 'NT208 - Lập trình ứng dụng web', 7),
(31, 'NT521 - Lập trình an toàn và khai thác lỗ hổng phần mềm', 7),
(32, 'IE101 - Cơ sở hạ tầng công nghệ thông tin', 1),
(33, 'IE103 - Quản lý thông tin', 1),
(34, 'IE104 - Internet và công nghệ Web', 1),
(35, 'IE105 - Nhập môn bảo đảm và an ninh thông tin', 1),
(36, 'IE106 - Thiết kế giao diện người dùng', 1),
(37, 'IE108 - Phân tích thiết kế phần mềm', 1),
(38, 'IS336 - Hoạch định nguồn lực doanh nghiệp', 2),
(39, 'IS201 - Phân tích thiết kế hệ thống thông tin', 2),
(40, 'IS210 - Hệ quản trị cơ sở dữ liệu', 2),
(41, 'IS208 - Quản lý dự án công nghệ thông tin', 2),
(42, 'IS216 - Lập trình Java', 2),
(43, 'IS207 - Phát triển ứng dụng web', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `team_details`
--

CREATE TABLE `team_details` (
  `sr_no` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `picture` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `team_details`
--

INSERT INTO `team_details` (`sr_no`, `name`, `picture`) VALUES
(9, 'Nguyễn Bá Hoàng', 'images/IMG_1747381878.webp'),
(10, 'Trần Hữu Tâm', 'images/IMG_1747381782.webp'),
(11, 'Quý Trương', 'images/IMG_1747381794.webp'),
(12, 'Lê Thị Thảo Vân', 'images/IMG_1747381811.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` timestamp NULL DEFAULT NULL,
  `verify_token` varchar(255) DEFAULT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `full_name`, `password`, `is_verified`, `last_login`, `reset_token`, `reset_token_expiry`, `verify_token`, `role`, `status`, `created_at`, `updated_at`) VALUES
(54, 'tonikrosspes1', 'tonikrosspes1@gmail.com', 'Kross Toni', '$2y$10$9Fhoo4PYgfoGRd/mhsHej.DtKrrbYAm4.sgT25ujKk4sSHFHzmrZ2', 1, '2025-05-31 17:53:07', NULL, NULL, NULL, 'student', 1, '2025-05-31 10:53:07', '2025-05-31 10:53:07'),
(55, 'tranhuutam125', 'tranhuutam125@gmail.com', 'Tam Tran', '$2y$10$9/PaUmSrsvRuloGcciEmH.AmJjoc4jl1gwFP..rLZR.sfYHIpIyoG', 1, '2025-05-31 19:49:01', NULL, NULL, NULL, 'student', 1, '2025-05-31 12:49:01', '2025-05-31 12:49:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_queries`
--

CREATE TABLE `user_queries` (
  `sr_no` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` varchar(500) NOT NULL,
  `datentime` datetime NOT NULL DEFAULT current_timestamp(),
  `seen` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`user_id`,`doc_id`),
  ADD KEY `doc_id` (`doc_id`);

--
-- Chỉ mục cho bảng `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`com_id`),
  ADD KEY `doc_id` (`doc_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `contact_details`
--
ALTER TABLE `contact_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_documents_subject` (`subject_id`),
  ADD KEY `fk_documents_major` (`major_id`);

--
-- Chỉ mục cho bảng `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`major_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `major_id` (`major_id`);

--
-- Chỉ mục cho bảng `team_details`
--
ALTER TABLE `team_details`
  ADD PRIMARY KEY (`sr_no`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `user_queries`
--
ALTER TABLE `user_queries`
  ADD PRIMARY KEY (`sr_no`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `comment`
--
ALTER TABLE `comment`
  MODIFY `com_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT cho bảng `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT cho bảng `majors`
--
ALTER TABLE `majors`
  MODIFY `major_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `team_details`
--
ALTER TABLE `team_details`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT cho bảng `user_queries`
--
ALTER TABLE `user_queries`
  MODIFY `sr_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_documents_major` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`),
  ADD CONSTRAINT `fk_documents_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`);

--
-- Các ràng buộc cho bảng `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`);

--
-- Các ràng buộc cho bảng `user_queries`
--
ALTER TABLE `user_queries`
  ADD CONSTRAINT `user_queries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
