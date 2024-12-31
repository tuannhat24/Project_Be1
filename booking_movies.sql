-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 31, 2024 lúc 08:23 PM
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
-- Cơ sở dữ liệu: `booking_movies`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `banners`
--

INSERT INTO `banners` (`id`, `movie_id`, `image`, `status`, `created_at`) VALUES
(1, 4, 'doctor_strange_banner.jpg', 'active', '2024-12-23 09:56:08'),
(2, 2, 'spiderman-no-way-home.jpg', 'active', '2024-12-23 09:56:09'),
(8, 7, 'jurassic_banner.jpg', 'active', '2024-12-24 22:43:13'),
(9, 11, 'tiec_trang_mau_banner.jpg', 'active', '2024-12-24 22:43:38'),
(10, 17, 'nguoi_bat_tu_banner.jpg', 'active', '2024-12-24 22:45:30'),
(11, 8, 'hai_phuong_banner.jpg', 'active', '2024-12-24 22:46:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(15) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','credit_card','momo') DEFAULT 'cash',
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `booking_code` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `schedule_id`, `customer_name`, `customer_email`, `customer_phone`, `total_price`, `payment_method`, `status`, `booking_code`, `created_at`, `updated_at`) VALUES
(296, 3, 3, 'Nhat Tuan', 'tuannhat124@gmail.com', '0987654321', 80000.00, 'cash', 'confirmed', 'BK-20241221223747-U0', '2024-12-21 21:37:47', '2024-12-23 22:27:45'),
(297, 3, 4, 'Nhat Tuan', 'tuannhat124@gmail.com', '0987654321', 150000.00, 'cash', 'cancelled', 'BK-20241223232024-U0', '2024-12-23 22:20:24', '2024-12-23 22:27:57'),
(299, 3, 2, 'Nhat Tuan', 'tuannhat124@gmail.com', '0987654321', 120000.00, 'cash', 'cancelled', 'BK-20241225035012-U0', '2024-12-25 02:50:12', '2024-12-25 02:50:29'),
(300, 3, 3, 'Nhat Tuan', 'tuannhat124@gmail.com', '0987654321', 160000.00, '', 'pending', 'BK-20241225035654-U0', '2024-12-25 02:56:54', '2024-12-25 02:56:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_seats`
--

CREATE TABLE `booking_seats` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_seats`
--

INSERT INTO `booking_seats` (`id`, `booking_id`, `seat_id`, `price`, `created_at`) VALUES
(256, 296, 891, 80000.00, '2024-12-21 21:37:47'),
(257, 297, 1008, 150000.00, '2024-12-23 22:20:24'),
(259, 299, 844, 120000.00, '2024-12-25 02:50:12'),
(260, 300, 915, 80000.00, '2024-12-25 02:56:54'),
(261, 300, 914, 80000.00, '2024-12-25 02:56:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `movie_id`, `user_id`, `comment`, `created_at`) VALUES
(8, 2, 3, 'dsa', '2024-12-24 20:43:42'),
(9, 14, 1, 'dsaa', '2024-12-24 22:25:27'),
(10, 1, 3, 'dsadad', '2024-12-25 02:49:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `genres`
--

CREATE TABLE `genres` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `genres`
--

INSERT INTO `genres` (`id`, `name`, `slug`) VALUES
(1, 'Hành Động', 'action'),
(2, 'Hài', 'comedy'),
(3, 'Tâm Lý', 'drama'),
(4, 'Kinh Dị', 'horror'),
(5, 'Tình Cảm', 'romance'),
(6, 'Khoa Học Viễn Tưởng', 'scifi'),
(7, 'Phim Giả Tưởng', 'fantasy'),
(8, 'Phiêu Lưu', 'adventure'),
(9, 'Hoạt Hình', 'animation'),
(10, 'Bí Ẩn', 'mystery'),
(11, 'Chiến Tranh', 'war'),
(12, 'Võ Thuật', 'martial-arts'),
(13, 'Âm Nhạc', 'musical'),
(14, 'Thể Thao', 'sport');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `poster` varchar(255) DEFAULT NULL,
  `trailer_url` varchar(255) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `release_date` date NOT NULL,
  `country` varchar(100) DEFAULT 'Việt Nam',
  `director` varchar(100) DEFAULT NULL,
  `status` enum('coming_soon','now_showing','ended') DEFAULT 'coming_soon',
  `rates` decimal(3,1) DEFAULT 10.0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `banner_title` varchar(255) DEFAULT NULL,
  `banner_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `poster`, `trailer_url`, `duration`, `release_date`, `country`, `director`, `status`, `rates`, `created_at`, `updated_at`, `banner_title`, `banner_description`) VALUES
