-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th12 24, 2025 lúc 11:27 PM
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
-- Cơ sở dữ liệu: `iot_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `automation_logs`
--

CREATE TABLE `automation_logs` (
  `id` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `rule_name` varchar(100) DEFAULT NULL,
  `trigger_type` varchar(50) DEFAULT NULL,
  `trigger_value_actual` varchar(50) DEFAULT NULL COMMENT 'Giá trị thực tế khi trigger',
  `action_executed` varchar(100) DEFAULT NULL,
  `status` enum('success','failed') DEFAULT 'success',
  `error_message` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `log_type` varchar(20) DEFAULT 'triggered' COMMENT 'triggered hoặc reverted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `automation_logs`
--

INSERT INTO `automation_logs` (`id`, `rule_id`, `rule_name`, `trigger_type`, `trigger_value_actual`, `action_executed`, `status`, `error_message`, `created_at`, `log_type`) VALUES
(4, 17, 'bật đèn khi có người', 'motion', 'none', 'light1_on', 'success', NULL, '2025-12-24 21:38:35', 'reverted'),
(5, 17, 'bật đèn khi có người', 'motion', 'none', 'light1_on', 'success', NULL, '2025-12-24 21:38:40', 'reverted'),
(6, 17, 'bật đèn khi có người', 'motion', 'detected', 'light1_on', 'success', NULL, '2025-12-24 21:38:47', 'triggered'),
(7, 17, 'bật đèn khi có người', 'motion', 'detected', 'light1_on', 'success', NULL, '2025-12-24 22:04:18', 'triggered'),
(8, 18, 'bật đèn khi có người', 'motion', 'detected', 'light2_on', 'success', NULL, '2025-12-24 22:04:18', 'triggered');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `automation_rules`
--

CREATE TABLE `automation_rules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Tên rule',
  `description` varchar(255) DEFAULT NULL COMMENT 'Mô tả',
  `trigger_type` enum('time','temperature','humidity','motion','light','fire') NOT NULL,
  `trigger_operator` enum('=','>','<','>=','<=','between') DEFAULT '=',
  `trigger_value` varchar(50) NOT NULL COMMENT 'Giá trị so sánh',
  `trigger_value2` varchar(50) DEFAULT NULL COMMENT 'Giá trị thứ 2 (cho between)',
  `action_type` enum('light1','light2','light3','light4','door','buzzer','all_lights') NOT NULL,
  `action_value` enum('on','off','toggle') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1=Bật, 0=Tắt',
  `last_triggered` datetime DEFAULT NULL COMMENT 'Lần cuối kích hoạt',
  `trigger_count` int(11) DEFAULT 0 COMMENT 'Số lần đã kích hoạt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `auto_revert` tinyint(1) DEFAULT 0 COMMENT 'Tự động thực hiện hành động ngược lại khi điều kiện không còn đúng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `automation_rules`
--

