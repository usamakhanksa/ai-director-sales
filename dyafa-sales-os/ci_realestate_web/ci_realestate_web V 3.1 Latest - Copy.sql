-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2021 at 02:13 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ci_realestate_web`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `att_id` int(11) NOT NULL,
  `att_name` varchar(255) NOT NULL,
  `att_path` varchar(255) NOT NULL,
  `att_alt` varchar(255) NOT NULL,
  `att_type` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `att_status` enum('Y','N','D') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attachments`
--

INSERT INTO `attachments` (`att_id`, `att_name`, `att_path`, `att_alt`, `att_type`, `user_id`, `file_type`, `att_status`) VALUES
(2, 'IMG_20210609_175725.jpg', 'uploads/documents/', 'IMG_20210609_175725.jpg', 'document', 1, 'image', 'Y'),
(3, 'IMG_20210609_165821.jpg', 'uploads/documents/', 'IMG_20210609_165821.jpg', 'document', 1, 'image', 'Y'),
(5, 'IMG_20210609_165821.jpg', 'uploads/documents/', 'IMG_20210609_165821.jpg', 'document', 1, 'image', 'Y'),
(6, 'IMG_20210609_175706.jpg', 'uploads/documents/', 'IMG_20210609_175706.jpg', 'document', 1, 'image', 'Y'),
(7, 'IMG_20210609_175854.jpg', 'uploads/documents/', 'IMG_20210609_175854.jpg', 'document', 1, 'image', 'Y'),
(8, 'IMG_20210609_180015.jpg', 'uploads/documents/', 'IMG_20210609_180015.jpg', 'document', 1, 'image', 'Y'),
(9, 'IMG_20210609_175725.jpg', 'uploads/documents/', 'IMG_20210609_175725.jpg', 'document', 1, 'image', 'Y'),
(11, '11.jpg', 'uploads/documents/', '11.jpg', 'document', 1, 'image', 'Y'),
(12, '601969.jpg', 'uploads/documents/', '601969.jpg', 'document', 1, 'image', 'Y'),
(13, '24797_furniture_verticalstore-priority3_HerosLandscape_bookcase._CB313283933_.jpg', 'uploads/documents/', '24797_furniture_verticalstore-priority3_HerosLandscape_bookcase._CB313283933_.jpg', 'document', 1, 'image', 'Y'),
(14, '6780479-coffee-shop-wallpaper-hd.jpg', 'uploads/documents/', '6780479-coffee-shop-wallpaper-hd.jpg', 'document', 1, 'image', 'Y'),
(15, '4089180923_312c50fc50_o.jpg', 'uploads/documents/', '4089180923_312c50fc50_o.jpg', 'document', 1, 'image', 'Y'),
(16, 'Auto_Renault_Renault_Duster_026200_.jpg', 'uploads/documents/', 'Auto_Renault_Renault_Duster_026200_.jpg', 'document', 1, 'image', 'Y'),
(17, 'Banner1.jpg', 'uploads/documents/', 'Banner1.jpg', 'document', 1, 'image', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `b_id` int(11) NOT NULL,
  `b_title` varchar(255) NOT NULL,
  `b_image` varchar(255) NOT NULL,
  `created_on` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `b_status` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`b_id`, `b_title`, `b_image`, `created_on`, `created_by`, `b_status`) VALUES
