-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2021 at 10:44 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hd_wallpaper`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_active_log`
--

CREATE TABLE `tbl_active_log` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `date_time` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_active_log`
--

INSERT INTO `tbl_active_log` (`id`, `user_id`, `date_time`) VALUES
(1, 1, '1605178813');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `username`, `password`, `email`, `image`) VALUES
(1, 'admin', 'admin', 'viaviwebtech@gmail.com', 'profile.png');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `cid` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_image` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`cid`, `category_name`, `category_image`, `status`) VALUES
(2, 'Baby', '3326_8312_beby_category_1_400_200.jpg', 1),
(4, 'Bird', '38144_Bird.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_color`
--

CREATE TABLE `tbl_color` (
  `color_id` int(10) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `color_code` varchar(10) NOT NULL,
  `color_status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_color`
--

INSERT INTO `tbl_color` (`color_id`, `color_name`, `color_code`, `color_status`) VALUES
(2, 'Dark Blue', '#1E08E9', 1),
(3, 'Sky Blue', '#80ADE9', 1),
(4, 'Red', '#E90313', 1),
(5, 'Yellow', '#DFE913', 1),
(6, 'Green', '#0CA83D', 1),
(7, 'Black', '#000000', 1),
(8, 'Lavender', '#BE78E9', 1),
(9, 'Orange', '#E95124', 0),
(10, 'TBY', '#6377E9', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_favorite`
--

CREATE TABLE `tbl_favorite` (
  `id` int(10) NOT NULL,
  `post_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  `created_at` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_favorite`
--

INSERT INTO `tbl_favorite` (`id`, `post_id`, `user_id`, `type`, `created_at`) VALUES
(1, 58, 1, 'wallpaper', '1605178730'),
(2, 1, 1, 'gif', '1605178770');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_home_banner`
--

CREATE TABLE `tbl_home_banner` (
  `id` int(11) NOT NULL,
  `banner_name` varchar(255) NOT NULL,
  `banner_image` varchar(255) NOT NULL,
  `banner_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rating`
--

CREATE TABLE `tbl_rating` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `rate` int(11) NOT NULL,
  `dt_rate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rating_gif`
--

CREATE TABLE `tbl_rating_gif` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `rate` int(11) NOT NULL,
  `dt_rate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `email_from` varchar(200) NOT NULL,
  `onesignal_app_id` text NOT NULL,
  `onesignal_rest_key` text NOT NULL,
  `envato_buyer_name` varchar(200) NOT NULL,
  `envato_purchase_code` text NOT NULL,
  `envato_buyer_email` varchar(150) NOT NULL,
  `envato_purchased_status` int(1) NOT NULL DEFAULT 0,
  `envato_ios_purchase_code` varchar(255) NOT NULL,
  `envato_ios_purchased_status` int(2) NOT NULL DEFAULT 0,
  `package_name` varchar(150) NOT NULL,
  `ios_bundle_identifier` varchar(200) NOT NULL,
  `app_api_key` varchar(255) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `app_logo` varchar(255) NOT NULL,
  `app_email` varchar(255) NOT NULL,
  `app_version` varchar(255) NOT NULL,
  `app_author` varchar(255) NOT NULL,
  `app_contact` varchar(255) NOT NULL,
  `app_website` varchar(255) NOT NULL,
  `app_description` text NOT NULL,
  `app_developed_by` varchar(255) NOT NULL,
  `app_privacy_policy` text NOT NULL,
  `account_delete_intruction` text NOT NULL,
  `item_type` varchar(255) NOT NULL DEFAULT 'Portrait',
  `gif_on_off` varchar(255) NOT NULL DEFAULT 'true',
  `home_latest_limit` int(2) NOT NULL DEFAULT 10,
  `home_most_viewed_limit` int(2) NOT NULL DEFAULT 10,
  `home_most_rated_limit` int(2) NOT NULL DEFAULT 10,
  `home_limit` int(3) DEFAULT NULL,
  `home_landscape_limit` int(2) NOT NULL DEFAULT 10,
  `home_square_limit` int(2) NOT NULL DEFAULT 10,
  `api_latest_limit` int(3) NOT NULL DEFAULT 15,
  `api_cat_order_by` varchar(255) NOT NULL DEFAULT 'category_name',
  `api_cat_post_order_by` varchar(255) NOT NULL DEFAULT 'DESC',
  `api_gif_post_order_by` varchar(255) NOT NULL DEFAULT 'DESC',
  `app_update_status` varchar(10) NOT NULL DEFAULT 'false',
  `app_new_version` double NOT NULL DEFAULT 1,
  `app_update_desc` text NOT NULL,
  `app_redirect_url` text NOT NULL,
  `cancel_update_status` varchar(10) NOT NULL DEFAULT 'false',
  `app_update_status_ios` varchar(10) NOT NULL DEFAULT 'false',
  `app_new_version_ios` double NOT NULL DEFAULT 1,
  `app_update_desc_ios` text NOT NULL,
  `app_redirect_url_ios` text NOT NULL,
  `cancel_update_status_ios` varchar(10) NOT NULL DEFAULT 'false',
  `publisher_id` text NOT NULL,
  `interstital_ad` varchar(255) NOT NULL,
  `interstital_ad_id` varchar(255) NOT NULL,
  `interstital_ad_click` varchar(255) NOT NULL,
  `banner_ad` varchar(255) NOT NULL,
  `banner_ad_id` varchar(255) NOT NULL,
  `facebook_interstital_ad` varchar(255) NOT NULL,
  `facebook_interstital_ad_id` varchar(255) NOT NULL,
  `facebook_interstital_ad_click` varchar(255) NOT NULL,
  `facebook_banner_ad` varchar(255) NOT NULL,
  `facebook_banner_ad_id` varchar(255) NOT NULL,
  `facebook_native_ad` varchar(255) NOT NULL,
  `facebook_native_ad_id` varchar(255) NOT NULL,
  `facebook_native_ad_click` varchar(255) NOT NULL,
  `admob_nathive_ad` varchar(255) NOT NULL,
  `admob_native_ad_id` varchar(255) NOT NULL,
  `admob_native_ad_click` varchar(255) NOT NULL,
  `publisher_id_ios` varchar(500) NOT NULL,
  `app_id_ios` varchar(500) NOT NULL,
  `interstital_ad_ios` varchar(500) NOT NULL,
  `interstital_ad_id_ios` varchar(500) NOT NULL,
  `interstital_ad_click_ios` varchar(500) NOT NULL,
  `banner_ad_ios` varchar(500) NOT NULL,
  `banner_ad_id_ios` varchar(500) NOT NULL,
  `ios_facebook_interstital_ad` varchar(255) NOT NULL,
  `ios_facebook_interstital_ad_id` varchar(255) NOT NULL,
  `ios_facebook_interstital_ad_click` varchar(255) NOT NULL,
  `ios_facebook_banner_ad` varchar(255) NOT NULL,
  `ios_facebook_banner_ad_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `email_from`, `onesignal_app_id`, `onesignal_rest_key`, `envato_buyer_name`, `envato_purchase_code`, `envato_buyer_email`, `envato_purchased_status`, `envato_ios_purchase_code`, `envato_ios_purchased_status`, `package_name`, `ios_bundle_identifier`, `app_api_key`, `app_name`, `app_logo`, `app_email`, `app_version`, `app_author`, `app_contact`, `app_website`, `app_description`, `app_developed_by`, `app_privacy_policy`, `account_delete_intruction`, `item_type`, `gif_on_off`, `home_latest_limit`, `home_most_viewed_limit`, `home_most_rated_limit`, `home_limit`, `home_landscape_limit`, `home_square_limit`, `api_latest_limit`, `api_cat_order_by`, `api_cat_post_order_by`, `api_gif_post_order_by`, `app_update_status`, `app_new_version`, `app_update_desc`, `app_redirect_url`, `cancel_update_status`, `app_update_status_ios`, `app_new_version_ios`, `app_update_desc_ios`, `app_redirect_url_ios`, `cancel_update_status_ios`, `publisher_id`, `interstital_ad`, `interstital_ad_id`, `interstital_ad_click`, `banner_ad`, `banner_ad_id`, `facebook_interstital_ad`, `facebook_interstital_ad_id`, `facebook_interstital_ad_click`, `facebook_banner_ad`, `facebook_banner_ad_id`, `facebook_native_ad`, `facebook_native_ad_id`, `facebook_native_ad_click`, `admob_nathive_ad`, `admob_native_ad_id`, `admob_native_ad_click`, `publisher_id_ios`, `app_id_ios`, `interstital_ad_ios`, `interstital_ad_id_ios`, `interstital_ad_click_ios`, `banner_ad_ios`, `banner_ad_id_ios`, `ios_facebook_interstital_ad`, `ios_facebook_interstital_ad_id`, `ios_facebook_interstital_ad_click`, `ios_facebook_banner_ad`, `ios_facebook_banner_ad_id`) VALUES
(1, 'info@viaviweb.in', '', '', '', '', '', 0, '', 0, 'com.vpapps.hdwallpaper', 'com.viavi.hdwallpapers', 'YtmpgwcUzCbeh1JlsK0gR0njtXm9g9lAUt4pzsPZhsH8a', 'HD Wallpaper App', 'Icon144.png', 'info@viaviweb.com', '1.1.0', 'Viavi Webtech', '+91 9227777522', 'www.viaviweb.com', '<p><strong>&ldquo;HD Wallpaper&rdquo;</strong> is a cool new app that brings all the best HD wallpapers and backgrounds to your Android device.</p>\r\n\r\n<p>Each high resolution image has been perfectly formatted fit to the phone display and comes with a host of user friendly features. The stunning UI allows you easily tap and swipe your way through the multiple image galleries. To develop similar app with your name you can contact us via skype or whatsapp.<br />\r\n<br />\r\n<strong>Skype:</strong> viaviwebtech<br />\r\n<strong>WhatsApp:</strong> +919227777522</p>\r\n', 'Viavi Webtech', '<p><strong>We are committed to protecting your privac&nbsp;</strong></p>\r\n\r\n<p>We collect the minimum amount of information about you that is commensurate with providing you with a satisfactory service. This policy indicates the type of processes that may result in data being collected about you. Your use of this website gives us the right to collect that information.&nbsp;</p>\r\n\r\n<p><strong>Information Collected</strong></p>\r\n\r\n<p>We may collect any or all of the information that you give us depending on the type of transaction you enter into, including your name, address, telephone number, and email address, together with data about your use of the website. Other information that may be needed from time to time to process a request may also be collected as indicated on the website.</p>\r\n\r\n<p><strong>Information Use</strong></p>\r\n\r\n<p>We use the information collected primarily to process the task for which you visited the website. Data collected in the UK is held in accordance with the Data Protection Act. All reasonable precautions are taken to prevent unauthorised access to this information. This safeguard may require you to provide additional forms of identity should you wish to obtain information about your account details.</p>\r\n\r\n<p><strong>Cookies</strong></p>\r\n\r\n<p>Your Internet browser has the in-built facility for storing small files - &quot;cookies&quot; - that hold information which allows a website to recognise your account. Our website takes advantage of this facility to enhance your experience. You have the ability to prevent your computer from accepting cookies but, if you do, certain functionality on the website may be impaired.</p>\r\n\r\n<p><strong>Disclosing Information</strong></p>\r\n\r\n<p>We do not disclose any personal information obtained about you from this website to third parties unless you permit us to do so by ticking the relevant boxes in registration or competition forms. We may also use the information to keep in contact with you and inform you of developments associated with us. You will be given the opportunity to remove yourself from any mailing list or similar device. If at any time in the future we should wish to disclose information collected on this website to any third party, it would only be with your knowledge and consent.&nbsp;</p>\r\n\r\n<p>We may from time to time provide information of a general nature to third parties - for example, the number of individuals visiting our website or completing a registration form, but we will not use any information that could identify those individuals.&nbsp;</p>\r\n\r\n<p>In addition Dummy may work with third parties for the purpose of delivering targeted behavioural advertising to the Dummy website. Through the use of cookies, anonymous information about your use of our websites and other websites will be used to provide more relevant adverts about goods and services of interest to you. For more information on online behavioural advertising and about how to turn this feature off, please visit youronlinechoices.com/opt-out.</p>\r\n\r\n<p><strong>Changes to this Policy</strong></p>\r\n\r\n<p>Any changes to our Privacy Policy will be placed here and will supersede this version of our policy. We will take reasonable steps to draw your attention to any changes in our policy. However, to be on the safe side, we suggest that you read this document each time you use the website to ensure that it still meets with your approval.</p>\r\n\r\n<p><strong>Contacting Us</strong></p>\r\n\r\n<p>If you have any questions about our Privacy Policy, or if you want to know what information we have collected about you, please email us at hd@dummy.com. You can also correct any factual errors in that information or require us to remove your details form any list under our control.</p>\r\n', '', 'Portrait,Landscape,Square', 'true', 20, 20, 20, 5, 10, 10, 20, 'category_name', 'DESC', 'ASC', 'false', 1, 'Kindly you can update new version app', 'https://play.google.com/store/apps/details?id=com.viaan.wallpaper', 'false', 'false', 1, '', '', 'false', 'pub-8356404931736973', 'false', 'ca-app-pub-3940256099942544/1033173712', '10', 'false', 'ca-app-pub-3940256099942544/6300978111', 'false', '1393008281089270_1393009821089116', '5', 'false', '1393008281089270_1393010137755751', 'false', '1393008281089270_1393009201089178', '12', 'false', 'ca-app-pub-3940256099942544/2247696110', '12', 'pub-8356404931736973', 'ca-app-pub-8356404931736973~5938963872', 'true', 'ca-app-pub-8356404931736973/9495065509', '5', 'true', 'ca-app-pub-8356404931736973/7383907483', 'false', '231345511380786_231350444713626', '3', 'false', '231345511380786_231349311380406');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_smtp_settings`
--

CREATE TABLE `tbl_smtp_settings` (
  `id` int(5) NOT NULL,
  `smtp_type` varchar(20) NOT NULL DEFAULT 'server',
  `smtp_host` varchar(150) NOT NULL,
  `smtp_email` varchar(150) NOT NULL,
  `smtp_password` text NOT NULL,
  `smtp_secure` varchar(20) NOT NULL,
  `port_no` varchar(10) NOT NULL,
  `smtp_ghost` varchar(150) NOT NULL,
  `smtp_gemail` varchar(150) NOT NULL,
  `smtp_gpassword` text NOT NULL,
  `smtp_gsecure` varchar(20) NOT NULL,
  `gport_no` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_smtp_settings`
--

INSERT INTO `tbl_smtp_settings` (`id`, `smtp_type`, `smtp_host`, `smtp_email`, `smtp_password`, `smtp_secure`, `port_no`, `smtp_ghost`, `smtp_gemail`, `smtp_gpassword`, `smtp_gsecure`, `gport_no`) VALUES
(1, 'server', '', '', '', 'ssl', '465', '', '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(10) NOT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'Normal',
  `name` varchar(60) NOT NULL,
  `email` varchar(70) NOT NULL,
  `password` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `auth_id` varchar(255) NOT NULL DEFAULT '0',
  `registered_on` varchar(200) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`id`, `user_type`, `name`, `email`, `password`, `phone`, `auth_id`, `registered_on`, `status`) VALUES
(1, 'Normal', 'User', 'user.viaviweb@gmail.com', '202cb962ac59075b964b07152d234b70', '1234567891', '0', '1599628490', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_report`
--

CREATE TABLE `tbl_user_report` (
  `user_report_id` int(10) NOT NULL,
  `report_for` varchar(30) NOT NULL,
  `user_id` int(5) NOT NULL,
  `parent_id` int(5) NOT NULL,
  `user_message` longtext NOT NULL,
  `seneded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_report_status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_wallpaper`
--

CREATE TABLE `tbl_wallpaper` (
  `id` int(11) NOT NULL,
  `featured` int(1) NOT NULL DEFAULT 0,
  `cat_id` int(11) NOT NULL,
  `wallpaper_type` varchar(255) NOT NULL DEFAULT 'none',
  `image_date` date NOT NULL,
  `image` varchar(255) NOT NULL,
  `wall_tags` text NOT NULL,
  `wall_colors` text NOT NULL,
  `total_rate` int(11) NOT NULL DEFAULT 0,
  `rate_avg` decimal(11,0) DEFAULT 0,
  `total_views` int(11) NOT NULL DEFAULT 0,
  `total_download` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_wallpaper`
--

INSERT INTO `tbl_wallpaper` (`id`, `featured`, `cat_id`, `wallpaper_type`, `image_date`, `image`, `wall_tags`, `wall_colors`, `total_rate`, `rate_avg`, `total_views`, `total_download`) VALUES
(29, 0, 2, 'Portrait', '2018-06-18', '5440_85144_Baby_4.jpg', 'Baby Girl, Sweet Girl', '3', 0, '0', 0, 0),
(30, 0, 2, 'Portrait', '2018-06-18', '13625_81297_Baby_9.jpg', 'Baby Girl, Sweet Girl', '', 0, '0', 0, 0),
(31, 0, 2, 'Portrait', '2018-06-18', '99216_2_Baby_13.jpg', 'Baby Girl, Sweet Girl', '', 0, '0', 0, 0),
(32, 0, 2, 'Portrait', '2018-06-18', '30743_8751_Baby_7.jpg', 'Baby Girl, Sweet Girl', '', 0, '0', 0, 0),
(54, 0, 4, 'Portrait', '2018-06-18', '57730_51480_Bird_2.jpg', 'Colorful Bird', '', 0, '0', 0, 0),
(55, 0, 4, 'Portrait', '2018-06-18', '96143_42332_bird.jpg', 'Birds', '', 0, '0', 0, 0),
(57, 0, 4, 'Portrait', '2018-06-18', '91460_19675_Bird_6.jpg', 'Colorful parrot, Birds', '2,6,9,3', 0, '0', 0, 0),
(58, 0, 4, 'Portrait', '2018-06-18', '439_39370_Bird_7.jpg', ' Bird', '8', 0, '0', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_wallpaper_gif`
--

CREATE TABLE `tbl_wallpaper_gif` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `gif_tags` text NOT NULL,
  `total_views` int(11) NOT NULL DEFAULT 0,
  `total_rate` int(11) NOT NULL DEFAULT 0,
  `rate_avg` decimal(11,2) NOT NULL DEFAULT 0.00,
  `total_download` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_wallpaper_gif`
--

INSERT INTO `tbl_wallpaper_gif` (`id`, `image`, `gif_tags`, `total_views`, `total_rate`, `rate_avg`, `total_download`) VALUES
(1, '64847_43754_gif-lady-of-the-camellias-ballet-dance-Favim.com-4202618.gif', 'lady,camellias,balletdance', 0, 0, '0.00', 0),
(2, '93874_18785_2.gif', 'Dancing Kid,Kids', 0, 0, '0.00', 0),
(3, '14983_17670_4.gif', 'cartoon', 0, 0, '0.00', 0),
(5, '7216_30319_5.gif', 'Girl,Boy,Love', 0, 0, '0.00', 0),
(6, '85293_70122_6.gif', 'aladdin,jasmine', 0, 0, '0.00', 0),
(7, '21118_89986_7.gif', 'micky', 0, 0, '0.00', 0),
(8, '51712_67402_8.gif', 'Dancing', 0, 0, '0.00', 0),
(9, '22672_15146_9_Business.gif', 'Internet,Lady', 0, 0, '0.00', 0),
(10, '78760_59532_2f86a060035aed0ea893967f6b4d9874.gif', 'Dancing Girl', 0, 0, '0.00', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_active_log`
--
ALTER TABLE `tbl_active_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `tbl_color`
--
ALTER TABLE `tbl_color`
  ADD PRIMARY KEY (`color_id`);

--
-- Indexes for table `tbl_favorite`
--
ALTER TABLE `tbl_favorite`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_home_banner`
--
ALTER TABLE `tbl_home_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rating`
--
ALTER TABLE `tbl_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_rating_gif`
--
ALTER TABLE `tbl_rating_gif`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_smtp_settings`
--
ALTER TABLE `tbl_smtp_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user_report`
--
ALTER TABLE `tbl_user_report`
  ADD PRIMARY KEY (`user_report_id`);

--
-- Indexes for table `tbl_wallpaper`
--
ALTER TABLE `tbl_wallpaper`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_wallpaper_gif`
--
ALTER TABLE `tbl_wallpaper_gif`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_active_log`
--
ALTER TABLE `tbl_active_log`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_color`
--
ALTER TABLE `tbl_color`
  MODIFY `color_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_favorite`
--
ALTER TABLE `tbl_favorite`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_home_banner`
--
ALTER TABLE `tbl_home_banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_rating`
--
ALTER TABLE `tbl_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_rating_gif`
--
ALTER TABLE `tbl_rating_gif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_smtp_settings`
--
ALTER TABLE `tbl_smtp_settings`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_user_report`
--
ALTER TABLE `tbl_user_report`
  MODIFY `user_report_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_wallpaper`
--
ALTER TABLE `tbl_wallpaper`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `tbl_wallpaper_gif`
--
ALTER TABLE `tbl_wallpaper_gif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
