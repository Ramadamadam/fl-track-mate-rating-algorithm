-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: db5000177741.hosting-data.io
-- Generation Time: Apr 29, 2020 at 11:57 AM
-- Server version: 5.7.28-log
-- PHP Version: 7.0.33-0+deb9u7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbs172530`
--

-- --------------------------------------------------------

--
-- Table structure for table `ajr_trackmate_all`
--

CREATE TABLE `ajr_trackmate_all` (
  `id` mediumint(9) NOT NULL,
  `added_card` int(1) DEFAULT NULL,
  `added_card_date` datetime DEFAULT '0000-00-00 00:00:00',
  `added_card_by` int(2) DEFAULT NULL,
  `added_result` int(1) DEFAULT NULL,
  `added_result_date` datetime DEFAULT '0000-00-00 00:00:00',
  `added_result_by` int(2) DEFAULT NULL,
  `updated` int(1) DEFAULT NULL,
  `updated_date` datetime DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(2) DEFAULT NULL,
  `race_date` date DEFAULT '0000-00-00',
  `race_time` time DEFAULT '00:00:00',
  `track_name` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `race_name` varchar(250) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `race_class` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `race_distance` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `race_distance_furlongs` varchar(6) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `going_description` varchar(25) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `prize_money` mediumint(8) DEFAULT NULL,
  `number_of_runners` int(2) DEFAULT NULL,
  `track_direction` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `card_number` int(2) DEFAULT NULL,
  `horse_name` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `horse_age` int(2) DEFAULT NULL,
  `horse_type` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `jockey_name` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `jockey_claim` int(1) DEFAULT NULL,
  `trainer_name` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `stall` int(2) DEFAULT NULL,
  `official_rating` int(3) DEFAULT NULL,
  `weight_pounds` int(3) DEFAULT NULL,
  `odds` float(6,2) DEFAULT NULL,
  `form` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `days` int(4) DEFAULT NULL,
  `head_gear` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `stallion` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `dam` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `cd` varchar(5) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `race_restrictions_age` varchar(5) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `race_type` varchar(25) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `major` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `place` varchar(4) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `placing_numerical` int(2) DEFAULT NULL,
  `distance_beat` varchar(6) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `total_distance_beat` float(6,2) DEFAULT NULL,
  `fav` varchar(8) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `comptime` varchar(14) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `comptime_numeric` float(5,2) DEFAULT NULL,
  `medianor` float(4,2) DEFAULT NULL,
  `rcode` varchar(25) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `bfsp` float(21,18) DEFAULT NULL,
  `bfsp_place` float(21,18) DEFAULT NULL,
  `places_paid` int(1) DEFAULT NULL,
  `bf_places_paid` int(1) DEFAULT NULL,
  `yards` int(5) DEFAULT NULL,
  `rail_move` int(4) DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_520_ci,
  `stall_positioning` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `silks` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `ajr_trackmate_all`
--

INSERT INTO `ajr_trackmate_all` (`id`, `added_card`, `added_card_date`, `added_card_by`, `added_result`, `added_result_date`, `added_result_by`, `updated`, `updated_date`, `updated_by`, `race_date`, `race_time`, `track_name`, `race_name`, `race_class`, `race_distance`, `race_distance_furlongs`, `going_description`, `prize_money`, `number_of_runners`, `track_direction`, `card_number`, `horse_name`, `horse_age`, `horse_type`, `jockey_name`, `jockey_claim`, `trainer_name`, `stall`, `official_rating`, `weight_pounds`, `odds`, `form`, `days`, `head_gear`, `stallion`, `dam`, `cd`, `race_restrictions_age`, `race_type`, `major`, `place`, `placing_numerical`, `distance_beat`, `total_distance_beat`, `fav`, `comptime`, `comptime_numeric`, `medianor`, `rcode`, `bfsp`, `bfsp_place`, `places_paid`, `bf_places_paid`, `yards`, `rail_move`, `comment`, `stall_positioning`, `silks`) VALUES
(1, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '13:50:00', 'Catterick', 'Racing To School Selling Handicap Hurdle', 'Class 5', '2m ', '16', 'Soft', 3509, 5, 'Left Handed', 1, 'Hurricane Hollow', 8, 'Gelding', 'Andrews, Miss B ', 3, 'Skelton, Daniel', 0, 129, 166, 2.63, '2358327', 15, '', 'Beat Hollow', 'Veenwouden', 'D', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(2, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '13:50:00', 'Catterick', 'Racing To School Selling Handicap Hurdle', 'Class 5', '2m ', '16', 'Soft', 3509, 5, 'Left Handed', 2, 'Young Tom', 5, 'Gelding', 'Quinlan, Sean', 0, 'Smith, Mrs S J', 0, 107, 144, 4.33, '2730456', 42, '', 'Sir Percy', 'Enford Princess', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(3, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '13:50:00', 'Catterick', 'Racing To School Selling Handicap Hurdle', 'Class 5', '2m ', '16', 'Soft', 3509, 5, 'Left Handed', 3, 'Discoverie', 10, 'Gelding', 'Hughes, Brian', 0, 'Slack, Kenneth', 0, 103, 140, 2.25, '5U462P3', 13, 'H+B', 'Runyon (IRE)', 'Sri (IRE)', 'C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(4, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '13:50:00', 'Catterick', 'Racing To School Selling Handicap Hurdle', 'Class 5', '2m ', '16', 'Soft', 3509, 5, 'Left Handed', 4, 'Jacarno', 6, 'Gelding', 'Turner, Mr Ross', 7, 'Crook, A', 0, 103, 141, 41.00, '56B6956', 10, 'Vsor', 'Lucarno (USA)', 'Sparkling Jewel', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(5, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '13:50:00', 'Catterick', 'Racing To School Selling Handicap Hurdle', 'Class 5', '2m ', '16', 'Soft', 3509, 5, 'Left Handed', 5, 'Atlas Peak (IRE)', 13, 'Gelding', 'Yeoman, Mr K', 7, 'Thompson, V', 0, 103, 140, 501.00, '909PP77', 30, '', 'Namid', 'My Delilah (IRE)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(6, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 1, 'Kafeel (USA)', 7, 'Gelding', 'Keniry, L P', 0, 'Moore, G L', 8, 64, 133, 4.00, '4462112', 18, 'Blnk', 'First Samurai (USA)', 'Ishraak (USA)', 'CD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(7, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 2, 'Shifting Star (IRE)', 13, 'Gelding', 'Morris, Luke', 0, 'Bridger, J J', 6, 63, 132, 34.00, '5386960', 61, 'Vsor TT', 'Night Shift (USA)', 'Ahshado', 'CD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(8, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 3, 'Fintech (IRE)', 4, 'Gelding', 'Crouch, Hector', 0, 'Hide, Philip', 9, 63, 132, 9.00, '5740725', 83, ' TT', 'Dark Angel (IRE)', 'Final Legacy (USA)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(9, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 4, 'Spiritual Star (IRE)', 9, 'Gelding', 'Bennett, Charlie', 3, 'Carter, Lee', 1, 63, 132, 17.00, '0300500', 21, '', 'Soviet Star (USA)', 'Million Spirits (IRE)', 'CD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(10, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 5, 'Presence Process', 4, 'Gelding', 'Bradley, Paddy', 5, 'Phelan, P M', 3, 61, 130, 4.50, '9668211', 18, '', 'Dansili', 'Loulwa (IRE)', 'CD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(11, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 6, 'Karam Albaari (IRE)', 10, 'Horse', 'Levey, S M', 0, 'Jenkins, J R', 11, 59, 128, 26.00, '8064130', 21, 'Vsor', 'Kings Best (USA)', 'Lilakiya (IRE)', 'C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(12, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 7, 'Luxford', 4, 'Filly', 'De Barros, Dayverson', 0, 'Best, J R', 2, 58, 127, 17.00, '7614956', 21, '', 'Mullionmileanhour (IRE)', 'Dolly Parton (IRE)', 'D', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(13, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 8, 'License To Thrill (USA)', 4, 'Gelding', 'Marquand, Tom', 0, 'Dow, S', 4, 57, 126, 8.00, '9535505', 16, 'CkPc', 'Mizzen Mast (USA)', 'Mystic Miracle', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(14, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 9, 'Baby Gal', 4, 'Filly', 'Francis, Isobel', 7, 'Boyle, J R', 5, 56, 125, 34.00, '6432557', 16, '', 'Royal Applause', 'Our Gal', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(15, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 10, 'Big Amigo (IRE)', 5, 'Gelding', 'Walsh, E J', 3, 'Loughnane, Daniel Mark', 10, 56, 125, 6.00, '4325661', 8, '', 'Bahamian Bounty', 'Goldamour (IRE)', 'CD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(16, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 11, 'Rivers Of Asia', 5, 'Gelding', 'Naseb, Milly', 5, 'Smith, Martin', 12, 54, 123, 11.00, '5040433', 8, 'CkPc', 'Medicean', 'Aliena (IRE)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(17, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:00:00', 'Lingfield', 'Play For Free At sunbets.co.uk/vegas Handicap', 'Class 6', '1m ', '8', 'Standard', 3105, 12, 'Left Handed', 12, 'Exspectation (IRE)', 4, 'Gelding', 'Hornby, Rob', 0, 'Blanshard, M', 7, 52, 121, 26.00, '009U850', 53, '', 'Excelebration (IRE)', 'Emeralds Spirit (IRE)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(18, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:10:00', 'Fontwell', 'Axio Novices Hurdle', 'Class 4', '2m6f ', '22', 'Soft', 4094, 5, 'Left Handed', 1, 'Boreham Bill (IRE)', 6, 'Gelding', 'Jacob, Daryl', 0, 'Pauling, Ben', 0, 130, 163, 2.63, '1205441', 30, '', 'Tikkanen (USA)', 'Crimond (IRE)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(19, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:10:00', 'Fontwell', 'Axio Novices Hurdle', 'Class 4', '2m6f ', '22', 'Soft', 4094, 5, 'Left Handed', 2, 'Golden Sunrise (IRE)', 5, 'Gelding', 'Cobden, Mr H', 0, 'Tizzard, C L', 0, 127, 163, 3.25, '473241', 24, '', 'Stowaway', 'Fairy Dawn (IRE)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(20, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:10:00', 'Fontwell', 'Axio Novices Hurdle', 'Class 4', '2m6f ', '22', 'Soft', 4094, 5, 'Left Handed', 3, 'Indian Hercules (IRE)', 6, 'Gelding', 'Sheehan, Gavin', 0, 'Greatrex, W J', 0, 0, 163, 7.00, '516', 88, '', 'Whitmores Conn (USA)', 'Carrawaystick (IRE)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(21, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:10:00', 'Fontwell', 'Axio Novices Hurdle', 'Class 4', '2m6f ', '22', 'Soft', 4094, 5, 'Left Handed', 4, 'Mighty Vic (IRE)', 10, 'Gelding', '', 3, 'Smith, Miss Suzy', 0, 0, 156, 1.00, '36', 10, ' TT', 'Old Vic', 'Mighty Marble (IRE)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(22, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:10:00', 'Fontwell', 'Axio Novices Hurdle', 'Class 4', '2m6f ', '22', 'Soft', 4094, 5, 'Left Handed', 5, 'Pandinus Imperator (IRE)', 5, 'Gelding', '', 0, 'Smith, Martin', 0, 0, 156, 1.00, '486', 135, '', 'Scorpion (IRE)', 'Casiana (GER)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(23, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:10:00', 'Fontwell', 'Axio Novices Hurdle', 'Class 4', '2m6f ', '22', 'Soft', 4094, 5, 'Left Handed', 6, 'Reikers Island (IRE)', 5, 'Gelding', 'Johnson, Richard', 0, 'Hobbs, P J', 0, 0, 156, 4.50, 'F', 12, '', 'Yeats (IRE)', 'Moricana (GER)', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(24, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:10:00', 'Fontwell', 'Axio Novices Hurdle', 'Class 4', '2m6f ', '22', 'Soft', 4094, 5, 'Left Handed', 7, 'Stockburn (IRE)', 5, 'Gelding', 'Hutchinson, Wayne', 0, 'King, A', 0, 0, 156, 15.00, '0865', 26, '', 'Scorpion (IRE)', 'Hayabusa', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ''),
(25, 1, '2020-03-10 23:36:23', 1, NULL, '0000-00-00 00:00:00', NULL, 1, '2020-03-10 23:36:23', 1, '2018-03-07', '14:20:00', 'Catterick', 'Injured Jockeys Fund Novices Chase', 'Class 4', '2m3.5f ', '19.5', 'Soft', 5458, 5, 'Left Handed', 1, 'Cracking Find (IRE)', 7, 'Gelding', 'Cook, Danny', 0, 'Smith, Mrs S J', 0, 122, 158, 7.50, '3233153', 55, '', 'Robin Des Pres (FR)', 'Crack The Kicker (IRE)', 'CD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ajr_trackmate_all`
--
ALTER TABLE `ajr_trackmate_all`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `race_date_2` (`race_date`,`horse_name`),
  ADD KEY `horse_name` (`horse_name`),
  ADD KEY `race_date` (`race_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ajr_trackmate_all`
--
ALTER TABLE `ajr_trackmate_all`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2695946;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