(1, 'some title', '', 1634549284, 1, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `banner_assigned_to`
--

CREATE TABLE `banner_assigned_to` (
  `banner_id` int(11) NOT NULL,
  `assign_type` varchar(255) NOT NULL,
  `assign_id` varchar(255) NOT NULL,
  `for_lang` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `banner_assigned_to`
--

INSERT INTO `banner_assigned_to` (`banner_id`, `assign_type`, `assign_id`, `for_lang`) VALUES
(1, 'static', 'agents', 'en');

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `b_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cat_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `publish_on` int(11) NOT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'draft',
  `seo_meta_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`b_id`, `title`, `short_description`, `description`, `slug`, `image`, `cat_id`, `created_by`, `created_on`, `updated_on`, `publish_on`, `status`, `seo_meta_keywords`, `seo_meta_description`) VALUES
(1, 'Test Blog', 'Something', '<p>Something</p>', 'test-blog', '', 1, 1, 1633938505, 1633939112, 1633890600, 'publish', '', ''),
(2, 'test101', 'some description', '<p>thsi is description</p>', 'test', '', 1, 1, 1634545374, 1634545477, 1634495400, 'publish', 'this is the meta', 'this is the meta description');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `c_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`c_id`, `title`, `slug`, `created_on`, `created_by`, `status`) VALUES
(1, 'Sports', 'sports', 1633934566, 1, 'N'),
(2, 'Green City', 'green_city', 1634545568, 1, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `blog_lang_details`
--

CREATE TABLE `blog_lang_details` (
  `bld_id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_id` int(11) NOT NULL,
  `language` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blog_lang_details`
--

INSERT INTO `blog_lang_details` (`bld_id`, `title`, `short_description`, `description`, `seo_meta_keywords`, `seo_meta_description`, `blog_id`, `language`) VALUES
(1, 'Test Blog', 'Something', '<p>Something</p>', '', '', 1, 'en'),
(2, 'test101', 'some description', '<p>thsi is description</p>', 'this is the meta', 'this is the meta description', 2, 'en');

-- --------------------------------------------------------

--
-- Table structure for table `credits`
--

CREATE TABLE `credits` (
  `credit_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `credit_type` varchar(50) NOT NULL,
  `updated_credit` int(11) NOT NULL,
  `credit_value` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `credit_for` varchar(50) NOT NULL,
  `credit_expires` int(11) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `credits`
--

INSERT INTO `credits` (`credit_id`, `transaction_id`, `credit_type`, `updated_credit`, `credit_value`, `user_id`, `credit_for`, `credit_expires`, `status`, `created_at`, `updated_at`) VALUES
(1, 13, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633608752, 1633608752),
(2, 13, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633608752, 1633608752),
(3, 14, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633608978, 1633608978),
(4, 14, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633608978, 1633608978),
(5, 39, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633692012, 1633692012),
(6, 39, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633692012, 1633692012),
(7, 40, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633692141, 1633692141),
(8, 40, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633692141, 1633692141),
(9, 41, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633692192, 1633692192),
(10, 41, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633692192, 1633692192),
(11, 42, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633692294, 1633692294),
(12, 42, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633692294, 1633692294),
(13, 43, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633692450, 1633692450),
(14, 43, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633692450, 1633692450),
(15, 46, 'post_property', 10, 10, 70, 'property', 0, 'Active', 1633766497, 1633766497),
(16, 46, 'featured_property', 5, 5, 70, 'property', 0, 'Active', 1633766497, 1633766497),
(17, 47, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633773315, 1633773315),
(18, 47, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633773315, 1633773315),
(19, 48, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633773429, 1633773429),
(20, 48, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633773429, 1633773429),
(21, 49, 'post_property', 10, 10, 69, 'property', 0, 'Active', 1633774137, 1633774137),
(22, 49, 'featured_property', 5, 5, 69, 'property', 0, 'Active', 1633774137, 1633774137);

-- --------------------------------------------------------

--
-- Table structure for table `credit_uses`
--

CREATE TABLE `credit_uses` (
  `credit_uses_id` int(11) NOT NULL,
  `credit_id` int(11) NOT NULL,
  `credit_uses_for` varchar(50) NOT NULL,
  `using_id` int(11) NOT NULL,
  `uses_type` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_visits`
--

CREATE TABLE `customer_visits` (
  `visit_id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `visit_requested_on` int(11) NOT NULL,
  `visit_requested_date` varchar(50) NOT NULL,
  `visit_requested_time` varchar(50) NOT NULL,
  `visit_pass_code` varchar(255) DEFAULT NULL,
  `visit_in_time` int(11) DEFAULT NULL,
  `visit_out_time` int(11) DEFAULT NULL,
  `visit_property_id` int(11) NOT NULL,
  `visit_status` varchar(50) NOT NULL,
  `visit_created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer_visits`
--

INSERT INTO `customer_visits` (`visit_id`, `visitor_id`, `visit_requested_on`, `visit_requested_date`, `visit_requested_time`, `visit_pass_code`, `visit_in_time`, `visit_out_time`, `visit_property_id`, `visit_status`, `visit_created_by`) VALUES
(3, 67, 1633505212, '10/09/2021', '01:00 PM', NULL, NULL, NULL, 1, 'pending', 67),
(4, 68, 1633505850, '10/10/2021', '04:00 PM', NULL, NULL, NULL, 1, 'reject', 68),
(5, 67, 1633506626, '11/04/2021', '04:00 PM', NULL, NULL, NULL, 1, 'reject', 67),
(6, 67, 1633506647, '11/12/2021', '12:00 PM', NULL, NULL, NULL, 1, 'pending', 67),
(7, 68, 1633509263, '10/16/2021', '09:00 AM', NULL, NULL, NULL, 1, 'pending', 68),
(8, 67, 1633509274, '10/10/2021', '10:00 AM', NULL, NULL, NULL, 1, 'accept', 67);

-- --------------------------------------------------------

--
-- Table structure for table `favorite_table`
--

CREATE TABLE `favorite_table` (
  `fav_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `craeted_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `lang_id` int(11) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `lang_for` varchar(50) NOT NULL DEFAULT 'back',
  `arabic_iraq` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '',
  `english` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`lang_id`, `keyword`, `lang_for`, `arabic_iraq`, `english`) VALUES
(1, 'india', 'front', '', 'India'),
(2, 'india', 'back', '', 'India'),
(3, 'rajasthan', 'front', '', 'Rajasthan'),
(4, 'rajasthan', 'back', '', 'Rajasthan'),
(5, 'bikaner', 'front', '', 'Bikaner'),
(6, 'bikaner', 'back', '', 'Bikaner'),
(7, 'rampura', 'front', '', 'Rampura'),
(8, 'rampura', 'back', '', 'Rampura'),
(9, '-mp-colony', 'front', '', ' Mp Colony'),
(10, '-mp-colony', 'back', '', ' Mp Colony'),
(11, 'jaipur', 'front', '', 'Jaipur'),
(12, 'jaipur', 'back', '', 'Jaipur'),
(13, 'jodhpur', 'front', '', 'Jodhpur'),
(14, 'jodhpur', 'back', '', 'Jodhpur'),
(15, 'something', 'front', '', 'something'),
(16, 'something', 'back', '', 'something'),
(17, 'Dashboard', 'back', 'لوحة القيادة', 'Dashboard'),
(18, 'Homepage', 'back', 'الصفحة الرئيسية', 'Homepage'),
(19, 'Property', 'back', 'ملكية', 'Property'),
(20, 'Locations', 'back', 'المواقع', 'Locations'),
(21, 'Document', 'back', 'وثيقة', 'Document'),
(22, 'abc', 'front', '', ''),
(23, 'abc', 'back', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `menu_id` int(11) NOT NULL,
  `menu_location` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_meta` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `notif_text` text NOT NULL,
  `notif_icon` varchar(50) NOT NULL,
  `notif_by` int(11) NOT NULL,
  `notif_for` int(11) NOT NULL,
  `notif_on` int(11) NOT NULL,
  `notif_status` enum('U','R','H') NOT NULL DEFAULT 'U',
  `prop_action` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `option_id` int(11) NOT NULL,
  `option_key` varchar(255) NOT NULL,
  `option_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`option_id`, `option_key`, `option_value`) VALUES
(1, 'website_logo_text', 'Real Estate Web'),
(2, 'website_tag_line', 'Best place to show your properties'),
(4, 'fevicon_icon', 'fav.png'),
(5, 'company_address', 'Rajasthan, India'),
(6, 'company_mob', '+91 9876543210, +91 9874563210'),
(7, 'company_tel', '(+91) 9874563210'),
(8, 'company_website', 'http://www.realestateweb.com/'),
(9, 'company_email', 'info@gmail.com'),
(10, 'contact_email', 'contact.realestateweb@gmail.com'),
(11, 'company_lat', '28.0229'),
(12, 'company_lng', '73.3119'),
(13, 'footer_text', '<p>ferer<br></p>'),
(14, 'contact_form_email', 'contact.realestateweb@gmail.com'),
(35, 'social_media', '{\"facebook\":{\"icon\":\"fa-facebook\",\"url\":\"http:\\/\\/www.facebook.com\\/\",\"enable\":\"1\"},\"google_plus\":{\"icon\":\"fa-google-plus\",\"url\":\"http:\\/\\/www.google-plus.com\\/\",\"enable\":\"1\"},\"twitter\":{\"icon\":\"fa-twitter\",\"url\":\"http:\\/\\/www.twitter.com\\/\",\"enable\":\"1\"},\"pinterest\":{\"icon\":\"fa-pinterest\",\"url\":\"http:\\/\\/www.pinterest.com\\/\",\"enable\":\"1\"},\"instagram\":{\"icon\":\"fa-instagram\",\"url\":\"http:\\/\\/www.instagram.com\\/\",\"enable\":\"1\"},\"youtube\":{\"icon\":\"fa-youtube\",\"url\":\"http:\\/\\/www.youtube.com\\/\",\"enable\":\"1\"}}'),
(40, 'website_title', 'ferer'),
(42, 'footer_title_text', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Saepe pariatur reprehenderit vero atque, consequatur id ratione, et non dignissimos culpa? Ut veritatis, quos illum totam quis blanditiis, minima minus odio!'),
(43, 'property_amenities', '{\"indoor_amenities\":[\"Ac\",\"Computer\",\"Heater\",\"Internet\",\"Air Conditions\"],\"outdoor_amenities\":[\"Parking\",\"Grill\",\"Pool\",\"Parks\"]}'),
(44, 'footer_copyright_text', '? Copyright 2021 |  All Rights Reserved. '),
(45, 'copyright_text', '<p>rerer<br></p>'),
(46, 'property_distances', '[\"Bank\",\"Atm\",\"Petrol Pump\",\"Medical\",\"College\",\"School\",\"Hospital\",\"Provison Store\",\"Super Market\",\"Mall\",\"Cinema\"]'),
(47, 'website_logo', 'stripe.png'),
(48, 'company_title', 'App Car Repair'),
(49, 'default_timezone', 'Asia/Kolkata'),
(50, 'currency', 'INR'),
(51, 'default_date_format', 'dd/mm/yyyy'),
(52, 'admin_approval_require_for_property', 'Y'),
(55, 'website_languages', '[]'),
(56, 'language', 'english'),
(57, 'direction', 'ltr'),
(58, 'skin', 'skin-blue-light'),
(63, 'enable_multi_language', 'Y'),
(64, 'site_language', '{\"1\":{\"language\":\"English~en\",\"timezone\":\"Asia\\/Kolkata\",\"direction\":\"ltr\",\"currency\":\"USD\",\"currency_pos\":\"left\",\"status\":\"enable\",\"thousand_sep\":\",\",\"decimal_sep\":\".\",\"num_decimals\":\"2\"},\"2\":{\"language\":\"Arabic (Iraq)~ar-IQ\",\"timezone\":\"Asia\\/Dubai\",\"direction\":\"rtl\",\"currency\":\"AED\",\"currency_pos\":\"left\",\"status\":\"enable\",\"thousand_sep\":\",\",\"decimal_sep\":\".\",\"num_decimals\":\"2\"}}'),
(65, 'default_language', 'English~en'),
(66, 'site_domain', 'mindlogixtech.com'),
(67, 'site_domain_email', 'info@mindlogixtech.com'),
(68, 'enable_property_for_cities', 'Y'),
(69, 'property_for_cities', ''),
(70, 'enable_compare_property', 'Y'),
(71, 'enable_autoupdate_lang_keyword', 'N'),
(74, 'enable_homepage_section', 'Y'),
(75, 'homepage_section', '{\"slider_section\":{\"section_type\":\"fixed\",\"section_key\":\"slider_section\",\"is_enable\":\"Y\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start_slider\":\"no\",\"slider_interval\":\"3000\"},\"search_section\":{\"section_type\":\"fixed\",\"section_key\":\"search_section\",\"is_enable\":\"Y\",\"show_advance_search\":\"yes\"},\"recent_property_section\":{\"section_type\":\"fixed\",\"section_key\":\"recent_property_section\",\"is_enable\":\"Y\",\"heading\":\"Recent Properties\",\"sub_heading\":\"\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"property_type_section\":{\"section_type\":\"fixed\",\"section_key\":\"property_type_section\",\"is_enable\":\"Y\",\"heading\":\"Looking for Property\",\"sub_heading\":\"\",\"no_of_item_in_carousel\":\"3\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\"},\"featured_property_section\":{\"section_type\":\"fixed\",\"section_key\":\"featured_property_section\",\"is_enable\":\"Y\",\"heading\":\"Featured Property\",\"sub_heading\":\"\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"recent_viewed_property_section\":{\"section_type\":\"fixed\",\"section_key\":\"recent_viewed_property_section\",\"is_enable\":\"Y\",\"heading\":\"Recent Viewed Property\",\"sub_heading\":\"\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\"},\"recent_blog_section\":{\"section_type\":\"fixed\",\"section_key\":\"recent_blog_section\",\"is_enable\":\"Y\",\"heading\":\"Recent Blog\",\"sub_heading\":\"\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"properties_section_11\":{\"section_type\":\"dynamic\",\"section_key\":\"properties_section\",\"is_enable\":\"Y\",\"heading\":\"Property in Jodhpur\",\"sub_heading\":\"Property in Jodhpur\",\"property_for\":\"sale\",\"property_type\":\"all\",\"property_for_lang\":\"all\",\"property_country\":\"india~IN\",\"property_state\":\"rajasthan~RJ\",\"property_city\":\"jodhpur~132286\",\"property_zipcode\":\"all\",\"property_sub_area\":\"all\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"properties_section_8\":{\"section_type\":\"dynamic\",\"section_key\":\"properties_section\",\"is_enable\":\"Y\",\"heading\":\"Property in Bikaner\",\"sub_heading\":\"Property in Bikaner\",\"property_for\":\"sale\",\"property_type\":\"all\",\"property_for_lang\":\"all\",\"property_country\":\"india~IN\",\"property_state\":\"rajasthan~RJ\",\"property_city\":\"bikaner~58156\",\"property_zipcode\":\"all\",\"property_sub_area\":\"all\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"properties_section_10\":{\"section_type\":\"dynamic\",\"section_key\":\"properties_section\",\"is_enable\":\"Y\",\"heading\":\"property in jaipur\",\"sub_heading\":\"property in jaipur\",\"property_for\":\"sale\",\"property_type\":\"1\",\"property_for_lang\":\"all\",\"property_country\":\"india~IN\",\"property_state\":\"rajasthan~RJ\",\"property_city\":\"jaipur~132201\",\"property_zipcode\":\"all\",\"property_sub_area\":\"all\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"video_section_12\":{\"section_type\":\"dynamic\",\"section_key\":\"video_section\",\"is_enable\":\"Y\",\"heading\":\"some videos\",\"sub_heading\":\"some videos\",\"video_lang\":\"all\",\"video_url\":[\"https:\\/\\/www.youtube.com\\/watch?v=rJesac0_Ftw\"]},\"video_section_13\":{\"section_type\":\"dynamic\",\"section_key\":\"video_section\",\"is_enable\":\"Y\",\"heading\":\"Test Video\",\"sub_heading\":\"\",\"video_lang\":\"all\",\"video_url\":[\"https:\\/\\/www.youtube.com\\/watch?v=BySuQg4NEGI\"]}}'),
(76, 'enable_test_homepage_section', 'N'),
(77, 'enbale_compare_listing', 'Y'),
(78, 'enbale_map_embed_js', 'Y'),
(79, 'enbale_favourite', 'Y'),
(80, 'enbale_print_priview', 'Y'),
(81, 'enbale_pdf_export', 'Y'),
(82, 'enbale_front_end_registration', 'Y'),
(83, 'enable_property_for_states', 'Y'),
(84, 'enbale_mortgage_calculator', 'Y'),
(85, 'enbale_agent_contact_form', 'Y'),
(86, 'enbale_social_share', 'Y'),
(87, 'property_for_states', ''),
(91, 'site_widgets', '{\"sidebar-property-details\":{\"3\":{\"sidebar_for\":\"sidebar-property-details\",\"sidebar_widget\":\"search-widget\",\"sidebar_options\":{\"enable_advance_search\":\"yes\"}},\"4\":{\"sidebar_for\":\"sidebar-property-details\",\"sidebar_widget\":\"related-properties-widget\",\"sidebar_options\":[]},\"5\":{\"sidebar_for\":\"sidebar-property-details\",\"sidebar_widget\":\"recent-properties-widget\",\"sidebar_options\":[]},\"6\":{\"sidebar_for\":\"sidebar-property-details\",\"sidebar_widget\":\"search-widget\",\"sidebar_options\":{\"widget_id\":\"\",\"enable_advance_search\":\"yes\"}},\"7\":{\"sidebar_for\":\"sidebar-property-details\",\"sidebar_widget\":\"search-widget\",\"sidebar_options\":{\"widget_id\":\"\",\"enable_advance_search\":\"yes\"}}},\"sidebar-page-details\":[{\"sidebar_for\":\"sidebar-page-details\",\"sidebar_widget\":\"search-widget\",\"sidebar_options\":{\"enable_advance_search\":\"yes\"}}],\"sidebar-footer\":[{\"sidebar_for\":\"sidebar-footer\",\"sidebar_widget\":\"search-widget\",\"sidebar_options\":{\"enable_advance_search\":\"yes\"}},{\"sidebar_for\":\"sidebar-footer\",\"sidebar_widget\":\"search-widget\",\"sidebar_options\":{\"enable_advance_search\":\"yes\"}}]}'),
(93, 'custom_styles', '{\"search_form\":{\"selector\":\".form-search \",\"background-color\":\"A1BD6A\"},\"search_form_submit\":{\"selector\":\".form-search .btn-success\",\"color\":\"B0FFB8\",\"background-color\":\"4044BD\",\"border-color\":\"BD60A1\"}}'),
(94, 'enbale_front_end_login', 'N'),
(95, 'enbale_our_agents', 'Y'),
(96, 'no_of_property_in_search_page', '3'),
(97, 'currency_pos', 'left_space'),
(98, 'thousand_sep', ','),
(99, 'decimal_sep', '.'),
(100, 'num_decimals', '2'),
(105, 'primary_menu', '[{\"menu_type\":\"Static Page\",\"deleted\":0,\"new\":1,\"name\":\"Home\",\"id\":\"static~homepage\"},{\"menu_type\":\"Static Page\",\"deleted\":0,\"new\":1,\"name\":\"Sale\",\"id\":\"static~property-for-sale\"},{\"menu_type\":\"Static Page\",\"deleted\":0,\"new\":1,\"name\":\"Properties\",\"id\":\"static~all_properties\",\"children\":[{\"deleted\":0,\"menu_type\":\"Property Type\",\"new\":1,\"name\":\"Apartments\",\"id\":\"property_type~c4ca42381\"},{\"deleted\":0,\"menu_type\":\"Property Type\",\"new\":1,\"name\":\"Flat\",\"id\":\"property_type~c81e728d2\"}]},{\"menu_type\":\"Static Page\",\"deleted\":0,\"new\":1,\"name\":\"Contact Us\",\"id\":\"static~contact\"}]'),
(106, 'footer_menu', '[{\"menu_type\":\"Static Page\",\"deleted\":0,\"new\":1,\"name\":\"Home\",\"id\":\"static~homepage\"},{\"menu_type\":\"Static Page\",\"deleted\":0,\"new\":1,\"name\":\"Sale\",\"id\":\"static~property-for-sale\"},{\"menu_type\":\"Static Page\",\"deleted\":0,\"new\":1,\"name\":\"Rent\",\"id\":\"static~property-for-rent\"},{\"menu_type\":\"Custom Link\",\"deleted\":0,\"new\":1,\"name\":\"Google\",\"id\":\"custom_link~http://www.google.com\"},{\"menu_type\":\"Static Page\",\"deleted\":0,\"new\":1,\"name\":\"Contact Us\",\"id\":\"static~contact\"}]'),
(109, 'login_page_bg_type', 'image'),
(110, 'login_bg_image', 'baseballwithbat-1600.jpg'),
(111, 'login_bg_color', '551832'),
(112, 'enbale_reg_auto_login', 'N'),
(113, 'enbale_reg_img_upload', 'Y'),
(114, 'default_user_status_after_reg', 'N'),
(117, 'payment_methods', '{\"paypal\":{\"is_enable\":\"Y\"},\"site_payment_methods\":{\"paypal\":{\"label_txt\":\"Paypal\",\"paypal_client_id\":\"\",\"paypal_client_secret\":\"\"},\"stripe\":{\"label_txt\":\"Stripe\",\"stripe_client_id\":\"\",\"stripe_client_secret\":\"\"},\"mollie\":{\"label_txt\":\"Mollie\",\"api_key\":\"\"},\"razorpay\":{\"label_txt\":\"Razorpay\",\"razorpay_api_key\":\"\",\"razorpay_api_secret\":\"\"}},\"stripe\":{\"is_enable\":\"Y\"},\"cash_card_window\":{\"is_enable\":\"Y\"},\"options\":{\"cash_card_window\":\"Cash Card Window\"},\"mollie\":{\"is_enable\":\"Y\"},\"razorpay\":{\"is_enable\":\"Y\"}}'),
(118, 'enable_payment_option', 'N'),
(119, 'admin_approval_require_for_blog', 'N'),
(120, 'enbale_watermark_on_media', 'N'),
(121, 'watermark_type', 'text'),
(122, 'watermark_text', 'Real Estate Web'),
(123, 'watermark_text_color', '010101'),
(124, 'watermark_text_font_size', '28'),
(125, 'watermark_image', ''),
(126, 'watermark_text_position', 'center-center'),
(127, 'cookie_text', '<p>We use cookie to give you the best experience of<strong> our website </strong>by browsing your agree to our use of cookies. See Our <a target=\"_blank\" rel=\"nofollow\">Cookie Policy</a>.<br><br></p>'),
(128, 'enable_cookie', 'Y'),
(129, 'enable_subscription', 'Y'),
(130, 'enable_property_posting', 'Y'),
(131, 'enable_featured_property_posting', 'Y'),
(132, 'enable_blog_posting', 'Y'),
(135, 'site_whatsapp_no', '91 9876543210'),
(136, 'site_whatsapp_group_link', 'http://www.chat.whastapp.com/group/12345674879454'),
(137, 'advance_search_min_price', '0'),
(138, 'advance_search_max_price', '10000000'),
(139, 'property_custom_fields', '[{\"title\":\"Abc\",\"slug\":\"_abc\",\"is_req\":\"\"},{\"title\":\"Width\",\"slug\":\"_width\",\"is_req\":\"\"},{\"title\":\"Height\",\"slug\":\"_height\",\"is_req\":\"\"},{\"title\":\"Color\",\"slug\":\"_color\",\"is_req\":\"\"},{\"title\":\"Property Size\",\"slug\":\"_property_size\",\"is_req\":\"\"},{\"title\":\"Property Site\",\"slug\":\"_property_site\",\"is_req\":\"\"}]'),
(140, 'google_analytics_tracking_code', ''),
(141, 'google_analytics_tracking_id', 'ferere'),
(142, 'enable_advance_search', 'Y'),
(143, 'advance_search_price_range', 'Y'),
(144, 'advance_search_bath', 'Y'),
(145, 'advance_search_bed', 'Y'),
(146, 'advance_search_indoor_amenities', 'N'),
(147, 'advance_search_outdoor_amenities', 'N'),
(152, 'site_plugins', '{\"4\":\"payment\",\"5\":\"cookie_consent\",\"6\":\"google_analytics\",\"7\":\"database_backup\",\"8\":\"blog\",\"9\":\"openstreetmap\",\"10\":\"document\",\"12\":\"google_map\",\"13\":\"property_locations\",\"14\":\"property_booking\"}'),
(153, 'media_file_size', '2'),
(154, 'media_file_type_options', 'limited'),
(155, 'media_file_type', '[\"bmp\",\"gif\",\"jpeg\",\"jpg\",\"png\",\"doc\",\"docx\",\"ppt\",\"pptx\",\"xls\",\"xlsx\",\"pdf\",\"txt\"]'),
(156, 'media_image_types', '[{\"title\":\"Thumbnail\",\"width\":\"150\",\"height\":\"150\",\"disable\":true},{\"title\":\"Medium\",\"width\":\"300\",\"height\":\"300\",\"disable\":false}]'),
(157, 'document_file_size', '2'),
(158, 'document_file_type_options', 'all'),
(159, 'document_file_type', '[]'),
(160, 'document_image_types', '[{\"title\":\"Thumbnail\",\"width\":\"150\",\"height\":\"150\",\"disable\":true},{\"title\":\"Medium\",\"width\":\"300\",\"height\":\"300\",\"disable\":false}]'),
(161, 'footer_1_menu', '[{\"menu_type\":\"Page\",\"deleted\":0,\"new\":1,\"name\":\"Terms of Use\",\"id\":\"page~3416a75f29\"},{\"menu_type\":\"Page\",\"deleted\":0,\"new\":1,\"name\":\"Privacy Policy\",\"id\":\"page~a1d0c6e82a\"},{\"menu_type\":\"Page\",\"deleted\":0,\"new\":1,\"name\":\"Listing Quaility Policy\",\"id\":\"page~17e621662b\"}]'),
(163, 'reg_term_of_user_page', '33'),
(164, 'enable_property_soft_delete', 'N'),
(199, 'loc_tax_settings', '{\"country\":{\"tax_type\":\"country\",\"tax_title\":\"Country\",\"tax_parent\":\"root\",\"has_child\":1,\"enabled\":true,\"multi_lang_enabled\":false},\"state\":{\"tax_type\":\"state\",\"tax_title\":\"State\",\"tax_parent\":\"country\",\"has_child\":1,\"enabled\":true,\"multi_lang_enabled\":true},\"city\":{\"tax_type\":\"city\",\"tax_title\":\"City\",\"tax_parent\":\"state\",\"has_child\":2,\"enabled\":true,\"multi_lang_enabled\":true},\"zipcode\":{\"tax_type\":\"zipcode\",\"tax_title\":\"Zipcode\",\"tax_parent\":\"city\",\"has_child\":0,\"enabled\":true,\"multi_lang_enabled\":true},\"sub-area\":{\"tax_type\":\"sub_area\",\"tax_title\":\"Sub-area\",\"tax_parent\":\"city\",\"has_child\":0,\"enabled\":true,\"multi_lang_enabled\":true}}'),
(200, 'locations', '{\"countries\":{\"IN\":{\"loc_title\":\"India\",\"loc_type\":\"country\",\"country_id\":\"1269750\",\"states\":{\"RJ\":{\"loc_title\":\"Rajasthan\",\"loc_type\":\"state\",\"state_id\":\"4014\",\"cities\":{\"58156\":{\"loc_title\":\"Bikaner\",\"loc_type\":\"city\",\"city_id\":\"58156\",\"zipcodes\":[\"334004\",\"334001\"],\"sub_areas\":[\"Rampura\",\" Mp Colony\"]},\"132201\":{\"loc_title\":\"Jaipur\",\"loc_type\":\"city\",\"city_id\":\"132201\"},\"132286\":{\"loc_title\":\"Jodhpur\",\"loc_type\":\"city\",\"city_id\":\"132286\"}}}},\"settings\":{\"languages\":null}},\"GB\":{\"loc_title\":\"United Kingdom\",\"loc_type\":\"country\",\"country_id\":\"2635167\",\"states\":{\"BPL\":{\"loc_title\":\"Blackpool\",\"loc_type\":\"state\",\"state_id\":\"2451\"},\"BOL\":{\"loc_title\":\"Bolton\",\"loc_type\":\"state\",\"state_id\":\"2504\"},\"BUR\":{\"loc_title\":\"Bury\",\"loc_type\":\"state\",\"state_id\":\"2459\"}}},\"US\":{\"loc_title\":\"United States\",\"loc_type\":\"country\",\"country_id\":\"6252001\",\"states\":{\"CA\":{\"loc_title\":\"California\",\"loc_type\":\"state\",\"state_id\":\"1416\",\"cities\":{\"110992\":{\"loc_title\":\"Acalanes Ridge\",\"loc_type\":\"city\",\"city_id\":\"110992\"},\"111001\":{\"loc_title\":\"Acton\",\"loc_type\":\"city\",\"city_id\":\"111001\"},\"111057\":{\"loc_title\":\"Agoura Hills\",\"loc_type\":\"city\",\"city_id\":\"111057\"}}},\"FL\":{\"loc_title\":\"Florida\",\"loc_type\":\"state\",\"state_id\":\"1436\",\"cities\":{\"111255\":{\"loc_title\":\"Alva\",\"loc_type\":\"city\",\"city_id\":\"111255\"},\"111321\":{\"loc_title\":\"Andover\",\"loc_type\":\"city\",\"city_id\":\"111321\"},\"111348\":{\"loc_title\":\"Anna Maria\",\"loc_type\":\"city\",\"city_id\":\"111348\"}}},\"IN\":{\"loc_title\":\"Indiana\",\"loc_type\":\"state\",\"state_id\":\"1440\",\"cities\":{\"111153\":{\"loc_title\":\"Alexandria\",\"loc_type\":\"city\",\"city_id\":\"111153\"},\"111313\":{\"loc_title\":\"Anderson\",\"loc_type\":\"city\",\"city_id\":\"111313\"},\"111667\":{\"loc_title\":\"Austin\",\"loc_type\":\"city\",\"city_id\":\"111667\"}}}}}}}'),
(209, 'email_templates_1', '{\"register_email\":{\"subject\":\"{SITE_TITLE} - {LANG_THANKSIGNUP}\",\"message\":\"<div>Dear Valued <br><\\/div><div><br><\\/div><div>Thanks for creating an account {SITE_TITLE} , <br><\\/div><div><br><\\/div><div>Your username: {USERNAME} <br><\\/div><div>Your password: {PASSWORD} <br><\\/div><div><br><\\/div><div>\\r\\n\\r\\nHave further questions? You can start chat with live support team.\\r\\nSincerely,<\\/div><div><br><\\/div><div>\\r\\n\\r\\n{SITE_TITLE} Team!\\r\\n{SITE_URL}<\\/div>\",\"email_lang\":\"all\"},\"account_conformation_email\":{\"subject\":\"{SITE_TITLE} - {LANG_EMAILCONFIRM} \",\"message\":\"Greetings from {SITE_TITLE} Team!\\r\\n\\r\\nThanks for registering with {SITE_TITLE}. We are thrilled to have you as a registered member and hope that you find our service beneficial.\\r\\n\\r\\nBefore we get you started please activate your account by clicking on the link below\\r\\n{CONFIRMATION_LINK}\\r\\n\\r\\n\\r\\nAfter your Account activation you will have  Post Ad, Chat with sellers and more. Once you have your Profile filled in you are ready to go.\\r\\n\\r\\nHave further questions? You can find answers in our FAQ Section at {LINK_CONTACT}\\r\\nSincerely,\\r\\n\\r\\n{SITE_TITLE} Team!\\r\\n{SITE_URL}\",\"email_lang\":\"ar-IQ\"},\"forgot_password_email\":{\"subject\":\"{SITE_TITLE} - {LANG_FORGOTPASS}\",\"message\":\"{LANG_TORESET}\\r\\n\\r\\n{FORGET_PASSWORD_LINK}\\r\\n\\r\\nHave further questions? You can find answers in our FAQ Section at {LINK_CONTACT}\\r\\nSincerely,\\r\\n\\r\\n{SITE_TITLE} Team!\\r\\n{SITE_URL}\",\"email_lang\":\"all\"},\"contact_us_email\":{\"subject\":\"{SITE_TITLE} - {LANG_FORGOTPASS}\",\"message\":\"{NAME} {LANG_WANT-TO-CONTACT} {SITE_TITLE}:\\r\\n\\r\\n{LANG_NAME}          : {NAME}\\r\\n{LANG_EMAIL}         : {EMAIL}\\r\\n\\r\\n{LANG_MESSAGE}:\\r\\n\\r\\n{MESSAGE}\\r\\n\\r\\n------------------------------------------\\r\\n\\r\\nThis message has been sent automatically by the {SITE_TITLE} system.\\r\\nIf you need to contact us, go to {LINK_CONTACT}\",\"email_lang\":\"all\"},\"property_submission_email\":{\"subject\":\"{LANG_CONTACT_SUBJECT_START} - {CONTACT_SUBJECT}\",\"message\":\"{NAME} {LANG_WANT-TO-CONTACT} {SITE_TITLE}:\\r\\n\\r\\n{LANG_NAME}          : {NAME}\\r\\n{LANG_EMAIL}         : {EMAIL}\\r\\n\\r\\n{LANG_MESSAGE}:\\r\\n\\r\\n{MESSAGE}\\r\\n\\r\\n------------------------------------------\\r\\n\\r\\nThis message has been sent automatically by the {SITE_TITLE} system.\\r\\nIf you need to contact us, go to {LINK_CONTACT}\",\"email_lang\":\"all\"},\"property_approve_email\":{\"subject\":\"Congratulations! {SELLER_NAME} Your property has been approved\",\"message\":\"Congratulations! Your property to {ADTITLE} on {SITE_TITLE} has been approved. You can view your item here:\\r\\n\\r\\n{propperty_link}\\r\\n\\r\\nThanks for your submission!\\r\\n\\r\\nRegards,\\r\\n{SITE_TITLE} Team\\r\\n\\r\\nQuestions? See our Knowledgebase and Support Center at {LINK_CONTACT}\\r\\n\\r\\n------------------------------------------\\r\\n\\r\\nThis message has been sent automatically by the {SITE_TITLE} system.\\r\\nIf you need to contact us, go to {LINK_CONTACT}\",\"email_lang\":\"all\"},\"property_reject_email\":{\"subject\":\"Congratulations! {SELLER_NAME} Your property has been Rejected\",\"message\":\"Congratulations! Your property to {ADTITLE} on {SITE_TITLE} has been rejected.\\r\\n\\r\\n\\r\\nRegards,\\r\\n{SITE_TITLE} Team\\r\\n\\r\\nQuestions? See our Knowledgebase and Support Center at {LINK_CONTACT}\\r\\n\\r\\n------------------------------------------\\r\\n\\r\\nThis message has been sent automatically by the {SITE_TITLE} system.\\r\\nIf you need to contact us, go to {LINK_CONTACT}\",\"email_lang\":\"all\"}}'),
(210, 'disclaimer_message', 'There are a few disclaimers that are regulated by law and mandatory in certain situations, but generally disclaimers are optional and used to benefit business owners.'),
(211, 'register_message', 'You should use disclaimers because they help limit your legal liability and keep your users informed. In some circumstances, you should use disclaimers because they\'re legally required.'),
(214, 'google_map_center_latitude', ''),
(215, 'google_map_center_longitude', ''),
(217, 'enbale_gmail_login', 'N'),
(218, 'enbale_facebook_login', 'N'),
(221, 'seo_settings', '{\"home_page\":{\"meta_keywords\":\"home page mk\",\"meta_description\":\"home page md\"},\"property_for_sale\":{\"meta_keywords\":\"Property for Sale MK\",\"meta_description\":\"Property for Sale MD\"},\"property_for_rent\":{\"meta_keywords\":\"Property for Rent MK\",\"meta_description\":\"Property for Rent MD\"},\"register\":{\"meta_keywords\":\"Register MK\",\"meta_description\":\"Register MD\"},\"property\":{\"meta_keywords\":\"All Property MK\",\"meta_description\":\"All Property MD\"},\"blog\":{\"meta_keywords\":\"All Blog MK\",\"meta_description\":\"All Blog MD\"},\"agent\":{\"meta_keywords\":\"All Agent MK\",\"meta_description\":\"All Agent MD\"}}'),
(222, 'seo_static_page_details', '{\"english_TLD_en\":{\"homepage\":{\"meta_keywords\":\"Properties in Bikaner.Jaipur\",\"meta_description\":\"Properties in Bikaner.Jaipur\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"Search Properties in Bikaner.Jaipur\",\"meta_description\":\"Search Properties in Bikaner.Jaipur\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}},\"arabic_iraq_TLD_ar-IQ\":{\"homepage\":{\"meta_keywords\":\"Properties in Dubai\",\"meta_description\":\"Properties in Dubai\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"Search Properties in Dubai\",\"meta_description\":\"Search Properties in Dubai\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}},\"spanish_TLD_es\":{\"homepage\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}},\"french_TLD_fr\":{\"homepage\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}},\"english_bahamas_TLD_en-BS\":{\"homepage\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}},\"english_united_kingdom_TLD_en-GB\":{\"homepage\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}},\"dutch_flemish_TLD_nl\":{\"homepage\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}},\"spanish_paraguay_TLD_es-PY\":{\"homepage\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}},\"german_TLD_de\":{\"homepage\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"register\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"contact\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"search\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-sale\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"property-for-rent\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"all_properties\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"agents\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blogs\":{\"meta_keywords\":\"\",\"meta_description\":\"\"},\"blog_categories\":{\"meta_keywords\":\"\",\"meta_description\":\"\"}}}'),
(224, 'default_mailer', 'php_mail'),
(225, 'email_templates', '{\"register_email\":{\"subject\":\"{website_title} - Thanks for Register\",\"message\":\"<p>Dear {first_name} {last_name}, thanks for creating an account on {website_title} ,<\\/p>\\r\\n\\r\\n<p>Your username: {user_name}<\\/p>\\r\\n\\r\\n<p>You can difrectly login from :\\u00a0{admin_url}<\\/p>\\r\\n\\r\\n<p>{website_title} Team!<\\/p>\\r\\n\\r\\n<p>{front_url}<\\/p>\\r\\n\"},\"account_confirmation_email\":{\"subject\":\"{website_title} - Account Confirmation Email\",\"message\":\"<p>Greetings from {website_title} Team!<\\/p>\\r\\n\\r\\n<p>Thanks for registering with us. Before you get started please activate your account by clicking on the link below<br \\/>\\r\\n{account_confirmation_link}<\\/p>\\r\\n\\r\\n<p>{forgot_password_link}<\\/p>\\r\\n\\r\\n<p>{forgot_password_link}<\\/p>\\r\\n\\r\\n<p>{account_confirmation_link}<br \\/>\\r\\nAfter your account activation you will be able to continue with theu user panel.<\\/p>\\r\\n\\r\\n<p>Regards<br \\/>\\r\\n{website_title} Team!<\\/p>\\r\\n\\r\\n<p>{front_url}<\\/p>\\r\\n\"},\"forgot_password_email\":{\"subject\":\"{website_title} - Forgot Password Email\",\"message\":\"<p>Hello {first_name} {last_name},<\\/p>\\r\\n\\r\\n<p>you seems like forgot your password for your account. Please click on folowing link to reset your password.<\\/p>\\r\\n\\r\\n<p>{forgot_password_link}<\\/p>\\r\\n\\r\\n<p>Regards<br \\/>\\r\\n{website_title} Team!<\\/p>\\r\\n\\r\\n<p>{front_url}<\\/p>\\r\\n\"},\"contact_us_email\":{\"subject\":\"{website_title} - Thanks for Contact us\",\"message\":\"<p>A contact form submitted, here are the details, sent to us:<\\/p>\\r\\n\\r\\n<p>Contact Name \\u00a0 \\u00a0 \\u00a0 \\u00a0 \\u00a0: {contact_name}<br \\/>\\r\\nContact Email \\u00a0 \\u00a0 \\u00a0 \\u00a0 : {contact_email}<br \\/>\\r\\nSubject\\u00a0\\u00a0 \\u00a0\\u00a0\\u00a0 \\u00a0 \\u00a0 \\u00a0 \\u00a0 \\u00a0 \\u00a0: {contact_subject}<\\/p>\\r\\n\\r\\n<p>Message:<\\/p>\\r\\n\\r\\n<p>{contact_message}<\\/p>\\r\\n\\r\\n<p><br \\/>\\r\\nThanks for contact us.<\\/p>\\r\\n\\r\\n<p>{website_title} Team!<\\/p>\\r\\n\\r\\n<p>{front_url}<\\/p>\\r\\n\"},\"property_submission_email\":{\"subject\":\"{website_title} - Property Submitted Successfully\",\"message\":\"<p>Thanks for submit your property on our website.\\u00a0<\\/p>\\r\\n\\r\\n<p>Here is the direct link \\u00a0of your property\\u00a0{property_title_linkable}\\u00a0for direct access.<\\/p>\\r\\n\\r\\n<p>{website_title} Team!<\\/p>\\r\\n\\r\\n<p>{front_url}<\\/p>\\r\\n\"},\"property_approve_email\":{\"subject\":\"{website_title} - Property Approved Successfully\",\"message\":\"<p>Congratulations! Your property {property_title} on {website_title} has been approved.<\\/p>\\r\\n\\r\\n<p>Here is the direct link \\u00a0of your property\\u00a0{property_title_linkable}\\u00a0for direct access.<br \\/>\\r\\n<br \\/>\\r\\n{property_title_linkable}<br \\/>\\r\\n<br \\/>\\r\\nThanks for your submission!<br \\/>\\r\\n<br \\/>\\r\\nRegards,<\\/p>\\r\\n\\r\\n<p>{website_title} Team!<\\/p>\\r\\n\\r\\n<p>{front_url}<\\/p>\\r\\n\"},\"property_reject_email\":{\"subject\":\"{website_title} - Property Submission Rejected\",\"message\":\"<p>Oops! Your property \\u00a0{property_title} on {website_title} has been rejected.<\\/p>\\r\\n\\r\\n<p>{website_title} Team!<\\/p>\\r\\n\\r\\n<p>{front_url}<\\/p>\\r\\n\"},\"account_confirmed_email\":{\"subject\":\"{website_title} - Account Confirmed \",\"message\":\"<p>Greetings from {website_title} Team!<br \\/>\\r\\n<br \\/>\\r\\nThanks for joining with us.\\u00a0Hope you will like our service.<br \\/>\\r\\n<br \\/>\\r\\nYour account is confirmed, now you can continue with user panel, {admin_url}<br \\/>\\r\\n<br \\/>\\r\\nRegards<br \\/>\\r\\n{website_title} Team!<br \\/>\\r\\n<br \\/>\\r\\n{front_url}<\\/p>\\r\\n\"},\"contact_us_email_admin\":{\"subject\":\"{website_title} - Contact Email Sent\",\"message\":\"<p>Dear admin {website_title} a contact form submitted, here are the details:<\\/p>\\r\\n\\r\\n<p>Contact Name \\u00a0 \\u00a0 \\u00a0 \\u00a0 \\u00a0: {contact_name}<br \\/>\\r\\nContact Email \\u00a0 \\u00a0 \\u00a0 \\u00a0 : {contact_email}<br \\/>\\r\\nSubject\\u00a0\\u00a0 \\u00a0\\u00a0\\u00a0 \\u00a0 \\u00a0 \\u00a0 \\u00a0 \\u00a0 \\u00a0: {contact_subject}<\\/p>\\r\\n\\r\\n<p>Message:<\\/p>\\r\\n\\r\\n<p>{contact_message}<\\/p>\\r\\n\"},\"new_user_registered_email_admin\":{\"subject\":\"{website_title} - New User Registered\",\"message\":\"<p>A new User has been Registered on\\u00a0{website_title}<\\/p>\\r\\n\\r\\n<p>Please check by login\\u00a0{admin_url}<\\/p>\\r\\n\\r\\n<p>\\u00a0<\\/p>\\r\\n\"},\"property_submission_email_admin\":{\"subject\":\"{website_title} - New Property Submitted\",\"message\":\"<p>A new Property has been Submitted on\\u00a0{website_title}<\\/p>\\r\\n\\r\\n<p>Please check by login\\u00a0{admin_url}<\\/p>\\r\\n\"},\"property_submitted_approval_email\":{\"subject\":\"{website_title} - Property Submitted for Approval\",\"message\":\"<p>Hurrah,\\u00a0your property \\u00a0{property_title} on {website_title} has been submitted for approval by admin. You will get notifications when your property get approved or status change.<br \\/>\\r\\n<br \\/>\\r\\nRegards,<br \\/>\\r\\n{website_title} Team!<br \\/>\\r\\n<br \\/>\\r\\n{front_url}<\\/p>\\r\\n\"}}'),
(226, 'site_modules', '{\"customer_visit_and_review_system\":{\"plugin_name\":\"Customer Visit Booking and Review System\",\"plugin_uri\":\"http:\\/\\/www.google.com\",\"plugin_version\":\"0.1\",\"plugin_description\":\"this is help customer to make visit site and add review\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"status\":\"Y\"},\"google_analytics\":{\"plugin_name\":\"Google Analytics\",\"plugin_uri\":\"http:\\/\\/www.google.com\",\"plugin_version\":\"0.1\",\"plugin_description\":\"Google Analytics as Graph\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"status\":\"Y\"},\"google_recatpcha\":{\"plugin_name\":\"google captcha\",\"plugin_uri\":\"http:\\/\\/www.google.com\",\"plugin_version\":\"0.1\",\"plugin_description\":\"Google captcha to Prevent the Boats\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"status\":\"Y\"},\"locations\":{\"plugin_name\":\"Locations\",\"plugin_uri\":\"http:\\/\\/www.mindlogixtech.com\",\"plugin_version\":\"0.1\",\"plugin_description\":\"Locations based on differend region.\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"status\":\"Y\"},\"mollie\":{\"plugin_name\":\"Mollie Payment Getway\",\"plugin_uri\":\"http:\\/\\/www.imranali.com\",\"plugin_version\":\"0.1\",\"plugin_description\":\"Mollie Payment Getway.\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"status\":\"N\"},\"paypal\":{\"plugin_name\":\"Paypal Payment Getway\",\"plugin_uri\":\"http:\\/\\/www.mindlogixtech.com\",\"plugin_version\":\"0.1\",\"plugin_description\":\"Paypal Payment Getway.\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.mindlogixtech.com\\/mindlogixtech\",\"status\":\"Y\"},\"property_custom_fields\":{\"plugin_name\":\"Custom Fields for Properties\",\"plugin_uri\":\"http:\\/\\/www.mindlogixtech.com\",\"plugin_version\":\"1.0\",\"plugin_description\":\"Extend Properties with additional custom fields\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.mindlogixtech.com\",\"status\":\"N\"},\"property_documents\":{\"plugin_name\":\"Documents for Properties\",\"plugin_uri\":\"http:\\/\\/www.mindlogixtech.com\",\"plugin_version\":\"1.0\",\"plugin_description\":\"Extend Properties with Documents\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.mindlogixtech.com\",\"status\":\"Y\"},\"razorpay\":{\"plugin_name\":\"Razorpay Payment Getway\",\"plugin_uri\":\"http:\\/\\/www.mindlogixtech.com\",\"plugin_version\":\"1.0\",\"plugin_description\":\"RazorPay Payment Getaway.\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.mindlogixtech.com\\/\",\"status\":\"Y\"},\"rental_property_booking\":{\"plugin_name\":\"Property Booking for Rental Properties\",\"plugin_uri\":\"http:\\/\\/www.google.com\",\"plugin_version\":\"0.1\",\"plugin_description\":\"Provide Booking for Rental Property.\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"status\":\"Y\"},\"stripe\":{\"plugin_name\":\"Stripe Payment Getway\",\"plugin_uri\":\"http:\\/\\/www.imranali.com\",\"plugin_version\":\"0.1\",\"plugin_description\":\"Stripe Payment Getway.\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"status\":\"N\"},\"submit_property\":{\"plugin_name\":\"Add Property\",\"plugin_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"plugin_version\":\"0.1\",\"plugin_description\":\"Add Property from Front End\",\"plugin_author\":\"Mindlogixtech\",\"plugin_author_uri\":\"http:\\/\\/www.facebook.com\\/mindlogixtech\",\"status\":\"Y\"}}'),
(227, 'site_payment_methods', '{\"site_payment_methods\":{\"cash_card_window\":{\"is_enable\":\"Y\",\"label_txt\":\"Cash Card Window1\"},\"paypal\":{\"is_enable\":\"Y\",\"label_txt\":\"Paypal\",\"paypal_client_id\":\"AXe5rRDokZG7jzBbLho1wqZ_Um6temPBpQpklPb1OQEsR28Nm4IB6-oUFyin\",\"paypal_client_secret\":\"EAP49xAGosw8lGbsNSVl-w7Sm_bMrNjBjXfR3enDuC5NvZGrwmFyMuh-LM6o\"},\"razorpay\":{\"is_enable\":\"Y\",\"label_txt\":\"Razorpay\",\"razorpay_api_key\":\"rzp_test_5yLvkiw1U8RJDK\",\"razorpay_api_secret\":\"rFoB2mV49bgXWtnV4G9s57X1\"}}}'),
(228, 'payment_methods_test', '{\"paypal\":{\"is_enable\":\"Y\"},\"site_payment_methods\":{\"paypal\":{\"label_txt\":\"Paypal\",\"paypal_client_id\":\"\",\"paypal_client_secret\":\"\"},\"stripe\":{\"label_txt\":\"Stripe\",\"stripe_client_id\":\"\",\"stripe_client_secret\":\"\"},\"mollie\":{\"label_txt\":\"Mollie\",\"api_key\":\"\"},\"razorpay\":{\"label_txt\":\"Razorpay\",\"razorpay_api_key\":\"\",\"razorpay_api_secret\":\"\"}},\"stripe\":{\"is_enable\":\"Y\"},\"cash_card_window\":{\"is_enable\":\"Y\"},\"options\":{\"cash_card_window\":\"Cash Card Window\"},\"mollie\":{\"is_enable\":\"Y\"},\"razorpay\":{\"is_enable\":\"Y\"}}'),
(230, 'enable_google_map_js_api', 'N'),
(231, 'google_map_js_api_key', ''),
(232, 'google_login_client_id', ''),
(233, 'google_login_client_secret', ''),
(234, 'facebook_login_app_id', ''),
(235, 'facebook_login_app_secret', ''),
(236, 'recaptcha_site_key', ''),
(237, 'recaptcha_secret_key', ''),
(238, 'email_setting', '{\"smtp_host\":\"\",\"smtp_port\":\"\",\"smtp_username\":\"\",\"smtp_password\":\"\",\"smtp_encryption\":\"off\",\"smtp_auth\":\"false\"}'),
(239, 'homepage_section_copy', '{\"slider_section\":{\"is_enable\":\"Y\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start_slider\":\"no\",\"slider_interval\":\"3000\"},\"search_section\":{\"is_enable\":\"Y\",\"show_advance_search\":\"yes\"},\"recent_viewed_property_section\":{\"is_enable\":\"Y\",\"heading\":\"Recent Viewed Property\",\"sub_heading\":\"\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\"},\"recent_property_section\":{\"is_enable\":\"Y\",\"heading\":\"Recent Properties\",\"sub_heading\":\"\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"property_type_section\":{\"is_enable\":\"Y\",\"heading\":\"Looking for Property\",\"sub_heading\":\"\",\"no_of_item_in_carousel\":\"3\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\"},\"featured_property_section\":{\"is_enable\":\"Y\",\"heading\":\"Featured Property\",\"sub_heading\":\"\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"recent_blog_section\":{\"is_enable\":\"Y\",\"heading\":\"Recent Blog\",\"sub_heading\":\"\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"properties_section_8\":{\"section_type\":\"dynamic\",\"is_enable\":\"Y\",\"heading\":\"Apartment in India for English\",\"sub_heading\":\"\",\"property_for\":\"sale\",\"property_type\":\"all\",\"property_for_lang\":\"en\",\"property_country\":\"all\",\"property_state\":\"all\",\"property_city\":\"all\",\"property_zipcode\":\"all\",\"property_sub_area\":\"all\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"properties_section_14\":{\"section_type\":\"dynamic\",\"is_enable\":\"Y\",\"heading\":\"Flat in India for English\",\"sub_heading\":\"\",\"property_for\":\"sale\",\"property_type\":\"all\",\"property_for_lang\":\"en\",\"property_country\":\"all\",\"property_state\":\"all\",\"property_city\":\"all\",\"property_zipcode\":\"all\",\"property_sub_area\":\"all\",\"show_as\":\"grid\",\"no_of_item_in_grid_list\":\"6\",\"no_of_item_in_carousel\":\"6\",\"show_nav\":\"yes\",\"show_nav_dots\":\"yes\",\"auto_start\":\"no\",\"carousel_interval\":\"5000\",\"show_view_more\":\"yes\"},\"video_section_10\":{\"section_type\":\"video\",\"is_enable\":\"Y\",\"heading\":\"Videos for Arabic Language\",\"sub_heading\":\"\",\"video_lang\":\"ar-IQ\",\"video_url\":[\"https:\\/\\/www.youtube.com\\/watch?v=MkUIQm--UfA\"]},\"video_section_11\":{\"section_type\":\"video\",\"is_enable\":\"Y\",\"heading\":\"Videos for English Language\",\"sub_heading\":\"\",\"video_lang\":\"en\",\"video_url\":[\"https:\\/\\/www.youtube.com\\/watch?v=ZgcIwHoLGKE\",\"https:\\/\\/www.youtube.com\\/watch?v=ZxmZyY2s0kU\"]},\"video_section_12\":{\"section_type\":\"video\",\"is_enable\":\"Y\",\"heading\":\"Videos for French Language\",\"sub_heading\":\"\",\"video_lang\":\"fr\",\"video_url\":[\"https:\\/\\/www.youtube.com\\/watch?v=WjRIWjpn_1c\"]},\"video_section_13\":{\"section_type\":\"video\",\"is_enable\":\"Y\",\"heading\":\"Videos for Spanish Language\",\"sub_heading\":\"\",\"video_lang\":\"es\",\"video_url\":[\"https:\\/\\/www.youtube.com\\/watch?v=pcXJZ79xHQM\"]}}'),
(240, 'rental_property_booking', '1.0');

-- --------------------------------------------------------

--
-- Table structure for table `options_lang_details`
--

CREATE TABLE `options_lang_details` (
  `sld_id` int(11) NOT NULL,
  `lang_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opt_id` int(11) NOT NULL,
  `language` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `options_lang_details`
--

INSERT INTO `options_lang_details` (`sld_id`, `lang_text`, `opt_id`, `language`) VALUES
(1, '<p>ferer<br></p>', 13, 'en'),
(2, '<p>rerer<br></p>', 45, 'en'),
(3, '<p>We use cookie to give you the best experience of<strong> our website </strong>by browsing your agree to our use of cookies. See Our <a target=\"_blank\" rel=\"nofollow\">Cookie Policy</a>.<br><br></p>', 127, 'en'),
(4, '<p>\r\n</p><p>something</p>\r\n\r\n<br><p></p>', 13, 'ar-IQ'),
(5, '<p>something</p>', 45, 'ar-IQ'),
(6, '<p>We use cookie to give you the best experience of<strong> our website </strong>by browsing your agree to our use of cookies. See Our <a target=\"_blank\" rel=\"nofollow\">Cookie Policy</a>.<br><br></p>', 127, 'ar-IQ');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `package_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_key` varchar(50) NOT NULL,
  `order_details` text NOT NULL,
  `order_price` varchar(50) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `payment_status` varchar(50) NOT NULL,
  `order_status` varchar(50) NOT NULL,
  `order_created_on` int(11) NOT NULL,
  `order_updated_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `transaction_id`, `package_id`, `customer_id`, `order_key`, `order_details`, `order_price`, `payment_method`, `payment_status`, `order_status`, `order_created_on`, `order_updated_on`) VALUES
(8, 49, 2, 69, 'I7CQiuIt7UYcju', '{\"subscription\":{\"title\":\"Subscription\",\"details\":\"\"},\"post_property\":{\"title\":\"Property Posting\",\"details\":\"\"},\"featured_property\":{\"title\":\"Featured Property Posting\",\"details\":\"\"},\"post_blog\":{\"title\":\"Blog Posting\",\"details\":\"\"}}', '150', 'razorpay', 'captured', 'captured', 1633774137, 1633774137),
(9, NULL, 2, 69, 'uacoy2ofaroziplk', '{\"subscription\":{\"title\":\"Subscription\",\"details\":\"\"},\"post_property\":{\"title\":\"Property Posting\",\"details\":\"\"},\"featured_property\":{\"title\":\"Featured Property Posting\",\"details\":\"\"},\"post_blog\":{\"title\":\"Blog Posting\",\"details\":\"\"}}', '150', 'cash_card_window', 'pending', 'temp_order', 1633774859, 1633774859);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_price` int(11) NOT NULL,
  `package_currency` varchar(50) NOT NULL,
  `package_life` varchar(50) NOT NULL,
  `package_type` varchar(150) NOT NULL,
  `applicable_for` varchar(50) NOT NULL,
  `purchase_limit` int(11) NOT NULL,
  `purchase_button_text` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_order` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_name`, `package_price`, `package_currency`, `package_life`, `package_type`, `applicable_for`, `purchase_limit`, `purchase_button_text`, `package_order`, `created_at`, `updated_at`) VALUES
(1, 'basic package', 12, 'INR', '0 days', 'topup', 'owner', 5, 'Buy Now', 0, 1629199740, 1629201082),
(2, 'Basic package for agents', 150, 'USD', '0 days', 'topup', 'agent', 5, 'Buy Now', 1, 1633591043, 1633591043);

-- --------------------------------------------------------

--
-- Table structure for table `package_features`
--

CREATE TABLE `package_features` (
  `feature_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `feature_for` varchar(255) NOT NULL,
  `feature_type` varchar(255) NOT NULL,
  `feature_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `package_features`
--

INSERT INTO `package_features` (`feature_id`, `package_id`, `feature_for`, `feature_type`, `feature_value`) VALUES
(3, 1, 'property', 'post_property', '5'),
(4, 1, 'property', 'featured_property', '2'),
(5, 2, 'property', 'post_property', '10'),
(6, 2, 'property', 'featured_property', '5');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `page_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_sidebar` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `created_by` int(11) NOT NULL,
  `created_on` int(11) NOT NULL,
  `updated_on` int(11) NOT NULL,
  `page_status` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `seo_meta_keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_id`, `page_title`, `page_content`, `page_slug`, `page_sidebar`, `created_by`, `created_on`, `updated_on`, `page_status`, `seo_meta_keywords`, `seo_meta_description`) VALUES
(40, 'about us for', '<p>about us</p>', 'about-us-for-1', 'no', 1, 1592916739, 1592916739, 'Y', '', ''),
(33, 'Single Lang Page', '<p>single lang page Description there are no more words to describe it sorry </p>', 'single-lang-page', 'no', 1, 1576134898, 1578470481, 'Y', '', ''),
(36, 'About Us', '<p>About Us</p>', 'about-us-for', 'no', 1, 1579336351, 1592378153, 'Y', '', ''),
(41, 'Terms of Use', '<p><span xss=removed>Terms of Use</span>  Goes here</p>', 'terms-of-use', 'no', 1, 1601384817, 1601384817, 'Y', '', ''),
(42, 'Privacy Policy', '<p><span xss=removed>Privacy Policy</span> goes here</p>', 'privacy-policy', 'no', 1, 1601384832, 1601384832, 'Y', '', ''),
(43, 'Listing Quaility Policy', '<p><span xss=removed>Listing Quaility Policy</span> goes here</p>', 'listing-quaility-policy', 'no', 1, 1601384847, 1601384847, 'Y', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `page_lang_details`
--

CREATE TABLE `page_lang_details` (
  `pld_id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_keywords` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` int(11) NOT NULL,
  `language` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `page_lang_details`
--

INSERT INTO `page_lang_details` (`pld_id`, `title`, `description`, `seo_meta_keywords`, `seo_meta_description`, `page_id`, `language`) VALUES
(1, 'About Us', '<p>About Us</p>', '', '', 36, 'en'),
(3, 'About Us arabic', '<p>About Us arabic</p>', '', '', 36, 'ar-IQ'),
(10, 'about us for', '<p>about us</p>', '', '', 40, 'en'),
(11, 'Terms of Use', '<p><span xss=removed>Terms of Use</span>  Goes here</p>', '', '', 41, 'en'),
(12, 'Privacy Policy', '<p><span xss=removed>Privacy Policy</span> goes here</p>', '', '', 42, 'en'),
(13, 'Listing Quaility Policy', '<p><span xss=removed>Listing Quaility Policy</span> goes here</p>', '', '', 43, 'en');

-- --------------------------------------------------------

--
-- Table structure for table `post_images`
--

CREATE TABLE `post_images` (
  `image_id` int(11) NOT NULL,
  `parent_image_id` int(11) NOT NULL DEFAULT '0',
  `image_name` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image_type` varchar(255) DEFAULT NULL,
  `post_type` varchar(255) DEFAULT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `post_images`
--

INSERT INTO `post_images` (`image_id`, `parent_image_id`, `image_name`, `image_path`, `image_type`, `post_type`, `image_alt`, `user_id`) VALUES
(46, 0, '11-2.jpg', 'uploads/media/', 'original', 'media', '11-2.jpg', 1),
(47, 46, '11-2-500X300.jpg', 'uploads/media/', 'medium', 'media', '11-2.jpg', 1),
(48, 46, '11-2-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', '11-2.jpg', 1),
(49, 0, 'Teja-garden.jpg', 'uploads/media/', 'original', 'media', 'Teja-garden.jpg', 1),
(50, 49, 'Teja-garden-500X300.jpg', 'uploads/media/', 'medium', 'media', 'Teja-garden.jpg', 1),
(51, 49, 'Teja-garden-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', 'Teja-garden.jpg', 1),
(52, 0, 'Teja-garden-1.jpg', 'uploads/media/', 'original', 'media', 'Teja-garden-1.jpg', 1),
(53, 52, 'Teja-garden-1-500X300.jpg', 'uploads/media/', 'medium', 'media', 'Teja-garden-1.jpg', 1),
(54, 52, 'Teja-garden-1-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', 'Teja-garden-1.jpg', 1),
(55, 0, 'Navayatown.jpg', 'uploads/media/', 'original', 'media', 'Navayatown.jpg', 1),
(56, 55, 'Navayatown-500X300.jpg', 'uploads/media/', 'medium', 'media', 'Navayatown.jpg', 1),
(57, 55, 'Navayatown-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', 'Navayatown.jpg', 1),
(58, 0, 'Vision-park.jpg', 'uploads/media/', 'original', 'media', 'Vision-park.jpg', 1),
(59, 58, 'Vision-park-500X300.jpg', 'uploads/media/', 'medium', 'media', 'Vision-park.jpg', 1),
(60, 58, 'Vision-park-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', 'Vision-park.jpg', 1),
(61, 0, '3-2.jpg', 'uploads/media/', 'original', 'media', '3-2.jpg', 1),
(62, 61, '3-2-500X300.jpg', 'uploads/media/', 'medium', 'media', '3-2.jpg', 1),
(63, 61, '3-2-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', '3-2.jpg', 1),
(64, 0, 'castle-gbd409f76e_1280.jpg', 'uploads/media/', 'original', 'media', 'castle-gbd409f76e_1280.jpg', 1),
(65, 64, 'castle-gbd409f76e_1280-500X300.jpg', 'uploads/media/', 'medium', 'media', 'castle-gbd409f76e_1280.jpg', 1),
(66, 64, 'castle-gbd409f76e_1280-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', 'castle-gbd409f76e_1280.jpg', 1),
(67, 0, 'buildings-g32b81aa04_1280.jpg', 'uploads/media/', 'original', 'media', 'buildings-g32b81aa04_1280.jpg', 1),
(68, 67, 'buildings-g32b81aa04_1280-500X300.jpg', 'uploads/media/', 'medium', 'media', 'buildings-g32b81aa04_1280.jpg', 1),
(69, 67, 'buildings-g32b81aa04_1280-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', 'buildings-g32b81aa04_1280.jpg', 1),
(70, 0, 'city-gf862cac6c_1280.jpg', 'uploads/media/', 'original', 'media', 'city-gf862cac6c_1280.jpg', 1),
(71, 70, 'city-gf862cac6c_1280-500X300.jpg', 'uploads/media/', 'medium', 'media', 'city-gf862cac6c_1280.jpg', 1),
(72, 70, 'city-gf862cac6c_1280-300X300.jpg', 'uploads/media/', 'thumbnail', 'media', 'city-gf862cac6c_1280.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `p_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `street_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `long` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_feat` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `property_for` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_type` int(11) NOT NULL,
  `bedroom` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bathroom` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `garage` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `indoor_amenities` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `outdoor_amenities` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `distance_list` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `video_urls` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_images` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `deleted` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `seo_meta_keywords` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `_property_site` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `_property_banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `_property_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `_width` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `_height` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `_abc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`p_id`, `title`, `slug`, `short_description`, `description`, `address`, `street_address`, `country`, `state`, `city`, `zip_code`, `sub_area`, `lat`, `long`, `price`, `size`, `is_feat`, `property_for`, `property_type`, `bedroom`, `bathroom`, `garage`, `indoor_amenities`, `outdoor_amenities`, `distance_list`, `video_urls`, `property_images`, `created_on`, `created_by`, `status`, `deleted`, `seo_meta_keywords`, `seo_meta_description`, `_property_site`, `_property_banner`, `_property_size`, `_color`, `_width`, `_height`, `_abc`) VALUES
(1, 'Tauju  Apartments', 'tauju', 'this is short Description', '<p>this is main Description</p>', 'Near Old Gajner Road', '', 'india', 'rajasthan', 'bikaner', '334004', '', '', '', '500', '1200~Sq Feet', 'Y', 'rent', 1, '3', '2', '1', '[\"Ac\",\"Computer\",\"Heater\",\"Internet\",\"Air Conditions\"]', '[\"Parking\",\"Grill\",\"Pool\"]', '[]', 'https://www.youtube.com/watch?v=O9tl1enYmCU,https://www.youtube.com/watch?v=LSQsNeH2WLc,https://www.youtube.com/watch?v=ZgcIwHoLGKE', '1,4,7,10,25,28,31,34,70', 1629205492, 1, 'publish', 'N', 'Test Property, Test', 'this is meta Description', '', '', '', '', '', '', ''),
(3, 'Vision park', 'vision-park', 'The expansive backyard includes a sparkling pool', '<p>central heating and cooling, and a convenient washer/dryer hook-up. Beyond a functional entryway space the home flows into a luminous, open- concept living, dining, and kitchen area.</p>', 'Rampura Basti', '', 'india', 'rajasthan', 'bikaner', '334004', 'Rampura', '', '', '150', '100~Sq Feet', 'N', 'sale', 1, '5', '4', '2', '[\"Heater\",\"Internet\",\"Air Conditions\"]', '[\"Parking\",\"Pool\",\"Parks\"]', '{\"Bank\":{\"direction\":\"East\",\"distance\":\"100\",\"distance_text\":\"Meter\"},\"Atm\":{\"direction\":\"North\",\"distance\":\"200\",\"distance_text\":\"Meter\"},\"Petrol Pump\":{\"direction\":\"West\",\"distance\":\"100\",\"distance_text\":\"Meter\"},\"Medical\":{\"direction\":\"North\",\"distance\":\"400\",\"distance_text\":\"Meter\"},\"College\":{\"direction\":\"West\",\"distance\":\"2\",\"distance_text\":\"KM\"},\"School\":{\"direction\":\"West\",\"distance\":\"1.2\",\"distance_text\":\"KM\"},\"Hospital\":{\"direction\":\"North-East\",\"distance\":\"300\",\"distance_text\":\"Meter\"},\"Provison Store\":{\"direction\":\"South\",\"distance\":\"2\",\"distance_text\":\"KM\"},\"Super Market\":{\"direction\":\"North\",\"distance\":\"1\",\"distance_text\":\"KM\"},\"Mall\":{\"direction\":\"North-East\",\"distance\":\"100\",\"distance_text\":\"Meter\"},\"Cinema\":{\"direction\":\"West\",\"distance\":\"200\",\"distance_text\":\"Meter\"}}', '', '58', 1633762041, 69, 'publish', 'N', '', '', '', '', '', '', '', '', ''),
(4, 'TEJA GARDEN', 'teja-gareden', 'Luxurious and upgraded, this 4 bedroom, 4.5 bathroom home of 5,281 sq. ft.', '<p><em>Stunning large late 80’s contemporary home with soaring ceilings and windows, split levels, great floor plan including open dining and living room. Located in the beautiful hilly and treed, desirable Windmill Hill section of Desoto you are conveniently located to shops, dining, and 20 minutes to downtown Dallas. </em></p>', 'Rani Bazar', '', 'india', 'rajasthan', 'jaipur', '', '', '', '', '110', '150~Sq Feet', 'Y', 'sale', 1, '30', '50', '50', '[\"Ac\",\"Heater\",\"Internet\"]', '[\"Parking\",\"Pool\",\"Parks\"]', '[]', '', '49', 1633762097, 69, 'publish', 'N', '', '', '', '', '', '', '', '', ''),
(6, 'Navaya town', 'navaya-town', 'The expansive backyard includes a sparkling pool', '<p>Contemporary amenities include solar PV and a Tesla EV charging station. The expansive backyard includes a sparkling pool and spa plus a comfortable poolhouse all in private, verdant surroundings. You’ll appreciate the short drive to downtown Los Altos, Rancho Shopping Center, access to Interstate 280, and numerous parks and preserves.</p>', 'Jaipur road', '', 'india', 'rajasthan', 'jodhpur', '', '', '', '', '100', '50~Sq Feet', 'Y', 'sale', 2, '4', '3', '2', '[\"Computer\",\"Air Conditions\"]', '[\"Parking\",\"Parks\"]', '[]', '', '55', 1634124445, 69, 'publish', 'N', '', '', '', '', '', '', '', '', ''),
(7, 'Green House Flat', 'green-flats', 'The Green House Flat', '<p>Flat in your town Green flat for Families ....</p>', 'Opposite the More money Bank pvt ltd', '', 'india', 'rajasthan', 'bikaner', '334001', 'Rampura', '', '', '500', '2500~Sq Feet', 'N', 'sale', 1, '5', '3', '1', '[\"Ac\",\"Internet\"]', '[\"Grill\",\"Pool\"]', '{\"North-West\":[{\"title\":\"RK Batteries\",\"entity\":\"Super Market\",\"measurement\":\".5\",\"measurement_type\":\"Meter\"},{\"title\":\"Saint NN School\",\"entity\":\"School\",\"measurement\":\".7\",\"measurement_type\":\"Meter\"}],\"North\":[{\"title\":\"Something\",\"entity\":\"Super Market\",\"measurement\":\".8\",\"measurement_type\":\"Meter\"}],\"North-East\":[{\"title\":\"Durga Sweets\",\"entity\":\"Provison Store\",\"measurement\":\".2\",\"measurement_type\":\"Meter\"},{\"title\":\"NWR Hospital\",\"entity\":\"Hospital\",\"measurement\":\".5\",\"measurement_type\":\"Meter\"}],\"West\":[{\"title\":\"Kothari Hospital\",\"entity\":\"Hospital\",\"measurement\":\".4\",\"measurement_type\":\"Meter\"}],\"East\":[{\"title\":\"\",\"entity\":\"Atm\",\"measurement\":\".8\",\"measurement_type\":\"Meter\"}],\"South-West\":[{\"title\":\"Raja Tea Stall\",\"entity\":\"Mall\",\"measurement\":\"8\",\"measurement_type\":\"Meter\"}],\"South-East\":[{\"title\":\"Rama Bhawan\",\"entity\":\"Provison Store\",\"measurement\":\"200\",\"measurement_type\":\"Meter\"}]}', '', '67', 1634198619, 69, 'publish', 'N', '', '', '', '', '', '', '', '', ''),
(9, 'Silver Aprtments', 'silver-apartments', 'The silver Apartment available in city', '<p>This is the description of Silver Apartments . <img alt=\\\"smiley\\\" src=\\\"http://192.168.0.80/main_demo/ci_realestate_web/application/views/admin/assets/plugins/ckeditor/plugins/smiley/images/regular_smile.png\\\" title=\\\"smiley\\\" xss=\\\"removed\\\"></p>', 'Rampura', '', 'india', 'rajasthan', 'bikaner', '334004', 'Rampura', '', '', '45', '45~Sq Feet', 'Y', 'sale', 2, '0', '0', '0', '[\"Ac\"]', '[\"Parking\"]', '{\"North-West\":[{\"title\":\"Kothari Hospital\",\"entity\":\"Hospital\",\"measurement\":\"0.3\",\"measurement_type\":\"Meter\"}]}', '', '64', 1634365103, 70, 'publish', 'N', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `property_doc_types`
--

CREATE TABLE `property_doc_types` (
  `pdt_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `is_required` enum('Y','N') NOT NULL DEFAULT 'N',
  `error_message` text NOT NULL,
  `created_on` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `pdt_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `property_doc_types`
--

INSERT INTO `property_doc_types` (`pdt_id`, `title`, `slug`, `description`, `is_required`, `error_message`, `created_on`, `created_by`, `status`, `pdt_order`) VALUES
(2, 'RC', 'rc', 'Something', 'N', 'Nothing', 1629184407, 1, 'Y', 0),
(3, 'Resume', 'resume', 'Something', 'N', '', 1634106933, 1, 'Y', 0);

-- --------------------------------------------------------

--
-- Table structure for table `property_lang_details`
--

CREATE TABLE `property_lang_details` (
  `pld_id` int(11) NOT NULL,
  `title` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_keywords` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `seo_meta_description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `p_id` int(11) NOT NULL,
  `language` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_lang_details`
--

INSERT INTO `property_lang_details` (`pld_id`, `title`, `short_description`, `description`, `price`, `seo_meta_keywords`, `seo_meta_description`, `p_id`, `language`) VALUES
(1, 'Tauju  Apartments', 'this is short Description', '<p>this is main Description</p>', '500', 'Test Property, Test', 'this is meta Description', 1, 'en'),
(3, 'Vision park', 'The expansive backyard includes a sparkling pool', '<p>central heating and cooling, and a convenient washer/dryer hook-up. Beyond a functional entryway space the home flows into a luminous, open- concept living, dining, and kitchen area.</p>', '150', '', '', 3, 'en'),
(4, 'TEJA GARDEN', 'Luxurious and upgraded, this 4 bedroom, 4.5 bathroom home of 5,281 sq. ft.', '<p><em>Stunning large late 80’s contemporary home with soaring ceilings and windows, split levels, great floor plan including open dining and living room. Located in the beautiful hilly and treed, desirable Windmill Hill section of Desoto you are conveniently located to shops, dining, and 20 minutes to downtown Dallas. </em></p>', '110', '', '', 4, 'en'),
(5, 'demo property', 'some description of property', '<p>ipsum lorean data</p>', '150', '', '', 5, 'en'),
(6, 'Navaya town', 'The expansive backyard includes a sparkling pool', '<p>Contemporary amenities include solar PV and a Tesla EV charging station. The expansive backyard includes a sparkling pool and spa plus a comfortable poolhouse all in private, verdant surroundings. You’ll appreciate the short drive to downtown Los Altos, Rancho Shopping Center, access to Interstate 280, and numerous parks and preserves.</p>', '100', '', '', 6, 'en'),
(7, 'Green House Flat', 'The Green House Flat', '<p>Flat in your town Green flat for Families ....</p>', '500', '', '', 7, 'en'),
(8, 'Silver Aprtments', 'The silver Apartment available in city', '<p>This is the description of Silver Apartments . <img alt=\"smiley\" src=\"http://192.168.0.80/main_demo/ci_realestate_web/application/views/admin/assets/plugins/ckeditor/plugins/smiley/images/regular_smile.png\" title=\"smiley\" xss=\"removed\"></p>', '45', '', '', 9, 'en'),
(9, 'adfasdfdsafasdf', 'asdfasdfadsfdsaf', 'asdfasdfasdf', '5', '', '', 10, 'en'),
(10, 'test property', 'short description of test property', 'some description of test property', '5', '', '', 11, 'en'),
(11, 'property test', 'this is the test property', 'this is the test property', '10', '', '', 12, 'en'),
(12, 'this is the test property', 'this is the test property', 'this is the test property', '5', '', '', 13, 'en'),
(13, 'asdfasdf', 'asdfadsf', 'adsfadsf', '10', '', '', 14, 'en'),
(14, 'asdfasdfadsf', 'asdfdasfadsf', 'asdfasdfasdf', '5', '', '', 15, 'en'),
(15, 'asfdasfadsf', 'asdfasdfasdf', 'asdfasdf', '5', '', '', 16, 'en'),
(16, 'asfdsafdsafsdaf', 'asdfdsafdasfdsaf', 'asdfdasfsdafdsaf', '5', '', '', 17, 'en'),
(17, 'test property', 'this is the short description', 'this is the full description', '10', '', '', 18, 'en'),
(18, 'this is the test', 'short description', 'full description', '25', '', '', 19, 'en'),
(19, 'this is the test property', 'this short description', 'this full description', '150', '', '', 20, 'en'),
(20, '', '', '', '', '', '', 21, 'en'),
(21, 'asdfasdfsdaf', 'asdfasdfsadf', 'asdfasdf', '15', '', '', 22, 'en');

-- --------------------------------------------------------

--
-- Table structure for table `property_meta`
--

CREATE TABLE `property_meta` (
  `meta_id` int(255) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `property_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `property_meta`
--

INSERT INTO `property_meta` (`meta_id`, `meta_key`, `meta_value`, `property_id`) VALUES
(1, 'openstreetmap_embed_code', ' ', 1),
(2, 'rc-ids', '3,2', 1),
(4, 'openstreetmap_embed_code', ' ', 3),
(5, 'openstreetmap_embed_code', ' ', 4),
(6, 'resume-ids', '16,15,14', 1),
(7, 'resume-ids', '0', 5),
(8, 'rc-ids', '0', 5),
(9, 'openstreetmap_embed_code', ' ', 5),
(10, 'resume-ids', '0', 6),
(11, 'rc-ids', '0', 6),
(12, 'openstreetmap_embed_code', ' ', 6),
(13, 'resume-ids', '0', 4),
(14, 'rc-ids', '0', 4),
(15, 'resume-ids', '0', 3),
(16, 'rc-ids', '0', 3),
(17, 'resume-ids', '0', 7),
(18, 'rc-ids', '0', 7),
(19, 'openstreetmap_embed_code', ' ', 7),
(20, 'openstreetmap_embed_code', ' ', 9),
(21, 'resume-ids', '0', 9),
(22, 'rc-ids', '0', 9);

-- --------------------------------------------------------

--
-- Table structure for table `property_types`
--

CREATE TABLE `property_types` (
  `pt_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_url` varchar(255) NOT NULL,
  `meta_options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` enum('Y','N') NOT NULL DEFAULT 'Y'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `property_types`
--

INSERT INTO `property_types` (`pt_id`, `title`, `slug`, `img_url`, `meta_options`, `created_on`, `created_by`, `status`) VALUES
(1, 'Apartments', 'apartments', '', '{\"adv_search_options\":{\"enable_min_bed\":\"N\",\"enable_min_bath\":\"N\"}}', 1629101377, 1, 'Y'),
(2, 'Flat', 'flat', 'IMG_20210609_180652-1.jpg', '{\"adv_search_options\":{\"enable_min_bed\":\"N\",\"enable_min_bath\":\"N\"}}', 1629101460, 1, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `transaction_id` int(11) NOT NULL,
  `transaction_key` varchar(20) NOT NULL,
  `packages_id` int(11) NOT NULL,
  `package_detail` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_mode` text NOT NULL,
  `transaction_meta` text,
  `transaction_amount` int(11) NOT NULL,
  `transaction_date` int(11) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `transaction_key`, `packages_id`, `package_detail`, `user_id`, `payment_mode`, `transaction_meta`, `transaction_amount`, `transaction_date`, `status`) VALUES
(48, '6161676475baa', 2, '\"{\\\"subscription\\\":{\\\"title\\\":\\\"Subscription\\\",\\\"details\\\":\\\"\\\"},\\\"post_property\\\":{\\\"title\\\":\\\"Property Posting\\\",\\\"details\\\":\\\"\\\"},\\\"featured_property\\\":{\\\"title\\\":\\\"Featured Property Posting\\\",\\\"details\\\":\\\"\\\"},\\\"post_blog\\\":{\\\"title\\\":\\\"Blog Posting\\\",\\\"details\\\":\\\"\\\"}}\"', 69, 'paypal', '{\"paypal_trans_id\":\"PAYID-MFQWO2I5PW725475V091352A\",\"method\":\"paypal\"}', 150, 1633773429, 'completed'),
(49, 'I7CQiuIt7UYcju', 2, '\"{\\\"subscription\\\":{\\\"title\\\":\\\"Subscription\\\",\\\"details\\\":\\\"\\\"},\\\"post_property\\\":{\\\"title\\\":\\\"Property Posting\\\",\\\"details\\\":\\\"\\\"},\\\"featured_property\\\":{\\\"title\\\":\\\"Featured Property Posting\\\",\\\"details\\\":\\\"\\\"},\\\"post_blog\\\":{\\\"title\\\":\\\"Blog Posting\\\",\\\"details\\\":\\\"\\\"}}\"', 69, 'razorpay', '{\"status\":\"captured\",\"created_at\":1633774136,\"response_obj\":{\"tnx_id\":\"pay_I7CR4hnEhk6vtt\",\"entity\":\"payment\",\"amount\":150,\"currency\":\"INR\",\"order_id\":\"order_I7CQiuIt7UYcju\",\"method\":\"upi\",\"email\":\"deo@gmail.com\",\"contact\":\"+911234567898\"}}', 150, 1633774137, 'captured');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_logs`
--

CREATE TABLE `transaction_logs` (
  `log_id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `trans_details` text NOT NULL,
  `trans_type` varchar(200) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_logs`
--

INSERT INTO `transaction_logs` (`log_id`, `transaction_id`, `trans_details`, `trans_type`, `created_by`, `created_on`) VALUES
(11, 49, 'hi, Admin agent_123 has been created order for Basic package for agents and its Price : 150 with payment method Razorpay', 'Paid Via Razorpay', 69, 1633774138);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_pass` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `user_registered_date` int(11) NOT NULL,
  `user_update_date` int(11) NOT NULL,
  `user_link_id` varchar(250) DEFAULT NULL,
  `user_code` varchar(50) DEFAULT NULL,
  `user_verified` enum('Y','N') NOT NULL DEFAULT 'N',
  `user_status` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_pass`, `user_email`, `user_type`, `user_registered_date`, `user_update_date`, `user_link_id`, `user_code`, `user_verified`, `user_status`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'demo@demo.com', 'admin', 1394868509, 0, '', '', 'Y', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `user_meta`
--

CREATE TABLE `user_meta` (
  `meta_id` int(255) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_meta`
--

INSERT INTO `user_meta` (`meta_id`, `meta_key`, `meta_value`, `user_id`) VALUES
(41, 'language', 'english', 1),
(42, 'first_name', 'Admin', 1),
(43, 'last_name', '', 1),
(44, 'mobile_no', '', 1),
(45, 'address', '', 1),
(46, 'photo_url', '', 1),
(47, 'description', '', 1),
(48, 'social_media', '{\"facebook\":{\"url\":\"\",\"title\":\"Facebook\",\"icon\":\"fa-facebook\"},\"google_plus\":{\"url\":\"\",\"title\":\"Google+\",\"icon\":\"fa-google-plus\"},\"twitter\":{\"url\":\"\",\"title\":\"Twitter\",\"icon\":\"fa-twitter\"},\"pinterest\":{\"url\":\"\",\"title\":\"Pinterest\",\"icon\":\"fa-pinterest\"},\"instagram\":{\"url\":\"\",\"title\":\"Instagram\",\"icon\":\"fa-instagram\"},\"youtube\":{\"url\":\"\",\"title\":\"Youtube\",\"icon\":\"fa-youtube\"}}', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`att_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`b_id`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`b_id`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `blog_lang_details`
--
ALTER TABLE `blog_lang_details`
  ADD PRIMARY KEY (`bld_id`);

--
-- Indexes for table `credits`
--
ALTER TABLE `credits`
  ADD PRIMARY KEY (`credit_id`);

--
-- Indexes for table `credit_uses`
--
ALTER TABLE `credit_uses`
  ADD PRIMARY KEY (`credit_uses_id`);

--
-- Indexes for table `customer_visits`
--
ALTER TABLE `customer_visits`
  ADD PRIMARY KEY (`visit_id`);

--
-- Indexes for table `favorite_table`
--
ALTER TABLE `favorite_table`
  ADD PRIMARY KEY (`fav_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`lang_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`option_id`);

--
-- Indexes for table `options_lang_details`
--
ALTER TABLE `options_lang_details`
  ADD PRIMARY KEY (`sld_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `package_features`
--
ALTER TABLE `package_features`
  ADD PRIMARY KEY (`feature_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `page_lang_details`
--
ALTER TABLE `page_lang_details`
  ADD PRIMARY KEY (`pld_id`);

--
-- Indexes for table `post_images`
--
ALTER TABLE `post_images`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `property_doc_types`
--
ALTER TABLE `property_doc_types`
  ADD PRIMARY KEY (`pdt_id`);

--
-- Indexes for table `property_lang_details`
--
ALTER TABLE `property_lang_details`
  ADD PRIMARY KEY (`pld_id`);

--
-- Indexes for table `property_meta`
--
ALTER TABLE `property_meta`
  ADD PRIMARY KEY (`meta_id`);

--
-- Indexes for table `property_types`
--
ALTER TABLE `property_types`
  ADD PRIMARY KEY (`pt_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`transaction_id`);

--
-- Indexes for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_meta`
--
ALTER TABLE `user_meta`
  ADD PRIMARY KEY (`meta_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `att_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `b_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `blog_lang_details`
--
ALTER TABLE `blog_lang_details`
  MODIFY `bld_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `credits`
--
ALTER TABLE `credits`
  MODIFY `credit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `credit_uses`
--
ALTER TABLE `credit_uses`
  MODIFY `credit_uses_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `customer_visits`
--
ALTER TABLE `customer_visits`
  MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `favorite_table`
--
ALTER TABLE `favorite_table`
  MODIFY `fav_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `lang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;
--
-- AUTO_INCREMENT for table `options_lang_details`
--
ALTER TABLE `options_lang_details`
  MODIFY `sld_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `package_features`
--
ALTER TABLE `package_features`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `page_lang_details`
--
ALTER TABLE `page_lang_details`
  MODIFY `pld_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `post_images`
--
ALTER TABLE `post_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `property_doc_types`
--
ALTER TABLE `property_doc_types`
  MODIFY `pdt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `property_lang_details`
--
ALTER TABLE `property_lang_details`
  MODIFY `pld_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `property_meta`
--
ALTER TABLE `property_meta`
  MODIFY `meta_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `property_types`
--
ALTER TABLE `property_types`
  MODIFY `pt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
--
-- AUTO_INCREMENT for table `transaction_logs`
--
ALTER TABLE `transaction_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;
--
-- AUTO_INCREMENT for table `user_meta`
--
ALTER TABLE `user_meta`
  MODIFY `meta_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