INSERT INTO `automation_rules` (`id`, `name`, `description`, `trigger_type`, `trigger_operator`, `trigger_value`, `trigger_value2`, `action_type`, `action_value`, `is_active`, `last_triggered`, `trigger_count`, `created_at`, `updated_at`, `auto_revert`) VALUES
(17, 'bật đèn khi có người', '', 'motion', '=', 'detected', NULL, 'light1', 'on', 1, '2025-12-25 05:04:18', 2, '2025-12-24 21:38:26', '2025-12-24 22:04:18', 1),
(18, 'bật đèn khi có người', '', 'motion', '=', 'detected', NULL, 'light2', 'on', 1, '2025-12-25 05:04:18', 1, '2025-12-24 21:53:58', '2025-12-24 22:04:18', 1),
(19, 'ngủ đi', '', 'temperature', '>', '30', NULL, 'light3', 'on', 1, NULL, 0, '2025-12-24 21:58:07', '2025-12-24 21:58:07', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `device_log`
--

CREATE TABLE `device_log` (
  `id` int(11) NOT NULL,
  `device` varchar(50) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dht_logs`
--

CREATE TABLE `dht_logs` (
  `id` int(11) NOT NULL,
  `temperature` float NOT NULL,
  `humidity` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dht_logs`
--

INSERT INTO `dht_logs` (`id`, `temperature`, `humidity`, `created_at`) VALUES
(694, 29.8, 75.9, '2025-12-19 13:55:45'),
(695, 29.8, 75.9, '2025-12-19 13:56:03'),
(696, 29.9, 75.8, '2025-12-19 14:14:03'),
(697, 29.8, 76.7, '2025-12-19 14:16:18'),
(698, 29.8, 76.2, '2025-12-19 14:16:33'),
(699, 29.8, 76.1, '2025-12-19 14:16:43'),
(700, 29.9, 75.9, '2025-12-19 14:16:53'),
(701, 29.8, 75.9, '2025-12-19 14:17:08'),
(702, 29.8, 75.8, '2025-12-19 14:17:18'),
(703, 29.7, 76.3, '2025-12-19 14:19:16'),
(704, 29.7, 76.3, '2025-12-19 14:19:29'),
(705, 29.7, 76.3, '2025-12-19 14:19:39'),
(706, 29.7, 76.4, '2025-12-19 14:19:49'),
(707, 29.7, 76.2, '2025-12-19 14:20:04'),
(708, 29.7, 76.2, '2025-12-19 14:20:14'),
(709, 29.7, 76.1, '2025-12-19 14:20:24'),
(710, 29.7, 76.1, '2025-12-19 14:20:34'),
(711, 29.7, 76.1, '2025-12-19 14:20:44'),
(712, 29.7, 76.2, '2025-12-19 14:20:59'),
(713, 29.7, 76.3, '2025-12-19 14:21:09'),
(714, 29.7, 76.3, '2025-12-19 14:21:19'),
(715, 29.7, 76.3, '2025-12-19 14:21:31'),
(716, 29.7, 76.2, '2025-12-19 14:21:48'),
(717, 29.7, 76.1, '2025-12-19 14:21:59'),
(718, 29.7, 76, '2025-12-19 14:22:14'),
(719, 29.7, 75.9, '2025-12-19 14:22:24'),
(720, 29.7, 75.9, '2025-12-19 14:22:39'),
(721, 29.7, 76, '2025-12-19 14:22:49'),
(722, 29.7, 76, '2025-12-19 14:22:59'),
(723, 29.7, 76.1, '2025-12-19 14:23:14'),
(724, 29.7, 76.1, '2025-12-19 14:23:24'),
(725, 29.7, 76.1, '2025-12-19 14:23:34'),
(726, 29.7, 76.2, '2025-12-19 14:23:44'),
(727, 29.7, 76.2, '2025-12-19 14:23:59'),
(728, 29.6, 76.1, '2025-12-19 14:24:09'),
(729, 29.7, 76.2, '2025-12-19 14:24:19'),
(730, 29.7, 76.2, '2025-12-19 14:24:29'),
(731, 29.7, 76.2, '2025-12-19 14:24:46'),
(732, 29.7, 76.2, '2025-12-19 14:25:05'),
(733, 29.7, 76.2, '2025-12-19 14:25:45'),
(734, 29.7, 76.3, '2025-12-19 14:25:59'),
(735, 29.6, 76.3, '2025-12-19 14:26:09'),
(736, 29.6, 76.3, '2025-12-19 14:26:24'),
(737, 30.1, 75.8, '2025-12-21 11:57:03'),
(738, 30.2, 75.7, '2025-12-21 11:57:20'),
(739, 30.7, 82.8, '2025-12-21 12:02:10'),
(740, 30.8, 79.4, '2025-12-21 12:02:20'),
(741, 30.8, 75.3, '2025-12-21 12:02:35'),
(742, 30.8, 74.4, '2025-12-21 12:02:45'),
(743, 30.7, 74, '2025-12-21 12:02:55'),
(744, 30.8, 78.4, '2025-12-21 12:05:12'),
(745, 30.9, 77.6, '2025-12-21 12:05:22'),
(746, 30.9, 74.2, '2025-12-21 12:05:37'),
(747, 30.9, 74, '2025-12-21 12:05:52'),
(748, 30.8, 73.7, '2025-12-21 12:06:02'),
(749, 30.8, 73.8, '2025-12-21 12:06:17'),
(750, 30.8, 73.9, '2025-12-21 12:06:27'),
(751, 30.8, 74, '2025-12-21 12:06:37'),
(752, 30.7, 74.1, '2025-12-21 12:06:47'),
(753, 30.7, 74.1, '2025-12-21 12:06:57'),
(754, 30.7, 74.1, '2025-12-21 12:07:12'),
(755, 30.7, 74.2, '2025-12-21 12:07:22'),
(756, 30.6, 74.2, '2025-12-21 12:07:37'),
(757, 30.6, 74.4, '2025-12-21 12:07:47'),
(758, 30.6, 74.3, '2025-12-21 12:08:02'),
(759, 30.5, 74.4, '2025-12-21 12:08:12'),
(760, 30.5, 74.5, '2025-12-21 12:08:22'),
(761, 30.5, 74.5, '2025-12-21 12:08:32'),
(762, 30.5, 74.4, '2025-12-21 12:08:47'),
(763, 30.5, 74.4, '2025-12-21 12:09:02'),
(764, 30.5, 74.6, '2025-12-21 12:09:12'),
(765, 30.4, 74.5, '2025-12-21 12:09:27'),
(766, 30.4, 74.6, '2025-12-21 12:09:37'),
(767, 30.4, 74.6, '2025-12-21 12:09:52'),
(768, 30.4, 74.8, '2025-12-21 12:10:02'),
(769, 30.4, 74.8, '2025-12-21 12:10:17'),
(770, 30.4, 74.7, '2025-12-21 12:10:32'),
(771, 30.4, 74.6, '2025-12-21 12:10:42'),
(772, 30.4, 74.7, '2025-12-21 12:10:57'),
(773, 30.4, 74.7, '2025-12-21 12:11:07'),
(774, 30.4, 74.8, '2025-12-21 12:11:17'),
(775, 30.4, 74.8, '2025-12-21 12:11:27'),
(776, 30.4, 74.9, '2025-12-21 12:11:42'),
(777, 30.4, 74.9, '2025-12-21 12:11:57'),
(778, 30.4, 75, '2025-12-21 12:12:12'),
(779, 30.3, 75, '2025-12-21 12:12:27'),
(780, 30.4, 74.9, '2025-12-21 12:12:37'),
(781, 30.3, 74.9, '2025-12-21 12:12:47'),
(782, 30.3, 75, '2025-12-21 12:12:57'),
(783, 30.3, 75, '2025-12-21 12:13:12'),
(784, 30.3, 74.9, '2025-12-21 12:13:22'),
(785, 30.3, 75.1, '2025-12-21 12:13:32'),
(786, 30.3, 75.2, '2025-12-21 12:13:47'),
(787, 30.4, 75.2, '2025-12-21 12:13:57'),
(788, 30.3, 75.1, '2025-12-21 12:14:07'),
(789, 30.4, 75, '2025-12-21 12:14:17'),
(790, 30.3, 75, '2025-12-21 12:14:27'),
(791, 30.3, 75, '2025-12-21 12:14:42'),
(792, 30.1, 75.8, '2025-12-21 12:17:31'),
(793, 30.2, 75.9, '2025-12-21 12:17:38'),
(794, 30.1, 75.9, '2025-12-21 12:18:37'),
(795, 30.1, 76, '2025-12-21 12:18:46'),
(796, 30.2, 75.9, '2025-12-21 12:19:01'),
(797, 30.2, 75.6, '2025-12-21 12:19:41'),
(798, 30.2, 75.6, '2025-12-21 12:19:51'),
(799, 30.2, 75.5, '2025-12-21 12:20:01'),
(800, 30.2, 75.4, '2025-12-21 12:20:11'),
(801, 30.2, 75.4, '2025-12-21 12:20:26'),
(802, 30.4, 77.1, '2025-12-21 12:20:41'),
(803, 30.4, 75.8, '2025-12-21 12:20:51'),
(804, 30.5, 75.1, '2025-12-21 12:21:06'),
(805, 30.4, 74.8, '2025-12-21 12:21:16'),
(806, 30.4, 74.8, '2025-12-21 12:21:26'),
(807, 30.4, 74.8, '2025-12-21 12:21:41'),
(808, 30.4, 74.8, '2025-12-21 12:21:51'),
(809, 30.3, 74.9, '2025-12-21 12:22:01'),
(810, 30.3, 74.9, '2025-12-21 12:22:16'),
(811, 30.3, 75.1, '2025-12-21 12:22:31'),
(812, 30.3, 75, '2025-12-21 12:22:41'),
(813, 30.3, 75.1, '2025-12-21 12:22:51'),
(814, 30.3, 75.2, '2025-12-21 12:23:01'),
(815, 30.3, 75.2, '2025-12-21 12:23:16'),
(816, 30.3, 75.1, '2025-12-21 12:23:26'),
(817, 30.3, 75.2, '2025-12-21 12:23:36'),
(818, 30.1, 75.6, '2025-12-21 12:25:56'),
(819, 30.2, 75.6, '2025-12-21 12:26:08'),
(820, 30.2, 75.5, '2025-12-21 12:26:18'),
(821, 30.2, 75.5, '2025-12-21 12:26:33'),
(822, 30.2, 75.5, '2025-12-21 12:26:43'),
(823, 30.2, 75.5, '2025-12-21 12:26:53'),
(824, 30.2, 75.4, '2025-12-21 12:27:08'),
(825, 30.3, 75.4, '2025-12-21 12:27:18'),
(826, 30.2, 75.3, '2025-12-21 12:27:28'),
(827, 30.3, 75.3, '2025-12-21 12:27:38'),
(828, 30.3, 75.2, '2025-12-21 12:27:53'),
(829, 30.3, 75.2, '2025-12-21 12:28:08'),
(830, 30.3, 75.1, '2025-12-21 12:28:18'),
(831, 30.3, 75, '2025-12-21 12:28:28'),
(832, 30.3, 74.9, '2025-12-21 12:28:43'),
(833, 30.3, 75, '2025-12-21 12:28:53'),
(834, 30.3, 75.2, '2025-12-21 12:29:04'),
(835, 30.3, 75, '2025-12-21 12:29:18'),
(836, 30.3, 74.9, '2025-12-21 12:29:33'),
(837, 30.3, 74.9, '2025-12-21 12:29:43'),
(838, 30.2, 74.9, '2025-12-21 12:29:53'),
(839, 30.2, 74.9, '2025-12-21 12:30:04'),
(840, 30.2, 75.1, '2025-12-21 12:30:18'),
(841, 30.2, 75.1, '2025-12-21 12:30:28'),
(842, 30.2, 75.2, '2025-12-21 12:30:38'),
(843, 30.2, 75.5, '2025-12-21 12:30:53'),
(844, 30.2, 75.5, '2025-12-21 12:31:03'),
(845, 30.2, 75.5, '2025-12-21 12:31:13'),
(846, 30.3, 75.3, '2025-12-21 12:31:24'),
(847, 30.3, 75.2, '2025-12-21 12:31:38'),
(848, 30.3, 75.1, '2025-12-21 12:31:48'),
(849, 30.3, 75.1, '2025-12-21 12:31:59'),
(850, 30.3, 75.1, '2025-12-21 12:32:09'),
(851, 30.3, 74.9, '2025-12-21 12:32:19'),
(852, 30.4, 74.9, '2025-12-21 12:32:34'),
(853, 30.4, 74.8, '2025-12-21 12:32:49'),
(854, 30.4, 74.9, '2025-12-21 12:33:04'),
(855, 30.4, 74.8, '2025-12-21 12:33:18'),
(856, 30.5, 74.8, '2025-12-21 12:33:29'),
(857, 30.5, 74.7, '2025-12-21 12:33:39'),
(858, 30.5, 74.6, '2025-12-21 12:33:54'),
(859, 30.5, 74.5, '2025-12-21 12:34:09'),
(860, 30.5, 74.3, '2025-12-21 12:34:19'),
(861, 30.5, 74.2, '2025-12-21 12:34:34'),
(862, 30.5, 74.2, '2025-12-21 12:34:44'),
(863, 30.5, 74.2, '2025-12-21 12:34:54'),
(864, 30.5, 74.1, '2025-12-21 12:35:04'),
(865, 30.5, 74.2, '2025-12-21 12:35:14'),
(866, 30.5, 74.4, '2025-12-21 12:35:29'),
(867, 30.5, 74.6, '2025-12-21 12:35:39'),
(868, 30.6, 74.6, '2025-12-21 12:35:54'),
(869, 30.6, 74.5, '2025-12-21 12:36:04'),
(870, 30.6, 74.4, '2025-12-21 12:36:14'),
(871, 30.6, 74.3, '2025-12-21 12:36:24'),
(872, 30.7, 74.1, '2025-12-21 12:36:39'),
(873, 30.7, 74.1, '2025-12-21 12:36:49'),
(874, 30.7, 74.1, '2025-12-21 12:37:04'),
(875, 30.7, 74.2, '2025-12-21 12:37:14'),
(876, 30.7, 74.2, '2025-12-21 12:37:29'),
(877, 30.8, 74.1, '2025-12-21 12:37:39'),
(878, 30.8, 74.1, '2025-12-21 12:37:54'),
(879, 30.8, 74, '2025-12-21 12:38:04'),
(880, 30.8, 73.9, '2025-12-21 12:38:19'),
(881, 30.8, 74, '2025-12-21 12:38:29'),
(882, 30.7, 73.6, '2025-12-21 12:38:39'),
(883, 30.7, 73.5, '2025-12-21 12:38:54'),
(884, 30.7, 73.9, '2025-12-21 12:39:09'),
(885, 30.6, 74, '2025-12-21 12:39:19'),
(886, 30.6, 74.1, '2025-12-21 12:39:29'),
(887, 30.6, 74.4, '2025-12-21 12:39:44'),
(888, 30.6, 74.3, '2025-12-21 12:39:59'),
(889, 30.6, 74.2, '2025-12-21 12:40:09'),
(890, 30.6, 74.4, '2025-12-21 12:40:24'),
(891, 30.6, 74.5, '2025-12-21 12:40:34'),
(892, 30.5, 74.3, '2025-12-21 12:40:44'),
(893, 30.6, 74, '2025-12-21 12:40:59'),
(894, 30.6, 74.1, '2025-12-21 12:41:14'),
(895, 30.5, 74, '2025-12-21 12:41:29'),
(896, 30.6, 74.1, '2025-12-21 12:41:39'),
(897, 30.6, 74.1, '2025-12-21 12:41:49'),
(898, 30.5, 74.2, '2025-12-21 12:41:59'),
(899, 30.5, 74.2, '2025-12-21 12:42:14'),
(900, 30.5, 74.1, '2025-12-21 12:42:24'),
(901, 30.6, 74.1, '2025-12-21 12:42:39'),
(902, 30.5, 73.9, '2025-12-21 12:42:49'),
(903, 30.5, 73.7, '2025-12-21 12:42:59'),
(904, 30.5, 73.6, '2025-12-21 12:43:14'),
(905, 30.5, 73.7, '2025-12-21 12:43:24'),
(906, 30.4, 73.7, '2025-12-21 12:43:39'),
(907, 30.4, 73.8, '2025-12-21 12:43:49'),
(908, 30.4, 73.8, '2025-12-21 12:43:59'),
(909, 30.4, 73.8, '2025-12-21 12:44:14'),
(910, 30.4, 73.9, '2025-12-21 12:44:24'),
(911, 30.3, 73.9, '2025-12-21 12:44:34'),
(912, 30.3, 73.9, '2025-12-21 12:44:49'),
(913, 30.3, 73.9, '2025-12-21 12:44:59'),
(914, 30.3, 74, '2025-12-21 12:45:09'),
(915, 30.3, 74, '2025-12-21 12:45:19'),
(916, 30.2, 74.1, '2025-12-21 12:45:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `fingerprints`
--

CREATE TABLE `fingerprints` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `fingerprints`
--

INSERT INTO `fingerprints` (`id`, `name`, `created_at`) VALUES
(1, 'nguyen', '2025-12-14 10:41:35'),
(2, 'Thu', '2025-12-14 11:13:00'),
(3, 'thao', '2025-12-14 13:06:12'),
(4, 'nguyen hoang thi thu nguyen', '2025-12-20 09:41:37'),
(14, 'dang', '2025-12-15 10:18:40'),
(20, 'khoa', '2025-12-15 10:14:51'),
(22, 'loan', '2025-12-24 15:56:31'),
(45, 'dung', '2025-12-15 10:14:13'),
(48, 'dan', '2025-12-24 15:56:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `fingerprint_logs`
--

CREATE TABLE `fingerprint_logs` (
  `log_id` int(11) NOT NULL,
  `finger_id` int(11) NOT NULL,
  `finger_name` varchar(100) DEFAULT NULL,
  `event` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mqtt_inbox`
--

CREATE TABLE `mqtt_inbox` (
  `id` int(11) NOT NULL,
  `topic` varchar(100) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `rules`
--

CREATE TABLE `rules` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT 1,
  `rule_type` enum('condition','timer') NOT NULL,
  `json_logic` text DEFAULT NULL,
  `action_topic` varchar(100) DEFAULT NULL,
  `action_payload` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sensor_data`
--

CREATE TABLE `sensor_data` (
  `id` int(11) NOT NULL,
  `sensor` varchar(50) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `source` varchar(20) DEFAULT NULL,
  `topic` varchar(100) DEFAULT NULL,
  `level` varchar(10) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `system_logs`
--

INSERT INTO `system_logs` (`id`, `source`, `topic`, `level`, `message`, `created_at`) VALUES
(1, 'esp32', 'fingerprint/pir', 'INFO', 'motion', '2025-12-24 21:17:55'),
(2, 'esp32', 'fingerprint/cmd', 'INFO', 'light1_on', '2025-12-24 21:17:55'),
(3, 'esp32', 'fingerprint/pir', 'INFO', 'normal', '2025-12-24 21:18:53'),
(4, 'esp32', 'fingerprint/pir', 'INFO', 'motion', '2025-12-24 21:25:46'),
(5, 'esp32', 'fingerprint/cmd', 'INFO', 'light1_on', '2025-12-24 21:25:46'),
(6, 'esp32', 'fingerprint/pir', 'INFO', 'normal', '2025-12-24 21:25:55'),
(7, 'esp32', 'fingerprint/pir', 'INFO', 'motion', '2025-12-24 21:26:25'),
(8, 'esp32', 'fingerprint/cmd', 'INFO', 'light1_on', '2025-12-24 21:26:25'),
(9, 'esp32', 'fingerprint/ligh1/state', 'INFO', 'on', '2025-12-24 21:26:37'),
(10, 'esp32', 'fingerprint/state/llight1', 'INFO', 'on', '2025-12-24 21:27:09'),
(11, 'esp32', 'fingerprint/light1/state', 'INFO', 'on', '2025-12-24 21:27:57'),
(12, 'esp32', 'fingerprint/pir', 'INFO', 'normal', '2025-12-24 21:29:49'),
(13, 'esp32', 'fingerprint/pir', 'INFO', 'normal', '2025-12-24 21:38:35'),
(14, 'esp32', 'fingerprint/cmd', 'INFO', 'light1_off', '2025-12-24 21:38:35'),
(15, 'esp32', 'fingerprint/pir', 'INFO', 'pir', '2025-12-24 21:38:40'),
(16, 'esp32', 'fingerprint/cmd', 'INFO', 'light1_off', '2025-12-24 21:38:40'),
(17, 'esp32', 'fingerprint/pir', 'INFO', 'motion', '2025-12-24 21:38:47'),
(18, 'esp32', 'fingerprint/cmd', 'INFO', 'light1_on', '2025-12-24 21:38:48'),
(19, 'esp32', 'fingerprint/pir', 'INFO', 'motion', '2025-12-24 22:04:18'),
(20, 'esp32', 'fingerprint/cmd', 'INFO', 'light1_on', '2025-12-24 22:04:19'),
(21, 'esp32', 'fingerprint/cmd', 'INFO', 'light2_on', '2025-12-24 22:04:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `timer_rules`
--

CREATE TABLE `timer_rules` (
  `id` int(11) NOT NULL,
  `rule_id` int(11) DEFAULT NULL,
  `interval_seconds` int(11) DEFAULT 60,
  `last_run` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `automation_logs`
--
ALTER TABLE `automation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rule_id` (`rule_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_log_type` (`log_type`),
  ADD KEY `idx_rule_id_created` (`rule_id`,`created_at`);

--
-- Chỉ mục cho bảng `automation_rules`
--
ALTER TABLE `automation_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_trigger_type` (`trigger_type`);

--
-- Chỉ mục cho bảng `device_log`
--
ALTER TABLE `device_log`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `dht_logs`
--
ALTER TABLE `dht_logs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `fingerprints`
--
ALTER TABLE `fingerprints`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `fingerprint_logs`
--
ALTER TABLE `fingerprint_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Chỉ mục cho bảng `mqtt_inbox`
--
ALTER TABLE `mqtt_inbox`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `rules`
--
ALTER TABLE `rules`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sensor_data`
--
ALTER TABLE `sensor_data`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `timer_rules`
--
ALTER TABLE `timer_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rule_id` (`rule_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `automation_logs`
--
ALTER TABLE `automation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `automation_rules`
--
ALTER TABLE `automation_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `device_log`
--
ALTER TABLE `device_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `dht_logs`
--
ALTER TABLE `dht_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=917;

--
-- AUTO_INCREMENT cho bảng `fingerprint_logs`
--
ALTER TABLE `fingerprint_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `mqtt_inbox`
--
ALTER TABLE `mqtt_inbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `rules`
--
ALTER TABLE `rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `sensor_data`
--
ALTER TABLE `sensor_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `timer_rules`
--
ALTER TABLE `timer_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `automation_logs`
--
ALTER TABLE `automation_logs`
  ADD CONSTRAINT `automation_logs_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `automation_rules` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `timer_rules`
--
ALTER TABLE `timer_rules`
  ADD CONSTRAINT `timer_rules_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `rules` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
