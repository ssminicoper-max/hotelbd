-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2025 at 04:01 PM
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
-- Database: `hotel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `total_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `listing_id`, `tenant_id`, `start_date`, `end_date`, `status`, `total_price`, `created_at`) VALUES
(1, 1, 2, '2025-11-01', '2026-04-30', 'pending', 21000.00, '2025-10-13 14:09:32'),
(2, 2, 2, '2025-12-01', '2026-03-31', 'approved', 16800.00, '2025-10-13 14:09:32'),
(3, 1, 2, '2025-10-01', '2025-10-31', 'cancelled', 3500.00, '2025-10-13 14:09:32'),
(4, 3, 9, '2568-10-14', '2569-10-14', 'cancelled', 110500.00, '2025-10-14 08:56:13'),
(5, 4, 9, '2568-12-13', '2569-12-13', 'approved', 68250.00, '2025-10-14 10:45:08'),
(6, 1, 10, '2568-10-14', '2569-10-14', 'pending', 45500.00, '2025-10-14 10:50:57'),
(7, 2, 9, '2568-10-14', '2569-10-14', 'pending', 54600.00, '2025-10-14 10:52:46'),
(8, 5, 10, '2568-10-15', '2569-10-15', 'approved', 50700.00, '2025-10-14 10:57:51'),
(9, 4, 14, '5555-05-15', '6666-05-05', 'approved', 71011500.00, '2025-10-14 16:14:36'),
(10, 4, 14, '2567-09-15', '2568-10-20', 'cancelled', 73500.00, '2025-10-14 16:16:30');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`user_id`, `listing_id`, `created_at`) VALUES
(2, 1, '2025-10-13 14:09:32'),
(2, 2, '2025-10-13 14:09:32');

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `listing_id`, `filename`, `uploaded_at`) VALUES
(5, 1, 'uploads/onin14_1.jpg', '2025-10-13 15:46:47'),
(7, 2, 'uploads/taramanta_1.jpg', '2025-10-13 15:46:47'),
(8, 3, 'uploads/maple_1.jpg', '2025-10-14 08:44:36'),
(9, 4, 'uploads/L4_20251014_112330_bfbf19_tarra_1.png', '2025-10-14 09:23:30'),
(10, 5, 'uploads/L5_20251014_125702_ab75ab_infi.png', '2025-10-14 10:57:02'),
(17, 8, 'uploads/L8_20251016_135534_fc6ea7_book1.png', '2025-10-16 11:55:34'),
(18, 8, 'uploads/L8_20251016_135534_e38056_book2.png', '2025-10-16 11:55:34'),
(19, 8, 'uploads/L8_20251016_135534_730b79_book3.png', '2025-10-16 11:55:34'),
(20, 9, 'uploads/L9_20251016_154117_1d8656_ava11.png', '2025-10-16 13:41:17'),
(21, 9, 'uploads/L9_20251016_154117_b2d898_ava12.png', '2025-10-16 13:41:17'),
(22, 9, 'uploads/L9_20251016_154117_5a336b_ava13.png', '2025-10-16 13:41:17'),
(23, 9, 'uploads/L9_20251016_154117_28c406_ava14.png', '2025-10-16 13:41:17'),
(24, 10, 'uploads/L10_20251016_154818_504616_pek11.png', '2025-10-16 13:48:18'),
(25, 10, 'uploads/L10_20251016_154818_b2a568_pek12.png', '2025-10-16 13:48:18'),
(26, 10, 'uploads/L10_20251016_154818_9a710f_pek13.png', '2025-10-16 13:48:18'),
(27, 10, 'uploads/L10_20251016_154818_6d92cb_pek14.png', '2025-10-16 13:48:18'),
(28, 10, 'uploads/L10_20251016_154818_230525_pek15.png', '2025-10-16 13:48:18');

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deposit` decimal(10,2) DEFAULT NULL,
  `electricity_rate` varchar(60) DEFAULT NULL,
  `water_rate` varchar(60) DEFAULT NULL,
  `other_fee` decimal(10,2) DEFAULT NULL,
  `internet` varchar(100) DEFAULT NULL,
  `details_long` mediumtext DEFAULT NULL,
  `contact_phone` varchar(32) DEFAULT NULL,
  `contact_line` varchar(64) DEFAULT NULL,
  `contact_facebook` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `map_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `owner_id`, `title`, `area`, `type`, `price`, `description`, `status`, `created_at`, `deposit`, `electricity_rate`, `water_rate`, `other_fee`, `internet`, `details_long`, `contact_phone`, `contact_line`, `contact_facebook`, `contact_email`, `map_url`) VALUES