(1, 'The Matrix Resurrections', 'Return to the world of The Matrix', 'poster_matrix.jpg', NULL, 148, '2023-12-22', 'Việt Nam', 'Lana Wachowski', 'now_showing', 10.0, '2024-12-19 06:25:42', '2024-12-21 23:25:22', NULL, NULL),
(2, 'Spider-Man: No Way Home', 'Spider-Man faces his greatest challenge', 'snwh_poster_bluemontage.jpg', NULL, 150, '2023-12-17', 'Việt Nam', 'Jon Watts', 'now_showing', 10.0, '2024-12-19 06:25:42', '2024-12-21 19:45:47', NULL, NULL),
(3, 'Avatar: The Way of Water', 'Explore the oceans of Pandora in this stunning sequel.', 'avatar_way_of_water.jpg', NULL, 192, '2024-01-01', 'Mỹ', 'James Cameron', 'coming_soon', 9.8, '2024-12-20 03:00:00', '2024-12-22 01:00:00', NULL, NULL),
(4, 'Doctor Strange in the Multiverse of Madness', 'Doctor Strange ventures into the unknown multiverse.', 'doctor_strange_poster.jpg', NULL, 126, '2024-02-15', 'Mỹ', 'Sam Raimi', 'now_showing', 9.5, '2024-12-20 04:00:00', '2024-12-22 02:00:00', NULL, NULL),
(5, 'The Batman', 'A dark and gritty tale of the caped crusader.', 'batman_poster.jpg', NULL, 176, '2024-03-10', 'Anh', 'Matt Reeves', 'coming_soon', 9.7, '2024-12-20 05:00:00', '2024-12-22 03:00:00', NULL, NULL),
(6, 'Black Panther: Wakanda Forever', 'Honoring a legacy and protecting the future.', 'wakanda_forever_poster.jpg', NULL, 161, '2024-05-05', 'Mỹ', 'Ryan Coogler', 'coming_soon', 9.6, '2024-12-20 06:00:00', '2024-12-22 04:00:00', NULL, NULL),
(7, 'Jurassic World Dominion', 'Humans and dinosaurs coexist in an epic conclusion.', 'jurassic_poster.jpg', NULL, 147, '2024-06-15', 'Mỹ', 'Colin Trevorrow', 'now_showing', 8.5, '2024-12-20 07:00:00', '2024-12-24 22:52:24', NULL, NULL),
(8, 'Hai Phượng', 'Hành trình người mẹ chiến đấu để cứu con gái.', 'hai_phuong_poster.jpg', NULL, 98, '2019-02-22', 'Việt Nam', 'Lê Văn Kiệt', 'ended', 9.0, '2024-12-20 08:00:00', '2024-12-22 06:00:00', NULL, NULL),
(9, 'Bố Già', 'Chuyện gia đình đời thường đầy ý nghĩa và cảm động.', 'bo_gia_poster.jpg', NULL, 128, '2021-03-05', 'Việt Nam', 'Trấn Thành', 'ended', 9.2, '2024-12-20 09:00:00', '2024-12-22 07:00:00', NULL, NULL),
(10, 'Mắt Biếc', 'Câu chuyện tình yêu sâu lắng dựa trên tiểu thuyết của Nguyễn Nhật Ánh.', 'mat_biec_poster.jpg', NULL, 117, '2019-12-20', 'Việt Nam', 'Victor Vũ', 'ended', 9.1, '2024-12-20 10:00:00', '2024-12-22 08:00:00', NULL, NULL),
(11, 'Tiệc Trăng Máu', 'Một buổi tiệc tối định mệnh, bí mật dần được hé lộ.', 'tiec_trang_mau_poster.jpg', NULL, 117, '2020-10-20', 'Việt Nam', 'Nguyễn Quang Dũng', 'now_showing', 9.3, '2024-12-20 11:00:00', '2024-12-22 09:00:00', NULL, NULL),
(12, 'Em Chưa 18', 'Bộ phim hài lãng mạn gây sốt phòng vé Việt Nam.', 'em_chua_18_poster.jpg', NULL, 95, '2017-04-28', 'Việt Nam', 'Lê Thanh Sơn', 'ended', 8.8, '2024-12-20 12:00:00', '2024-12-22 10:00:00', NULL, NULL),
(13, 'Ròm', 'Câu chuyện về tuổi trẻ đường phố đầy khắc nghiệt.', 'rom_poster.jpg', NULL, 79, '2020-09-25', 'Việt Nam', 'Trần Thanh Huy', 'ended', 9.1, '2024-12-20 13:00:00', '2024-12-22 11:00:00', NULL, NULL),
(14, 'Song Lang', 'Tình bạn đặc biệt trong thế giới cải lương Việt.', 'song_lang_poster.jpg', NULL, 100, '2018-08-17', 'Việt Nam', 'Leon Lê', 'ended', 9.0, '2024-12-20 14:00:00', '2024-12-22 12:00:00', NULL, NULL),
(15, 'Cua Lại Vợ Bầu', 'Chuyện tình hài hước giữa Trọng Thoại và người yêu cũ.', 'cua_lai_vo_bau_poster.jpg', NULL, 100, '2019-02-05', 'Việt Nam', 'Nhất Trung', 'ended', 8.5, '2024-12-20 15:00:00', '2024-12-22 13:00:00', NULL, NULL),
(16, 'Lật Mặt 5: 48H', 'Một cuộc chạy đua sinh tử trong 48 giờ.', 'lat_mat_5_poster.jpg', NULL, 100, '2021-04-16', 'Việt Nam', 'Lý Hải', 'now_showing', 8.8, '2024-12-20 16:00:00', '2024-12-22 14:00:00', NULL, NULL),
(17, 'Người Bất Tử', 'Hành trình khám phá câu chuyện huyền bí.', 'nguoi_bat_tu_poster.jpg', NULL, 132, '2018-10-26', 'Việt Nam', 'Victor Vũ', 'ended', 8.9, '2024-12-20 17:00:00', '2024-12-22 15:00:00', NULL, NULL),
(18, 'Tháng Năm Rực Rỡ', 'Câu chuyện về tình bạn và tuổi trẻ.', 'thang_nam_ruc_ro_poster.jpg', NULL, 118, '2018-03-09', 'Việt Nam', 'Nguyễn Quang Dũng', 'coming_soon', 9.2, '2024-12-20 18:00:00', '2024-12-24 22:52:50', NULL, NULL),
(19, 'Chị Mười Ba: Ba Ngày Sinh Tử', 'Câu chuyện giang hồ đầy gay cấn.', 'chi_muoi_ba_poster.jpg', NULL, 96, '2020-12-25', 'Việt Nam', 'Võ Thanh Hòa', 'ended', 8.7, '2024-12-20 19:00:00', '2024-12-22 17:00:00', NULL, NULL),
(20, 'Hồn Papa Da Con Gái', 'Câu chuyện hoán đổi thân xác đầy thú vị.', 'hon_papa_da_con_gai_poster.jpg', NULL, 120, '2018-12-28', 'Việt Nam', 'Ken Ochiai', 'ended', 8.6, '2024-12-20 20:00:00', '2024-12-22 18:00:00', NULL, NULL),
(21, 'Sài Gòn Trong Cơn Mưa', 'Lãng mạn giữa dòng chảy Sài Gòn.', 'sg_trong_con_mua_poster.jpg', NULL, 103, '2020-11-06', 'Việt Nam', 'Lê Minh Hoàng', 'now_showing', 8.9, '2024-12-20 21:00:00', '2024-12-22 19:00:00', NULL, NULL),
(22, 'Mẹ Chồng', 'Xung đột gia đình giữa ba thế hệ.', 'me_chong_poster.jpg', NULL, 95, '2017-12-01', 'Việt Nam', 'Lý Minh Thắng', 'ended', 8.8, '2024-12-20 22:00:00', '2024-12-22 20:00:00', NULL, NULL),
(23, 'Chàng Vợ Của Em', 'Câu chuyện hoán đổi vai trò thú vị.', 'chang_vo_cua_em_poster.jpg', NULL, 97, '2018-08-17', 'Việt Nam', 'Charlie Nguyễn', 'now_showing', 8.9, '2024-12-20 23:00:00', '2024-12-24 22:51:59', NULL, NULL),
(34, 'eqweqw', 'dsadad', '11dd0e76d9eb2381c4bc94cfae679141cdd283980aa1470f640b6ab7032ac2f4.jpg', NULL, 21, '2024-12-22', 'Việt Nam', NULL, 'now_showing', 10.0, '2024-12-25 02:51:22', '2024-12-25 02:51:22', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `movie_genres`
--

CREATE TABLE `movie_genres` (
  `movie_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `movie_genres`
--

INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES
(1, 1),
(1, 6),
(2, 1),
(2, 6),
(3, 1),
(3, 6),
(3, 7),
(4, 1),
(4, 6),
(4, 7),
(4, 8),
(5, 1),
(5, 6),
(5, 14),
(6, 1),
(6, 6),
(6, 7),
(6, 8),
(8, 1),
(8, 3),
(9, 2),
(9, 3),
(9, 5),
(10, 5),
(10, 13),
(11, 3),
(12, 2),
(12, 3),
(12, 5),
(13, 3),
(13, 8),
(14, 3),
(15, 2),
(15, 5),
(16, 1),
(16, 7),
(16, 10),
(17, 1),
(17, 7),
(18, 5),
(18, 13),
(19, 1),
(19, 2),
(20, 2),
(20, 7),
(21, 3),
(21, 5),
(22, 3),
(22, 12),
(23, 2),
(23, 5),
(34, 3),
(34, 10),
(34, 14);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 10),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL,
  `room_type` enum('2D','3D','4D') DEFAULT '2D',
  `status` enum('active','maintenance') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `rooms`
--

INSERT INTO `rooms` (`id`, `theater_id`, `name`, `capacity`, `room_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Room 1', 100, '2D', 'active', '2024-12-19 06:32:00', '2024-12-25 02:52:14'),
(2, 1, 'Room 2', 120, '3D', 'active', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(3, 2, 'Room 1', 80, '2D', 'active', '2024-12-19 06:32:00', '2024-12-24 15:48:34'),
(4, 2, 'Room 2', 90, '4D', 'active', '2024-12-19 06:32:00', '2024-12-24 15:48:27'),
(6, 7, 'room 1', 50, '3D', 'active', '2024-12-24 21:51:12', '2024-12-24 21:51:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('active','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `schedules`
--

INSERT INTO `schedules` (`id`, `movie_id`, `room_id`, `show_date`, `show_time`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2024-12-20', '18:00:00', 10000.00, 'active', '2024-12-19 06:32:30', '2024-12-23 18:55:27'),
(2, 1, 2, '2024-12-26', '20:00:00', 120000.00, 'active', '2024-12-19 06:32:30', '2024-12-25 00:40:46'),
(3, 2, 3, '2024-12-25', '19:30:00', 80000.00, 'active', '2024-12-19 06:32:30', '2024-12-24 18:59:16'),
(4, 2, 4, '2024-12-25', '21:00:00', 150000.00, 'cancelled', '2024-12-19 06:32:30', '2024-12-24 18:59:11'),
(5, 16, 6, '2024-12-28', '09:30:00', 120000.00, 'active', '2024-12-24 23:25:06', '2024-12-25 00:41:00'),
(6, 4, 3, '2024-12-27', '10:00:00', 200000.00, 'active', '2024-12-25 00:39:51', '2024-12-25 00:40:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `seat_row` varchar(2) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `seat_type` enum('normal','vip','couple') DEFAULT 'normal',
  `status` enum('available','booked','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `seats`
--

INSERT INTO `seats` (`id`, `room_id`, `seat_row`, `seat_number`, `seat_type`, `status`, `created_at`, `updated_at`) VALUES
(637, 1, 'A', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:37:34'),
(638, 1, 'A', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(639, 1, 'A', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(640, 1, 'A', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(641, 1, 'A', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(642, 1, 'A', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(643, 1, 'A', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(644, 1, 'A', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(645, 1, 'A', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(646, 1, 'A', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(647, 1, 'A', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(648, 1, 'A', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(649, 1, 'B', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(650, 1, 'B', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(651, 1, 'B', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(652, 1, 'B', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(653, 1, 'B', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(654, 1, 'B', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(655, 1, 'B', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(656, 1, 'B', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(657, 1, 'B', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(658, 1, 'B', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(659, 1, 'B', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(660, 1, 'B', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(661, 1, 'C', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(662, 1, 'C', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(663, 1, 'C', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(664, 1, 'C', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(665, 1, 'C', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(666, 1, 'C', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(667, 1, 'C', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(668, 1, 'C', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(669, 1, 'C', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(670, 1, 'C', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(671, 1, 'C', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(672, 1, 'C', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(673, 1, 'D', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(674, 1, 'D', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(675, 1, 'D', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(676, 1, 'D', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(677, 1, 'D', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(678, 1, 'D', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(679, 1, 'D', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(680, 1, 'D', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(681, 1, 'D', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(682, 1, 'D', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(683, 1, 'D', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(684, 1, 'D', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(685, 1, 'E', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(686, 1, 'E', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(687, 1, 'E', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(688, 1, 'E', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(689, 1, 'E', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(690, 1, 'E', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(691, 1, 'E', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(692, 1, 'E', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(693, 1, 'E', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(694, 1, 'E', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(695, 1, 'E', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(696, 1, 'E', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(697, 1, 'F', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(698, 1, 'F', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(699, 1, 'F', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(700, 1, 'F', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(701, 1, 'F', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(702, 1, 'F', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(703, 1, 'F', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(704, 1, 'F', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(705, 1, 'F', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(706, 1, 'F', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(707, 1, 'F', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(708, 1, 'F', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(709, 1, 'G', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(710, 1, 'G', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(711, 1, 'G', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(712, 1, 'G', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(713, 1, 'G', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(714, 1, 'G', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(715, 1, 'G', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(716, 1, 'G', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(717, 1, 'G', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(718, 1, 'G', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(719, 1, 'G', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(720, 1, 'G', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(721, 1, 'H', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(722, 1, 'H', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(723, 1, 'H', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(724, 1, 'H', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(725, 1, 'H', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(726, 1, 'H', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(727, 1, 'H', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(728, 1, 'H', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(729, 1, 'H', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(730, 1, 'H', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(731, 1, 'H', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(732, 1, 'H', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(733, 1, 'I', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(734, 1, 'I', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(735, 1, 'I', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(736, 1, 'I', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(737, 2, 'A', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(738, 2, 'A', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(739, 2, 'A', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(740, 2, 'A', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:38:32'),
(741, 2, 'A', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:38:32'),
(742, 2, 'A', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:38:32'),
(743, 2, 'A', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(744, 2, 'A', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(745, 2, 'A', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(746, 2, 'A', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(747, 2, 'A', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(748, 2, 'A', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(749, 2, 'B', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(750, 2, 'B', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(751, 2, 'B', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(752, 2, 'B', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(753, 2, 'B', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(754, 2, 'B', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(755, 2, 'B', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(756, 2, 'B', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(757, 2, 'B', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(758, 2, 'B', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(759, 2, 'B', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(760, 2, 'B', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(761, 2, 'C', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(762, 2, 'C', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(763, 2, 'C', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:04:32'),
(764, 2, 'C', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(765, 2, 'C', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(766, 2, 'C', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(767, 2, 'C', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(768, 2, 'C', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(769, 2, 'C', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(770, 2, 'C', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(771, 2, 'C', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(772, 2, 'C', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(773, 2, 'D', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(774, 2, 'D', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(775, 2, 'D', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(776, 2, 'D', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(777, 2, 'D', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(778, 2, 'D', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(779, 2, 'D', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(780, 2, 'D', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(781, 2, 'D', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(782, 2, 'D', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 19:49:15'),
(783, 2, 'D', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(784, 2, 'D', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(785, 2, 'E', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(786, 2, 'E', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(787, 2, 'E', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(788, 2, 'E', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(789, 2, 'E', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(790, 2, 'E', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(791, 2, 'E', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(792, 2, 'E', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(793, 2, 'E', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(794, 2, 'E', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(795, 2, 'E', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(796, 2, 'E', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(797, 2, 'F', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(798, 2, 'F', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(799, 2, 'F', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(800, 2, 'F', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(801, 2, 'F', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(802, 2, 'F', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(803, 2, 'F', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(804, 2, 'F', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(805, 2, 'F', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(806, 2, 'F', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(807, 2, 'F', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 18:18:11'),
(808, 2, 'F', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(809, 2, 'G', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(810, 2, 'G', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(811, 2, 'G', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(812, 2, 'G', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(813, 2, 'G', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(814, 2, 'G', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(815, 2, 'G', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(816, 2, 'G', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(817, 2, 'G', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(818, 2, 'G', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(819, 2, 'G', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(820, 2, 'G', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(821, 2, 'H', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(822, 2, 'H', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(823, 2, 'H', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(824, 2, 'H', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(825, 2, 'H', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(826, 2, 'H', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(827, 2, 'H', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(828, 2, 'H', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(829, 2, 'H', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(830, 2, 'H', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(831, 2, 'H', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(832, 2, 'H', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(833, 2, 'I', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(834, 2, 'I', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(835, 2, 'I', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(836, 2, 'I', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(837, 2, 'I', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(838, 2, 'I', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(839, 2, 'I', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(840, 2, 'I', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(841, 2, 'I', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(842, 2, 'I', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(843, 2, 'I', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(844, 2, 'I', 12, 'normal', 'booked', '2024-12-19 06:32:00', '2024-12-25 02:50:12'),
(845, 2, 'J', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(846, 2, 'J', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(847, 2, 'J', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(848, 2, 'J', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(849, 2, 'J', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(850, 2, 'J', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(851, 2, 'J', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(852, 2, 'J', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(853, 2, 'J', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(854, 2, 'J', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(855, 2, 'J', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(856, 2, 'J', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(857, 3, 'A', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(858, 3, 'A', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(859, 3, 'A', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(860, 3, 'A', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(861, 3, 'A', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(862, 3, 'A', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(863, 3, 'A', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(864, 3, 'A', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(865, 3, 'A', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(866, 3, 'A', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(867, 3, 'A', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(868, 3, 'A', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(869, 3, 'B', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(870, 3, 'B', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(871, 3, 'B', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(872, 3, 'B', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(873, 3, 'B', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(874, 3, 'B', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(875, 3, 'B', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(876, 3, 'B', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(877, 3, 'B', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(878, 3, 'B', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(879, 3, 'B', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(880, 3, 'B', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(881, 3, 'C', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(882, 3, 'C', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(883, 3, 'C', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(884, 3, 'C', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(885, 3, 'C', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(886, 3, 'C', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(887, 3, 'C', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(888, 3, 'C', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(889, 3, 'C', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(890, 3, 'C', 10, 'normal', 'booked', '2024-12-19 06:32:00', '2024-12-21 21:36:23'),
(891, 3, 'C', 11, 'normal', 'booked', '2024-12-19 06:32:00', '2024-12-21 21:37:47'),
(892, 3, 'C', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(893, 3, 'D', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(894, 3, 'D', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(895, 3, 'D', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(896, 3, 'D', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(897, 3, 'D', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(898, 3, 'D', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(899, 3, 'D', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(900, 3, 'D', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(901, 3, 'D', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(902, 3, 'D', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(903, 3, 'D', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(904, 3, 'D', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(905, 3, 'E', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(906, 3, 'E', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(907, 3, 'E', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(908, 3, 'E', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(909, 3, 'E', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(910, 3, 'E', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(911, 3, 'E', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(912, 3, 'E', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(913, 3, 'E', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(914, 3, 'E', 10, 'normal', 'booked', '2024-12-19 06:32:00', '2024-12-25 02:56:54'),
(915, 3, 'E', 11, 'normal', 'booked', '2024-12-19 06:32:00', '2024-12-25 02:56:54'),
(916, 3, 'E', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(917, 3, 'F', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(918, 3, 'F', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(919, 3, 'F', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(920, 3, 'F', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(921, 3, 'F', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(922, 3, 'F', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(923, 3, 'F', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(924, 3, 'F', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(925, 3, 'F', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(926, 3, 'F', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(927, 3, 'F', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(928, 3, 'F', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(929, 3, 'G', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(930, 3, 'G', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(931, 3, 'G', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(932, 3, 'G', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(933, 3, 'G', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(934, 3, 'G', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(935, 3, 'G', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(936, 3, 'G', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(937, 4, 'A', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(938, 4, 'A', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(939, 4, 'A', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(940, 4, 'A', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(941, 4, 'A', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(942, 4, 'A', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(943, 4, 'A', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(944, 4, 'A', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(945, 4, 'A', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(946, 4, 'A', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(947, 4, 'A', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(948, 4, 'A', 12, 'normal', 'booked', '2024-12-19 06:32:00', '2024-12-24 09:57:53'),
(949, 4, 'B', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(950, 4, 'B', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(951, 4, 'B', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(952, 4, 'B', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(953, 4, 'B', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(954, 4, 'B', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(955, 4, 'B', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(956, 4, 'B', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(957, 4, 'B', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(958, 4, 'B', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(959, 4, 'B', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(960, 4, 'B', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(961, 4, 'C', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(962, 4, 'C', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(963, 4, 'C', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(964, 4, 'C', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(965, 4, 'C', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(966, 4, 'C', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(967, 4, 'C', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(968, 4, 'C', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(969, 4, 'C', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(970, 4, 'C', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(971, 4, 'C', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(972, 4, 'C', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(973, 4, 'D', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(974, 4, 'D', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(975, 4, 'D', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(976, 4, 'D', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(977, 4, 'D', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(978, 4, 'D', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(979, 4, 'D', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(980, 4, 'D', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(981, 4, 'D', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(982, 4, 'D', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(983, 4, 'D', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(984, 4, 'D', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(985, 4, 'E', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(986, 4, 'E', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(987, 4, 'E', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(988, 4, 'E', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(989, 4, 'E', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(990, 4, 'E', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(991, 4, 'E', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(992, 4, 'E', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(993, 4, 'E', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(994, 4, 'E', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(995, 4, 'E', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(996, 4, 'E', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(997, 4, 'F', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(998, 4, 'F', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(999, 4, 'F', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1000, 4, 'F', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1001, 4, 'F', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1002, 4, 'F', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1003, 4, 'F', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1004, 4, 'F', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1005, 4, 'F', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1006, 4, 'F', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1007, 4, 'F', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1008, 4, 'F', 12, 'normal', 'booked', '2024-12-19 06:32:00', '2024-12-23 22:20:24'),
(1009, 4, 'G', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1010, 4, 'G', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1011, 4, 'G', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1012, 4, 'G', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1013, 4, 'G', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1014, 4, 'G', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1015, 4, 'G', 7, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(1016, 4, 'G', 8, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-21 13:26:19'),
(1017, 4, 'G', 9, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1018, 4, 'G', 10, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1019, 4, 'G', 11, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1020, 4, 'G', 12, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1021, 4, 'H', 1, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1022, 4, 'H', 2, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1023, 4, 'H', 3, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1024, 4, 'H', 4, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1025, 4, 'H', 5, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00'),
(1026, 4, 'H', 6, 'normal', 'available', '2024-12-19 06:32:00', '2024-12-19 06:32:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theaters`
--

CREATE TABLE `theaters` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `total_seats` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `theaters`
--

INSERT INTO `theaters` (`id`, `name`, `address`, `phone`, `total_seats`, `created_at`, `updated_at`) VALUES
(1, 'CGV Vincom', '54 Nguyễn Chí Thanh, Hà Nội', '0123456787', 500, '2024-12-19 06:25:42', '2024-12-24 18:42:15'),
(2, 'Galaxy Cinema', '123 Láng Hạ, Hà Nội', '0987654321', 400, '2024-12-19 06:25:42', '2024-12-19 06:25:42'),
(3, 'CGV CT Plaza', 'CT Plaza, 60A Trường Sơn, P.2, Q. Tân Bình, Tp. Hồ Chí Minh', '0123456787', 200, '2024-12-24 21:46:45', '2024-12-24 22:49:40'),
(6, 'Lotte Thủ Đức', 'Tầng 2, TTTM Joy Citipoint, KCX Linh Trung 1, Tp. Hồ Chí Minh', '0989876756', 350, '2024-12-24 21:47:37', '2024-12-24 21:50:35'),
(7, 'Galaxy Nguyễn Du ', '116 Nguyễn Du, Q.1, Tp. Hồ Chí Minh', '6536364635', 400, '2024-12-24 21:50:09', '2024-12-24 21:50:09'),
(8, 'Mega GS Cao Thắng', 'Lầu 6 - 7, 19 Cao Thắng, P.2, Q.3, Tp. Hồ Chí Minh', '4322424248', 400, '2024-12-24 22:34:40', '2024-12-24 22:34:40');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status` enum('active','blocked') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `fullname`, `phone`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2a$12$Z8o2xZyUV1mf9oZ4q7CBXOYzQIqdtZwI6HVGNLuZgga0SrNUjJhlS', 'admin@example.com', 'Administrator', '0123456789', 'admin', 'active', '2024-12-19 06:25:42', '2024-12-20 11:27:05'),
(2, 'Linh', '$2a$12$Z8o2xZyUV1mf9oZ4q7CBXOYzQIqdtZwI6HVGNLuZgga0SrNUjJhlS', 'john@example.com', 'Vu Linh', '0987654321', 'admin', 'active', '2024-12-19 06:25:42', '2024-12-21 21:39:24'),
(3, 'Tuan', '$2y$10$xeVRJJFLbrWJ/Y9VlClEcOlyjCD5Td/5KJiI7x2pcl1jJH65/NLvi', 'tuannhat124@gmail.com', 'Nhat Tuan', '0987654321', 'user', 'active', '2024-12-21 21:37:24', '2024-12-21 21:37:24'),
(4, 'Thang', '$2y$10$C/M2ts5fR8mVdyIFu4KqSenUu1TtKRGgAhiTptMeHsGp.wR7HcgyW', 'hungthang4497@gmail.com', 'Hung Thang', '0985674321', 'user', 'active', '2024-12-24 21:44:03', '2024-12-24 21:44:03'),
(5, 'Nhat', '$2y$10$Wyq7KZUXQ5Gq7iwfUIJtbu4pQciBRop8.HgGszNvKa988ceg2RS/2', 'tunanguyen421@gmail.com', 'Tuna Nhat', '0432765892', 'user', 'active', '2024-12-24 21:45:05', '2024-12-25 02:52:33');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `schedule_id` (`schedule_id`),
  ADD KEY `idx_booking_status` (`status`);

--
-- Chỉ mục cho bảng `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_movie_status` (`status`);

--
-- Chỉ mục cho bảng `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD PRIMARY KEY (`movie_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Chỉ mục cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Chỉ mục cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `idx_schedule_date` (`show_date`);

--
-- Chỉ mục cho bảng `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Chỉ mục cho bảng `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT cho bảng `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `genres`
--
ALTER TABLE `genres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1027;

--
-- AUTO_INCREMENT cho bảng `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `banners`
--
ALTER TABLE `banners`
  ADD CONSTRAINT `banners_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_seats_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD CONSTRAINT `movie_genres_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_genres_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
