-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 12, 2025 lúc 07:55 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
START TRANSACTION;
SET time_zone = '+00:00';


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlns`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bangcap`
--

CREATE TABLE `bangcap` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenbc` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bangcap`
--

INSERT INTO `bangcap` (`id`, `tenbc`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Tiến Sĩ', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 'Thạc Sĩ', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 'Cử Nhân', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 'Đại Học', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 'Cao Đẳng', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baohiem`
--

CREATE TABLE `baohiem` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `loaibaohiem_id` int(10) UNSIGNED NOT NULL,
  `maso` varchar(255) NOT NULL,
  `noicap` varchar(255) NOT NULL,
  `ngaycap` date NOT NULL,
  `ngayhethan` date NOT NULL,
  `mucdong` double(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chamcong`
--

CREATE TABLE `chamcong` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chamcong`
--

INSERT INTO `chamcong` (`id`, `nhanvien_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2025-05-09 17:00:00', '2025-05-10 06:04:11', NULL),
(2, 19, '2025-05-09 17:00:00', '2025-05-10 06:38:02', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chucvu`
--

CREATE TABLE `chucvu` (
  `id` int(10) UNSIGNED NOT NULL,
  `tencv` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chucvu`
--

INSERT INTO `chucvu` (`id`, `tencv`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Trưởng Phòng', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 'Trưởng Phòng', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 'Phó Phòng', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 'Marketing', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 'Nhân Viên', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuyenmon`
--

CREATE TABLE `chuyenmon` (
  `id` int(10) UNSIGNED NOT NULL,
  `tencm` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chuyenmon`
--

INSERT INTO `chuyenmon` (`id`, `tencm`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Programmer', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 'Tester', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 'Front-end', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 'Back-end', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 'Full-Stack', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dantoc`
--

CREATE TABLE `dantoc` (
  `id` int(10) UNSIGNED NOT NULL,
  `tendt` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dantoc`
--

INSERT INTO `dantoc` (`id`, `tendt`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Kinh', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 'Thái', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 'Mường', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 'Khmer', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 'Hoa', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(6, 'Nùng', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(7, 'H\'Mông', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `heso`
--

CREATE TABLE `heso` (
  `id` int(10) UNSIGNED NOT NULL,
  `luongcb` bigint(20) NOT NULL,
  `bac1` double(5,2) NOT NULL,
  `bac2` double(5,2) NOT NULL,
  `bac3` double(5,2) NOT NULL,
  `bac4` double(5,2) NOT NULL,
  `bac5` double(5,2) NOT NULL,
  `bac6` double(5,2) NOT NULL,
  `bac7` double(5,2) NOT NULL,
  `bac8` double(5,2) NOT NULL,
  `bac9` double(5,2) NOT NULL,
  `bac10` double(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `heso`
--

INSERT INTO `heso` (`id`, `luongcb`, `bac1`, `bac2`, `bac3`, `bac4`, `bac5`, `bac6`, `bac7`, `bac8`, `bac9`, `bac10`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1500000, 1.00, 1.20, 1.40, 1.60, 1.80, 1.90, 2.00, 2.20, 2.40, 2.60, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hopdong`
--

CREATE TABLE `hopdong` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `ngaybd` date NOT NULL,
  `ngaykt` date DEFAULT NULL,
  `loaihopdong` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hopdong`
--

INSERT INTO `hopdong` (`id`, `nhanvien_id`, `ngaybd`, `ngaykt`, `loaihopdong`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 20, '2025-05-10', '2025-05-17', 0, '2025-05-10 04:21:22', '2025-05-10 04:21:22', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khautru`
--

CREATE TABLE `khautru` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `loaibaohiem_id` int(10) UNSIGNED NOT NULL,
  `mucdong` double(5,2) NOT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaibaohiem`
--

CREATE TABLE `loaibaohiem` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenbh` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `loaibaohiem`
--

INSERT INTO `loaibaohiem` (`id`, `tenbh`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Bảo Hiểm Xã Hội', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 'Bảo Hiểm Y Tế', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 'Bảo Hiểm Tai Nạn', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 'Bảo Hiểm Thất Nghiệp', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2020_01_01_000001_create_password_resets_table', 1),
(2, '2020_01_01_000002_create_failed_jobs_table', 1),
(3, '2021_09_16_110111_create_sessions_table', 1),
(4, '2021_09_25_115514_create_chucvu_table', 1),
(5, '2021_09_25_115518_create_phongban_table', 1),
(6, '2021_09_25_115522_create_bangcap_table', 1),
(7, '2021_09_25_115526_create_chuyenmon_table', 1),
(8, '2021_09_25_125819_create_phucap_table', 1),
(9, '2021_09_25_135933_create_dantoc_table', 1),
(10, '2021_09_25_135933_create_tongiao_table', 1),
(11, '2021_09_25_135934_create_ngoaingu_table', 1),
(12, '2021_09_25_145217_create_nhanvien_table', 1),
(13, '2021_09_25_145218_create_users_table', 1),
(14, '2021_09_25_145248_create_thuongphat_table', 1),
(15, '2021_09_25_145259_create_chamcong_table', 1),
(16, '2021_09_25_154734_create_ungluong_table', 1),
(17, '2021_09_27_023923_create_hopdong_table', 1),
(18, '2021_10_03_040135_create_nghiviec_table', 1),
(19, '2021_10_07_122524_create_loaibaohiem_table', 1),
(20, '2021_10_07_122913_create_baohiem_table', 1),
(21, '2021_10_07_122932_create_nhanluong_table', 1),
(22, '2021_10_15_085345_create_khautru_table', 1),
(23, '2021_10_25_122809_create_heso_table', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nghiviec`
--

CREATE TABLE `nghiviec` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `ngaybd` date NOT NULL,
  `ngaykt` date NOT NULL,
  `lydo` varchar(255) NOT NULL,
  `huongluong` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nghiviec`
--

INSERT INTO `nghiviec` (`id`, `nhanvien_id`, `ngaybd`, `ngaykt`, `lydo`, `huongluong`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 17, '2025-03-11', '2025-05-11', 'di tre', 0, '2025-05-11 01:38:53', '2025-05-11 01:38:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ngoaingu`
--

CREATE TABLE `ngoaingu` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenng` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ngoaingu`
--

INSERT INTO `ngoaingu` (`id`, `tenng`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Sơ cấp - Bậc 1 (A1)', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 'Sơ cấp - Bậc 2 (A2)', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 'Trung cấp - Bậc 1 (B1)', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 'Trung cấp - Bậc 2 (B2)', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 'Cao cấp - Bậc 1 (C1)', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(6, 'Cao cấp - Bậc 2 (C2)', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanluong`
--

CREATE TABLE `nhanluong` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `heso` double(5,2) NOT NULL,
  `hsphucap` double(5,2) NOT NULL,
  `khautru` bigint(20) NOT NULL,
  `luongcb` bigint(20) NOT NULL,
  `mucluong` bigint(20) NOT NULL,
  `phucap` bigint(20) NOT NULL,
  `ngaycongchuan` int(11) NOT NULL,
  `ngaycong` int(11) NOT NULL,
  `nghihl` int(11) NOT NULL,
  `nghikhl` int(11) NOT NULL,
  `thuong` bigint(20) NOT NULL,
  `phat` bigint(20) NOT NULL,
  `tamung` bigint(20) NOT NULL,
  `thuclinh` bigint(20) NOT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanluong`
--

INSERT INTO `nhanluong` (`id`, `nhanvien_id`, `heso`, `hsphucap`, `khautru`, `luongcb`, `mucluong`, `phucap`, `ngaycongchuan`, `ngaycong`, `nghihl`, `nghikhl`, `thuong`, `phat`, `tamung`, `thuclinh`, `thang`, `nam`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1.60, 2.00, 0, 1500000, 2400000, 48000, 30, 0, 0, 30, 0, 0, 0, 0, 5, 2025, '2025-05-10 06:02:45', '2025-05-10 06:03:02', '2025-05-10 06:03:02'),
(2, 1, 1.60, 2.00, 0, 1500000, 2400000, 48000, 30, 0, 0, 30, 0, 0, 0, 0, 1, 2025, '2025-05-10 06:03:36', '2025-05-10 06:06:22', '2025-05-10 06:06:22'),
(3, 1, 1.60, 2.00, 0, 1500000, 2400000, 48000, 1, 1, 0, 0, 0, 0, 0, 2448000, 5, 2025, '2025-05-10 06:05:48', '2025-05-10 06:05:48', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `id` int(10) UNSIGNED NOT NULL,
  `phucap_id` int(10) UNSIGNED NOT NULL,
  `bangcap_id` int(10) UNSIGNED NOT NULL,
  `chuyenmon_id` int(10) UNSIGNED NOT NULL,
  `ngoaingu_id` int(10) UNSIGNED NOT NULL,
  `dantoc_id` int(10) UNSIGNED NOT NULL,
  `tongiao_id` int(10) UNSIGNED NOT NULL,
  `hovaten` varchar(100) NOT NULL,
  `gioitinh` tinyint(1) NOT NULL DEFAULT 0,
  `ngaysinh` date NOT NULL,
  `cmnd` varchar(50) NOT NULL,
  `sdt` varchar(15) NOT NULL,
  `diachi` varchar(255) DEFAULT NULL,
  `quequan` varchar(255) DEFAULT NULL,
  `trangthai` tinyint(1) NOT NULL DEFAULT 0,
  `ngaynghilam` date DEFAULT NULL,
  `bacluong` tinyint(4) NOT NULL,
  `hesoluong` double(5,2) NOT NULL,
  `photo_path` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`id`, `phucap_id`, `bangcap_id`, `chuyenmon_id`, `ngoaingu_id`, `dantoc_id`, `tongiao_id`, `hovaten`, `gioitinh`, `ngaysinh`, `cmnd`, `sdt`, `diachi`, `quequan`, `trangthai`, `ngaynghilam`, `bacluong`, `hesoluong`, `photo_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 5, 5, 3, 6, 6, 'Nguyen Minh Nhat', 0, '1986-03-02', '597526535', '0944430146', '72041 Senger Gateway', 'Alyciaside', 1, NULL, 4, 1.00, NULL, '2025-05-08 23:24:53', '2025-05-11 01:37:40', NULL),
(2, 11, 2, 5, 2, 5, 5, 'Mai Tấn Lộc', 0, '1995-01-08', '181535491', '0934343444', '992 Kolby Lock', 'South Pierceshire', 1, NULL, 5, 1.00, NULL, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 18, 2, 3, 1, 7, 4, 'Lê Quang Vinh', 0, '1992-11-20', '571001324', '09343430156', '746 Runolfsson Locks', 'North Susan', 1, NULL, 9, 1.00, NULL, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 4, 5, 5, 1, 5, 6, 'Adella Kreiger', 0, '1988-12-11', '712623505', '888-837-4510', '701 Swift Crest Apt. 179', 'West Abbigail', 1, NULL, 3, 1.00, NULL, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 17, 5, 4, 2, 2, 5, 'Rebeca Abernathy', 1, '1992-05-08', '192624510', '1-888-878-5563', '89065 Marta Glens Apt. 874', 'Benedictside', 1, NULL, 4, 1.00, NULL, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(6, 14, 5, 3, 3, 3, 3, 'Eudora Grady', 1, '1993-02-27', '606304134', '855-344-1912', '80543 Gislason Spring Suite 044', 'West Kenneth', 1, NULL, 9, 1.00, NULL, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(7, 7, 1, 1, 2, 2, 1, 'Vivianne Fadel', 0, '1990-01-01', '957728821', '800.369.2788', '31743 Wisozk Valley Apt. 483', 'Bryceside', 1, NULL, 5, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(8, 9, 5, 5, 2, 7, 4, 'Eugene Medhurst', 0, '1985-05-04', '687875215', '(800) 874-7158', '257 Landen Avenue', 'Port Autumn', 1, NULL, 10, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-11 11:14:54', NULL),
(9, 15, 5, 3, 1, 6, 2, 'Adam Gleason', 1, '1993-05-18', '835867797', '888.371.6314', '1723 McClure Path', 'North Joyce', 1, NULL, 1, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-11 11:24:32', NULL),
(10, 17, 2, 1, 4, 7, 2, 'Rafaela Schroeder', 0, '1994-01-31', '842896234', '888-819-4957', '322 Cristal Pass Suite 193', 'New Coralie', 1, NULL, 7, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(11, 13, 1, 1, 2, 5, 6, 'Arvid Doyle', 0, '1991-11-21', '121377370', '866.203.9505', '831 Rubye Points', 'East Delfinamouth', 1, NULL, 1, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(12, 7, 2, 5, 4, 2, 2, 'Jensen Windler', 0, '1986-10-29', '325860609', '1-800-694-6563', '614 Alba Oval Suite 723', 'Auerfort', 1, NULL, 6, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(13, 2, 4, 3, 6, 2, 1, 'Esmeralda Braun', 1, '1995-08-24', '368589921', '(855) 688-9992', '6103 Mathilde Shoals Apt. 684', 'East Caitlyn', 1, NULL, 10, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(14, 4, 1, 2, 2, 3, 5, 'Anne King', 0, '1993-11-06', '259678682', '(844) 620-4766', '687 Hollie Islands Apt. 789', 'Funkport', 1, NULL, 3, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(15, 4, 2, 5, 2, 4, 1, 'Fleta Lesch', 0, '1993-05-15', '266895301', '855-307-6439', '954 Ivy Groves', 'Port Tiara', 1, NULL, 5, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(16, 10, 1, 4, 2, 7, 3, 'Lamont Bahringer', 1, '1993-03-03', '608243144', '877.426.3562', '14820 Piper Unions', 'Breitenbergbury', 1, NULL, 3, 1.00, NULL, '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(17, 14, 4, 5, 4, 6, 2, 'D\'angelo Hickle', 0, '1992-11-22', '193668445', '855-388-0955', '80019 Reinger Knoll', 'Gaylordbury', 0, NULL, 5, 1.00, NULL, '2025-05-08 23:24:55', '2025-05-11 01:39:03', NULL),
(18, 6, 5, 3, 3, 5, 6, 'Jewel Friesen', 1, '1988-09-20', '186494318', '800.367.8293', '2903 Malinda Tunnel Suite 176', 'Adriannamouth', 1, NULL, 3, 1.00, NULL, '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(19, 8, 1, 4, 3, 6, 3, 'Terrell Abshire', 1, '1994-07-06', '434898527', '888.578.5662', '114 Maryse Stream Suite 637', 'Rosalyntown', 0, NULL, 9, 1.00, NULL, '2025-05-08 23:24:55', '2025-05-11 01:33:36', '2025-05-11 01:33:36'),
(20, 4, 5, 2, 6, 3, 5, 'Ila Hodkiewicz', 1, '1993-05-19', '656000699', '1-844-626-4301', '199 Josephine Harbor', 'East Columbus', 1, NULL, 4, 1.00, NULL, '2025-05-08 23:24:55', '2025-05-11 11:14:14', NULL),
(21, 7, 3, 3, 2, 4, 6, 'Lelia Osinski', 0, '1994-05-30', '231901778', '855-379-9108', '8251 Mills Courts', 'Port Adrianaview', 1, NULL, 9, 1.00, NULL, '2025-05-08 23:24:55', '2025-05-11 11:14:30', NULL),
(22, 16, 5, 5, 1, 4, 2, 'Tillman Dare', 1, '1985-08-28', '100167514', '888-393-5617', '61910 Stephany Shoals', 'Abernathyside', 1, NULL, 8, 1.00, NULL, '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(23, 10, 4, 5, 2, 4, 6, 'Camryn Abshire', 0, '1992-05-11', '965970499', '888-570-6886', '2405 Bauch Plains', 'New Barrett', 1, NULL, 1, 1.00, NULL, '2025-05-08 23:24:55', '2025-05-11 11:14:43', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phongban`
--

CREATE TABLE `phongban` (
  `id` int(10) UNSIGNED NOT NULL,
  `tenpb` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phongban`
--

INSERT INTO `phongban` (`id`, `tenpb`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Ban Giám Đốc', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 'Phòng Kinh Doanh', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 'Phòng Phân Tích', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 'Phòng Thiết Kế', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 'Phòng Lập Trình', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(6, 'Phòng Hành Chính', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phucap`
--

CREATE TABLE `phucap` (
  `id` int(10) UNSIGNED NOT NULL,
  `phongban_id` int(10) UNSIGNED NOT NULL,
  `chucvu_id` int(10) UNSIGNED NOT NULL,
  `hsphucap` double(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phucap`
--

INSERT INTO `phucap` (`id`, `phongban_id`, `chucvu_id`, `hsphucap`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 2.50, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 1, 3, 2.00, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 2, 2, 1.50, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 2, 3, 1.20, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 2, 4, 1.00, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(6, 2, 5, 1.00, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(7, 3, 2, 1.50, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(8, 3, 3, 1.20, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(9, 3, 5, 1.00, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(10, 4, 2, 1.50, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(11, 4, 3, 1.20, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(12, 4, 5, 1.00, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(13, 5, 2, 1.50, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(14, 5, 3, 1.20, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(15, 5, 5, 1.00, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(16, 6, 2, 1.50, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(17, 6, 3, 1.20, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(18, 6, 5, 1.00, '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('KIBJIXJROxBFpAFmMyhfPo6acT6Xp3Mh4gEGeJOQ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZWJmNUtEaWFMS29RZnNWdXI1MVV4VHhPS1pvVHUzOVpid3ZuVjNkSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1747028835),
('SYOiRRjYnWxz3WSW3IUeNcxzbCVPNUs0eAzml2xh', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYm9jSFNKbVhEeTA5OE1Bc2hPUk8yRkFrMWptR0l3cG5hc3ZHVFJDbCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fX0=', 1746988079);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thuongphat`
--

CREATE TABLE `thuongphat` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `loai` tinyint(1) NOT NULL DEFAULT 0,
  `sotien` bigint(20) NOT NULL,
  `lydo` varchar(255) NOT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thuongphat`
--

INSERT INTO `thuongphat` (`id`, `nhanvien_id`, `loai`, `sotien`, `lydo`, `thang`, `nam`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 13, 0, 100000, 'Tang Ca', 5, 2025, '2025-05-10 04:27:21', '2025-05-10 04:56:51', NULL),
(2, 17, 1, 50000, 'tang ca', 5, 2025, '2025-05-10 05:22:03', '2025-05-10 05:22:03', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tongiao`
--

CREATE TABLE `tongiao` (
  `id` int(10) UNSIGNED NOT NULL,
  `tentg` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tongiao`
--

INSERT INTO `tongiao` (`id`, `tentg`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Phật giáo', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 'Công giáo', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 'Tin Lành', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 'Hồi giáo', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 'Cao Đài', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(6, 'Hoà Hảo', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ungluong`
--

CREATE TABLE `ungluong` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `sotien` bigint(20) NOT NULL,
  `lydo` varchar(255) NOT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nhanvien_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(50) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `nhanvien_id`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'admin@email.com', '2025-05-08 23:24:53', '$2y$10$J68Xqq1MeBfO0G4M0D8eZ.7noZRJ3nsOnjiZWIIGNOv0siBwJkKlS', 2, 'drCjGreMl67a2iVwW2qatRN6z6UGia4Gq85vdSrjPthNRFWwkT8GjQ2OWCdj', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(2, 2, 'quanly@email.com', '2025-05-08 23:24:53', '$2y$10$K8b6hHpGQiL991EH8fy6AOH7o745t1BC7c.a6nDUKlfKNQuHhZRSe', 1, '8AZwLVkhNDEIl4uAHFN10NaBUHPaMCw8fufeiF26R67jElI1rPAm3ITaqaWz', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(3, 3, 'nhanvien@email.com', '2025-05-08 23:24:53', '$2y$10$B5xNZ5UDVJQ2Qw.GPJd2a.ZBSOLoq86zKiZN.pOXllnU4Z7wtzzwW', 0, 'LJLDmSEab7mNfyqBw8JPNNNG2ORpp5v6uoPsSkzZCilDE55bo6PKk11mEpfm', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(4, 4, 'rgutmann@example.net', '2025-05-08 23:24:53', '$2y$10$GLk.wvRR45U9vF32exEsv.tkmUs9mWgAgQ1fij2ufBzcXhDmq0sPK', 0, 'lSEjVDOLBO', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(5, 5, 'wendy12@example.com', '2025-05-08 23:24:53', '$2y$10$5.zF7EoGS29mrX8Oyc1oFew5jsMaQwtyCa1WCUWsWJ04Mhn5alaZ6', 0, 'DToOF42rZ9', '2025-05-08 23:24:53', '2025-05-08 23:24:53', NULL),
(6, 6, 'norbert42@example.net', '2025-05-08 23:24:53', '$2y$10$GK1yN2MQNzv8tTD77T93z..NybN.PFN5TZEtc8p/ankBse2guWJZC', 0, 'RXvrwNVNTB', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(7, 7, 'marvin.parker@example.org', '2025-05-08 23:24:54', '$2y$10$8Mx6KCqMWHHkhSfmkAkgwO350ehbJGTAos5ss3sRgqjowW4WvVpy2', 0, 'tgDnPg1Cwq', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(8, 8, 'kendall.lehner@example.com', '2025-05-08 23:24:54', '$2y$10$s9mJstA1gxUw5zn2QZkFxuqVzui4Xd/s6GnS1z1ug0VT6vRE6YnDK', 0, 'hLq4sCVTPE', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(9, 9, 'goldner.lauretta@example.org', '2025-05-08 23:24:54', '$2y$10$PGDW2QPFShSgBWGnAAP8KeZzUNoOcKsmdM1DYAr4rDXP9mv7VBCqG', 0, 'Okz3g86nFJ', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(10, 10, 'gerhard00@example.com', '2025-05-08 23:24:54', '$2y$10$KRcxNpOG0JzyrJjRcz5gFus3hN7pfgpN2qAYXEN6JN43gSRRBt/fW', 0, '7i49v8le3f', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(11, 11, 'alena.glover@example.com', '2025-05-08 23:24:54', '$2y$10$MvhW9Rh6AMj0EaEYxYNuFePw3WezUP.7LPjfulmJtuk0CwBboB.Je', 0, 'DESMrGALg5', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(12, 12, 'bschamberger@example.org', '2025-05-08 23:24:54', '$2y$10$7m3ORSOQickpuWnA5gd7Y.UWKP6CFpBkfwPmMZm4UjBHRPsiS0nOW', 0, 'g7RRU4ONaK', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(13, 13, 'labadie.sam@example.net', '2025-05-08 23:24:54', '$2y$10$v.cYcy1UD4kc8bvzHWqwxemB38rEievmokzE0hotmSUN/DqvB6BJK', 0, '6HTsKExBQ5', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(14, 14, 'njohnson@example.org', '2025-05-08 23:24:54', '$2y$10$3ZK6oOmUujQqn9dDpAvpgOTQU/gfrwvACSRhQsoBtHu25EZ/loS.q', 0, '7DjHjzK8tK', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(15, 15, 'bertrand79@example.org', '2025-05-08 23:24:54', '$2y$10$gLrfELszvh.q5MRVzeVZoOb3TI8CMSofxCBWwQ6uptx0ft5pByILK', 0, 'qaYLszyrsC', '2025-05-08 23:24:54', '2025-05-08 23:24:54', NULL),
(16, 16, 'marcellus66@example.net', '2025-05-08 23:24:54', '$2y$10$qV/NSLew140AORJBjBk.euJURMsOk4mBHpa0XweLPyiM4fxXLk3LK', 0, 'OJ99GZD6as', '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(17, 17, 'cfadel@example.net', '2025-05-08 23:24:55', '$2y$10$4GB1Yr3Tsp/lyQn4H8WdZ.HOPF0/JGL8Af7hqaH7A30Z1RKyb2rb2', 0, 'APaaNCM8iI', '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(18, 18, 'stephania11@example.org', '2025-05-08 23:24:55', '$2y$10$V55MZBpTszR4FeXiRFtG9uLPkyVz397G9A654LVrKN0g7lUSQ04SW', 0, 'V1d7O0dCJQ', '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(19, 19, 'lexie43@example.com', '2025-05-08 23:24:55', '$2y$10$xYFVgJIfcecODP46pcISQeFMk/O88RaNwSPaQQBcGCS5B7Od8mCgm', 0, 'vq5XQIsz5b', '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(20, 20, 'raegan58@example.com', '2025-05-08 23:24:55', '$2y$10$.8j02GNfJgbwPfZx0FjV5.JMwiYRB8ebaVB0axhMgpVtWYueyT62u', 0, '2EC7njVMzr', '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(21, 21, 'kiley82@example.net', '2025-05-08 23:24:55', '$2y$10$hShuZtC4o7Ne8oANOaTdy.v7aBxfKcMdqYQ.7RKrlKjG5GSImDD6m', 0, 'gFJUUYHLET', '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(22, 22, 'johns.maegan@example.org', '2025-05-08 23:24:55', '$2y$10$7ZkjJ6jZikLX4zAHMOVw7O9FpDiSE7f4HbJOupJQ83t6kjUmlL4VO', 0, '7jmNk6HGaQ', '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL),
(23, 23, 'gaylord.ebert@example.org', '2025-05-08 23:24:55', '$2y$10$hRIN1jdnx4dXGcI/pSz2SecfSdITjjDYYdhGUAVBDSsEl7NzbxLyG', 0, '5penbgmeMa', '2025-05-08 23:24:55', '2025-05-08 23:24:55', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bangcap`
--
ALTER TABLE `bangcap`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `baohiem`
--
ALTER TABLE `baohiem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_baohiem_nhanvien_id` (`nhanvien_id`),
  ADD KEY `fk_baohiem_loaibaohiem_id` (`loaibaohiem_id`);

--
-- Chỉ mục cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_chamcong_nhanvien_id` (`nhanvien_id`);

--
-- Chỉ mục cho bảng `chucvu`
--
ALTER TABLE `chucvu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chuyenmon`
--
ALTER TABLE `chuyenmon`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `dantoc`
--
ALTER TABLE `dantoc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `heso`
--
ALTER TABLE `heso`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `hopdong`
--
ALTER TABLE `hopdong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_hopdong_nhanvien_id` (`nhanvien_id`);

--
-- Chỉ mục cho bảng `khautru`
--
ALTER TABLE `khautru`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_khautru_nhanvien_id` (`nhanvien_id`),
  ADD KEY `fk_khautru_loaibaohiem_id` (`loaibaohiem_id`);

--
-- Chỉ mục cho bảng `loaibaohiem`
--
ALTER TABLE `loaibaohiem`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nghiviec`
--
ALTER TABLE `nghiviec`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nghiviec_nhanvien_id` (`nhanvien_id`);

--
-- Chỉ mục cho bảng `ngoaingu`
--
ALTER TABLE `ngoaingu`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nhanluong`
--
ALTER TABLE `nhanluong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nhanluong_nhanvien_id` (`nhanvien_id`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nhanvien_phucap_id` (`phucap_id`),
  ADD KEY `fk_nhanvien_bangcap_id` (`bangcap_id`),
  ADD KEY `fk_nhanvien_chuyenmon_id` (`chuyenmon_id`),
  ADD KEY `fk_nhanvien_ngoaingu_id` (`ngoaingu_id`),
  ADD KEY `fk_nhanvien_dantoc_id` (`dantoc_id`),
  ADD KEY `fk_nhanvien_tongiao_id` (`tongiao_id`);

--
-- Chỉ mục cho bảng `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Chỉ mục cho bảng `phongban`
--
ALTER TABLE `phongban`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `phucap`
--
ALTER TABLE `phucap`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_phucap_phongban_id` (`phongban_id`),
  ADD KEY `fk_phucap_chucvu_id` (`chucvu_id`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `thuongphat`
--
ALTER TABLE `thuongphat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_thuongphat_nhanvien_id` (`nhanvien_id`);

--
-- Chỉ mục cho bảng `tongiao`
--
ALTER TABLE `tongiao`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `ungluong`
--
ALTER TABLE `ungluong`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ungluong_nhanvien_id` (`nhanvien_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_nhanvien_id_unique` (`nhanvien_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bangcap`
--
ALTER TABLE `bangcap`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `baohiem`
--
ALTER TABLE `baohiem`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `chucvu`
--
ALTER TABLE `chucvu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `chuyenmon`
--
ALTER TABLE `chuyenmon`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `dantoc`
--
ALTER TABLE `dantoc`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `heso`
--
ALTER TABLE `heso`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `hopdong`
--
ALTER TABLE `hopdong`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `khautru`
--
ALTER TABLE `khautru`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loaibaohiem`
--
ALTER TABLE `loaibaohiem`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `nghiviec`
--
ALTER TABLE `nghiviec`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `ngoaingu`
--
ALTER TABLE `ngoaingu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `nhanluong`
--
ALTER TABLE `nhanluong`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `phongban`
--
ALTER TABLE `phongban`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `phucap`
--
ALTER TABLE `phucap`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT cho bảng `thuongphat`
--
ALTER TABLE `thuongphat`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `tongiao`
--
ALTER TABLE `tongiao`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `ungluong`
--
ALTER TABLE `ungluong`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `baohiem`
--
ALTER TABLE `baohiem`
  ADD CONSTRAINT `fk_baohiem_loaibaohiem_id` FOREIGN KEY (`loaibaohiem_id`) REFERENCES `loaibaohiem` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_baohiem_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  ADD CONSTRAINT `fk_chamcong_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `hopdong`
--
ALTER TABLE `hopdong`
  ADD CONSTRAINT `fk_hopdong_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `khautru`
--
ALTER TABLE `khautru`
  ADD CONSTRAINT `fk_khautru_loaibaohiem_id` FOREIGN KEY (`loaibaohiem_id`) REFERENCES `loaibaohiem` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_khautru_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nghiviec`
--
ALTER TABLE `nghiviec`
  ADD CONSTRAINT `fk_nghiviec_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nhanluong`
--
ALTER TABLE `nhanluong`
  ADD CONSTRAINT `fk_nhanluong_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `fk_nhanvien_bangcap_id` FOREIGN KEY (`bangcap_id`) REFERENCES `bangcap` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nhanvien_chuyenmon_id` FOREIGN KEY (`chuyenmon_id`) REFERENCES `chuyenmon` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nhanvien_dantoc_id` FOREIGN KEY (`dantoc_id`) REFERENCES `dantoc` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nhanvien_ngoaingu_id` FOREIGN KEY (`ngoaingu_id`) REFERENCES `ngoaingu` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nhanvien_phucap_id` FOREIGN KEY (`phucap_id`) REFERENCES `phucap` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nhanvien_tongiao_id` FOREIGN KEY (`tongiao_id`) REFERENCES `tongiao` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `phucap`
--
ALTER TABLE `phucap`
  ADD CONSTRAINT `fk_phucap_chucvu_id` FOREIGN KEY (`chucvu_id`) REFERENCES `chucvu` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phucap_phongban_id` FOREIGN KEY (`phongban_id`) REFERENCES `phongban` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `thuongphat`
--
ALTER TABLE `thuongphat`
  ADD CONSTRAINT `fk_thuongphat_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `ungluong`
--
ALTER TABLE `ungluong`
  ADD CONSTRAINT `fk_ungluong_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_nhanvien_id` FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