(1, 1, 'Onin14', 'ซ.ตะวันออก 12 ถ.เลียบคลองหก คลองหก คลองหลวง ปทุมธานี', 'หอพัก', 3500.00, 'ห้องสตูดิโอ ใกล้ มทร.ธัญบุรี เดิน 8 นาที', 'active', '2025-10-13 14:09:32', 5500.00, 'ราคาต่อยูนิตตามที่การไฟฟ้ากำหนด', 'ราคาต่อยูนิตตามที่การประปากำหนด', 350.00, 'ฟรี', 'ข้อมูลเพิ่มเติม ONIN14 (โอนิน14 คลองหก ปทุมธานี)\r\nเลขบัญชีโอนจอง โอนค่าห้องโอนิน14 คือ บจก.พาโนเพลส ธ.กรุงศรี 3971411068 เท่านั้น อย่าหลงเชื่อผู้แอบอ้างเลขบัญชีอื่น\r\n\r\nให้เช่าอพาร์ทเม้นท์รูปแบบทันสมัย ตึก8 ชั้นใกล้ม.ราชมงคลธัญบุรี ซ.ตะวันออก12 บรรยากาศร่มรื่นเย็นสบาย คำนึงถึงความสะดวกสบายและความปลอดภัย ห้องพักสไตล์โมเดิร์น มีให้เลือกหลายโทนสี \r\n\r\n-พร้อมแอร์/น้ำอุ่นทุกห้อง \r\n\r\n-ห้องน้ำแยกส่วนเปียกแห้ง \r\n\r\n-เฟอร์บิวอิน พร้อมห้องแต่งตัว \r\n\r\n-ฟรี High Speed Internet \r\n\r\n-ฟรี ห้องฟิตเนส ติดแอร์\r\n\r\n-ที่จอดรถในร่ม \r\n\r\n-รปภ.\r\n\r\n-ระบบสแกนนิ้ว\r\n\r\n-มีมินิมาร์ท ร้านอาหาร ร้านซักอบรีดเปิดให้บริการในอพาร์ทเม้นท์\r\n\r\n-CCTVรอบอาคาร 40 ตัว', '0632108361', NULL, NULL, NULL, NULL),
(2, 1, 'Taramanta', 'คลองหก', 'อพาร์ตเมนต์', 4900.00, 'วิวโล่ง ชั้นสูง ลมดี เฟอร์ครบ', 'active', '2025-10-13 14:09:32', 5500.00, '5 บาท/ยูนิต', '33 บาท/ยูนิต', 350.00, 'ฟรี', 'ข้อมูลเพิ่มเติม ทารามันตา เซอร์วิส อพาร์ทเม้นท์ ตึก A\r\nที่พักแบบรายวัน และแบบรายเดือน สไตร์สหรู ย่านคลองหก ใกล้มหาวิทยาลัยเทคโนราชมงคลธัญบุรี คลองหก  ไม่แออัด ธรรมชาติ บรรยากาศร่มรื่น โล่ง โปร่งสบายสะอาด ปลอดภัย(ด้วยระบบคีย์การ์ด และ รปภ.) สิ่งอำนวยความสะดวกพร้อม เฟอร์นิเจอร์ภายในห้องพักพร้อม\r\nฟรีฟิตเนต  ฟรีWifi มีร้านอาหาร ร้านกาแฟ ภายในบริเวณที่พัก ที่จอดรถสะดวกสบาย  บริการเป็นกันเอง', '0831482915', NULL, NULL, NULL, NULL),
(3, 1, 'Maple City', 'คลองเจ็ด', 'หอพัก', 8500.00, 'คลองหลวง คลองเจ็ด ปทุมธานี', 'active', '2025-10-13 14:09:32', 500.00, '7 บาท/ยูนิต', '150 บาทต่อห้อง/เดือน ', 350.00, 'ฟรี', 'ข้อมูลเพิ่มเติม Maple City - เมเปิลซิตี้ หอพักเปิดใหม่!!\r\nหอพักเปิดใหม่! ห้องใหม่เอี่ยม! ที่จอดรถเยอะมาก พื้นที่กว้างขวาง ทำเลทอง ใกล้ ม.ราชมงคลธัญบุรี คลอง 6\r\n\r\nราคาเบาๆ แต่ได้ความสะดวกแบบจัดเต็ม อารมณ์เหมือนอยู่คอนโด\r\n\r\nขนาดห้อง 26 ตร.ม. เริ่มต้นเพียง 4,900 บาท/เดือน\r\n\r\nขนาดห้อง 34 ตร.ม. เริ่มต้นเพียง 8,500 บาท/เดือน\r\n\r\nเหมาะสำหรับนักศึกษา คนทำงาน หรือใครที่มองหาที่พักสะดวกสบายในราคาคุ้มค่า\r\n\r\nเฟอร์ครบ พร้อมอยู่!\r\n\r\nแอร์, ตู้เย็น\r\n\r\nไมโครเวฟ, เครื่องทำน้ำอุ่น\r\n\r\nสมาร์ททีวี Samsung 50 นิ้ว\r\n\r\nโซฟา + โต๊ะกลาง\r\n\r\nชุดครัว + อ่างล้างจาน\r\n\r\nโต๊ะอ่านหนังสือ\r\n\r\nพิเศษ! มี Fitness + ห้องรับรองส่วนกลาง\r\n\r\nระบบรักษาความปลอดภัยแน่นหนา\r\n\r\nสแกนคีย์การ์ดเข้า-ออก\r\n\r\nกล้องวงจรปิดทุกชั้น\r\n\r\nใกล้ ม.เทคโนโลยีราชมงคลธัญบุรี คลอง 6\r\n\r\nใกล้หมู่บ้านพรธิสาร 7\r\n\r\nใกล้ตลาดอาม่า 1,000 สุข\r\n\r\nใกล้ทางออกถนนใหญ่ เดินทางสะดวก', '082 239 8998', '@maplecity', NULL, NULL, ' https://maps.app.goo.gl/CrMsPTCoNZC6ZNuN6'),
(4, 8, 'Tarra', 'คลองหก', 'หอพัก', 5250.00, 'ซ.คลองหกตะวันออก 12 คลองหก คลองหลวง ปทุมธานี', 'active', '2025-10-14 08:59:08', 10.00, '7 บาท/ยูนิต', '30 บาท/ยูนิต ขั้นต่ำ 100 บาท/เดือน', 100.00, 'ฟรี', 'ข้อมูลเพิ่มเติม Tarra (ทาร่า คลอง 6 ปทุมธานี) ราชมงคลธัญบุรี คลอง 6 New Apartment\r\n🎉Tarra (ทาร่า) คลอง 6 ปทุมธานี🎉\r\n\r\nเปิดจองห้องพักรายเดือนแล้ววันนี้\r\n\r\nพิเศษ ค่าจองเพิยง 4,000 บาทเท่านั้น❗❗\r\n\r\n🌈จองก่อนเลือกห้องได้ก่อนน้า\r\n\r\nติดต่อจองห้อง หรือสอบถามเพิ่มเติมได้ที่\r\n\r\n📞 โทร : 0642864553\r\n\r\n🆔 Line ID : @775docuw (มี@)\r\n________________________________________________________________________________________________________________\r\n\r\nTarra (ทาร่า) คลอง 6 ปทุมธานี\r\n\r\nโครงการที่พักพรีเมี่ยม พร้อมสิ่งอำนวยความสะดวกครบครัน ใกล้แหล่งร้านอาหาร เดินทางสะดวก\r\n\r\nห้องพัก Studio Room พร้อม โซนครัว🥳', '0942514278', '@775docuw (มี@)', NULL, NULL, NULL),
(5, 13, 'Infinity place', 'คลองหก', 'หอพัก', 3900.00, 'ซ.คลองหกตะวันออก 16 ถ.เลียบคลองหก คลองหก คลองหลวง ปทุมธานี', 'active', '2025-10-14 10:55:19', 7000.00, '7 บาท/ยูนิต', '30 บาท/ยูนิต ขั้นต่ำ 100 บาท/เดือน', 500.00, 'ฟรี', 'รายละเอียด\r\nข้อมูลเพิ่มเติม อินฟินิตี้ เพลส ( Infinity Place ) ใกล้ ม.ราชมงคลธัญบุรี คลองหก\r\nอพาร์ทเม้นท์หรู สไตล์คอนโด \r\nซอยคลองหกตะวันออก 16\r\n*** ห้องพัก ก ว้ า ง 3 0  ตารางเมตร ***\r\nทันสมัย สะอาด สะดวกสบาย และปลอดภัยสุดๆ บรรยากาศร่มรื่น เน้นโล่งโปร่งสบาย\r\nเดินทางสะดวก แยกพื้นที่เป็นสัดส่วน\r\n**ใกล้ ม.ราชมงคลธัญบุรี เพียง 5 นาที หรือ 450 เมตรเท่านั้น**\r\n**ใกล้ ม.อิสเทิร์นเอเซีย**\r\nสิ่งอำนวยความสะดวก\r\n๐ ระบบความปลอดภัย 24 ชั่วโมง\r\n๐ เข้า-ออกด้วยระบบตรวจสอบลายนิ้วมือ\r\n๐ กล้องวงจรปิด (CCTV )กว่า 100 ตัว ทั่วอาคาร\r\n๐ ที่จอดรถในอาคาร และลานจอดรถ\r\n๐ สวนหย่อม\r\n๐ ร้านมินิมาร์ท / ร้านอาหาร / ร้านซักรีด / ร้านกาแฟ\r\n๐ ตู้น้ำดื่ม-เครื่องซักผ้าหยอดเหรียญ, ตู้ ATM\r\no กริ่งกดเรียกวินมอไซด์หน้าอาคาร กดปุ๊บ...มาปั๊บ!! \r\n\r\nเฟอร์นิเจอร์ครบชุด \r\n๐ เครื่องปรับอากาศ\r\n๐ เครื่องทำน้ำอุ่น\r\no ไมโครเวฟ\r\n๐ ตู้เย็น 5.2 คิว\r\n๐ ระเบียงเคาเตอร์ซิ้งล้างจาน', '0819379177', '@infinityplace ', 'https://www.facebook.com/infinityapartment', NULL, NULL),
(8, 15, 'เดอะบุ๊ค (THE BOOK)', 'คลองหก', 'หอพัก', 3900.00, 'ซ.คลองหกตะวันออก 12 (มาลี) ถ.เลียบคลองหก คลองหก คลองหลวง ปทุมธานี', 'active', '2025-10-16 11:55:28', 5000.00, '', 'ราคาต่อยูนิตตามที่การประปากำหนด', 300.00, 'ฟรี', 'ข้อมูลเพิ่มเติม เดอะบุ๊ค (THE BOOK)\r\nเดอะบุ๊ค (THE BOOK)\r\n***ติดต่อห้องพัก วันจันทร์ - เสาร์ 9.00 น. - 18.00 น.\r\n  หยุดวันอาทิตย์ \r\nสระว่ายน้ำสไตล์รีสอร์ท พร้อมมีร้าน SEVEN-ELEVEN (7-11) เปิดหน้าตึกเลย \r\nให้เช่าอพาร์ทเม้นท์ใหม่ เพิ่งสร้างเสร็จมกราคม 2559 ตึกสวย ถ่ายหนังตึก 8 ชั้นใกล้ม.ราชมงคลธัญบุรี ซ.ตะวันออก12 บรรยากาศร่มรื่นเย็นสบาย คำนึงถึงความสะดวกสบายและความปลอดภัย ห้องพักสไตล์โมเดิร์น มีให้เลือกหลายโทนสี \r\n\r\nพร้อมแอร์/น้ำอุ่นทุกห้อง \r\nห้องน้ำแยกส่วนเปียกแห้ง ฝักบัวแบบrain shower ทันสมัย\r\nเฟอร์บิวอิน พร้อมห้องแต่งตัว \r\nฟรี ห้องฟิตเนส ติดแอร์\r\nสระว่ายน้ำ สไตล์รีสอร์ท\r\nที่จอดรถ\r\nรปภ.\r\nระบบสแกนนิ้ว/สแกนหน้า\r\nกล้องวงจรปิด 40 ตัว\r\nมี 7- eleven ร้านอาหาร อยู่ด้านหน้า เครื่องซักผ้าหยอดเหรียญ(เปิดให้บริการในอพาร์ทเม้นท์)', '0615232442', 'rp-999', '', 'jongjit1969@gmail.com', ''),
(9, 16, 'ELVA (เอลวา คลองหก ปทุมธานี)', 'คลองหก', 'อพาร์ตเมนต์', 4000.00, 'ซ.คลองหกตะวันออก 12 คลองหก คลองหลวง ปทุมธานี', 'active', '2025-10-16 13:40:55', 6000.00, 'ราคาต่อยูนิตตามที่การไฟฟ้ากำหนด', 'ราคาต่อยูนิตตามที่การประปากำหนด', 350.00, 'ฟรี', 'ค่าน้ำและค่าไฟคิดตามมิเตอร์จริง และค่าส่วนกลาง 350 บาทต่อเดือน**\r\nอาคารชุดใหม่ 8 ชั้น ใกล้มหาวิทยาลัยเทคโนโลยีราชมงคลธัญบุรี ซอยตะวันออก 12 บรรยากาศร่มรื่นเย็นสบาย คำนึงถึงความสะดวกสบายและความปลอดภัย ห้องพักสไตล์โมเดิร์น มีโทนสีให้เลือกหลากหลาย\r\n** ราคาห้อง 3,9000 บาท และ 4,100 บาท พร้อมอินเทอร์เน็ตความเร็วสูงฟรี**\r\n- มีเครื่องปรับอากาศ/น้ำอุ่นทุกห้อง\r\n- ห้องน้ำแยกส่วนเปียกและส่วนแห้ง\r\n- เฟอร์นิเจอร์บิวท์อิน พร้อมห้องแต่งตัว\r\n- อินเทอร์เน็ตความเร็วสูงฟรี\r\n- ห้องออกกำลังกายปรับอากาศฟรี\r\n- ที่จอดรถในร่ม\r\n- ระบบรักษาความปลอดภัย\r\n- ระบบสแกนลายนิ้วมือ\r\n- กล้องวงจรปิดรอบอาคาร 40 อาคาร\r\n- ที่จอดรถในร่ม\r\n- สวนหย่อม\r\nสถานที่ใกล้เคียง\r\nใกล้มหาวิทยาลัยเทคโนโลยีราชมงคลธัญบุรี เพียง 5 นาที หรือ 400 เมตร จากหน้าประตู 4 (มหาวิทยาลัยเทคโนโลยีราชมงคลธัญบุรี)\r\nใกล้พิพิธภัณฑ์วิทยาศาสตร์แห่งชาติ\r\nใกล้ Thai Wake Park\r\nใกล้มหาวิทยาลัยอีสเทิร์นเอเชีย\r\nใกล้องค์การบริหารส่วนตำบลคลองหก\r\nใกล้พรธิสาร', '0635168194', 'elvaklong6', '', '', ''),
(10, 17, 'หอพัก THE PEAK (เดอะ พีค )', 'คลองหก', 'หอพัก', 4500.00, 'ซ.คลองหกตะวันออก 14 ถ.รังสิต-นครนายก คลองหก คลองหลวง ปทุมธานี', 'active', '2025-10-16 13:45:54', 2000.00, 'ราคาต่อยูนิตตามที่การไฟฟ้ากำหนด', 'ราคาต่อยูนิตตามที่การประปากำหนด', 350.00, 'ฟรี', 'ข้อมูลเพิ่มเติม หอพัก THE PEAK (เดอะ พีค ) ใกล้ม.ราชมงคลธัญบุรี คลอง6 ซอย14\r\nสัมผัสอพาร์ทเม้นท์รูปแบบใหม่ ห้องพักสไตล์สุดหรูแบบทันสมัยอย่างมีสไตล์โดดเด่น เอกลักษณ์เหนือใคร ด้วยการตกแต่งเฟอร์นิเจอร์ มากมายอาทิเช่น\r\n(ฟรี) เฟอร์นิเจอร์ ใหม่!\r\nตู้เสื้อผ้า,โต๊ะอเนกประสงค์,เตียง 6 ฟุต พร้อมที่นอน\r\n(ฟรี) อุปกรณ์อำนวยความสะดวก ใหม่!\r\nแอร์เบอร์ 5 ใหม่,ทีวี จอแบน LED,ตู้เย็น,ไมโครเวฟ,เครื่องทำน้ำอุ่น ฝักบัวrain shower,\r\n-ลิฟท์ขนาดใหญ่\r\n- ที่จอดรถยนต์ และ จักรยานยนต์ระบุที่จอด (VIP PARK)\r\n- อินเทอร์เน็ต WI-FI ฟรีตรง ล๊อบบี้\r\n- ฟิตเนส\r\n- มินิมาร์ท\r\n- เครื่องซักผ้า\r\n-ตู้กดน้ำดื่ม\r\nมีบริการ รปภ. 24 ชม\r\nมหาวิทยาลัยใกล้เคียง มหาลัยเทคโนโลยีราชมงคลธัญบุรี', '0959199800', '@thepeak', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `listing_amenities`
--

CREATE TABLE `listing_amenities` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `code` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listing_amenities`
--

INSERT INTO `listing_amenities` (`id`, `listing_id`, `code`) VALUES
(1, 1, 'aircon'),
(4, 1, 'cctv'),
(3, 1, 'parking'),
(2, 1, 'wifi');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('tenant','owner','admin') DEFAULT 'tenant',
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `phone`, `created_at`) VALUES
(1, 'เกว', 'owner@example.com', '$2y$10$Bp9xg6d6VjN0rPj3m6oIYejyUQk9mF7Kq4vK7c7UqT1k7v5m8y0ZC', 'owner', '02222222222', '2025-10-13 14:09:32'),
(2, 'เปิ้ล', 'tenant@example.com', '$2y$10$Bp9xg6d6VjN0rPj3m6oIYejyUQk9mF7Kq4vK7c7UqT1k7v5m8y0ZC', 'tenant', '0999999', '2025-10-13 14:09:32'),
(8, 'ohm', 'ohm19@gmail.com', '$2y$10$Vl/fUiBWJdpC8fSUvIOyPuSded/UdinClZ3nRF2XRUAGHr0LVy6JS', 'owner', '0896526164', '2025-10-14 08:54:10'),
(9, 'kik', 'ssminicoper@gmail.com', '$2y$10$XBiowjjhSFbmnwMi7omaFu2G9J0lSQgsXgLIvDd4hRLfr7n4O1tfG', 'tenant', '056492313', '2025-10-14 08:55:36'),
(10, 'Pai', 'pai23@gmail.com', '$2y$10$i16jJmiJjO2f1lFADucczunDfeYV9uRebw/lfUUo3dlPlF4dRC2LO', 'tenant', '01797922', '2025-10-14 10:50:24'),
(13, 'mew', 'mew19@gmail.com', '$2y$10$irczGCotv3YVpMtHLSKe8exRCFRYesdMIkZbB/Jwvijb7gT3nljJG', 'owner', '02879641855', '2025-10-14 10:54:20'),
(14, 'kam', 'kam@gmail.com', '$2y$10$6AUKPIo6Qv3F6OezFjhCFO5ey25Z7RnObxrSLu3TIa9geGKfsgo0C', 'tenant', '0958090404', '2025-10-14 16:13:46'),
(15, 'ton', 'ton@gmail.com', '$2y$10$MXUAqkpRqajpCw3cNVBiZOGSXfbdsEBlS94gXhudmY7XMdFUOc0/S', 'owner', '0897856421', '2025-10-16 11:17:07'),
(16, 'Ava', 'Ava@gmail.com', '$2y$10$o70uM0JG1rmxzrcW.u1OweHfO5t/TvEzfrxeDSrZE4.4BUCf1GXdi', 'owner', '0889561651864', '2025-10-16 13:35:03'),
(17, 'sis', 'sis@gmail.com', '$2y$10$Xd0YWMEuIKphwMH7JvhMO.MOuWHl9zsdHUZYIphmZtSMzM0T.vDYa', 'owner', '0658088555', '2025-10-16 13:43:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listing_id` (`listing_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`listing_id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listing_id` (`listing_id`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `listing_amenities`
--
ALTER TABLE `listing_amenities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_listing_code` (`listing_id`,`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `listing_amenities`
--
ALTER TABLE `listing_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`tenant_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `listing_amenities`
--
ALTER TABLE `listing_amenities`
  ADD CONSTRAINT `listing_amenities_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
