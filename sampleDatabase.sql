-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2022 at 09:18 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flightbooking`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_credentials`
--

CREATE TABLE `admin_credentials` (
  `id` int(11) NOT NULL,
  `username` char(20) NOT NULL,
  `pwd` char(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin_credentials`
--

INSERT INTO `admin_credentials` (`id`, `username`, `pwd`) VALUES
(1, 'admin', 'admin'),
(2, 'elgamal.s', 'bookFlights');

-- --------------------------------------------------------

--
-- Table structure for table `blacklist`
--

CREATE TABLE `blacklist` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blacklist`
--

INSERT INTO `blacklist` (`id`, `first_name`, `last_name`) VALUES
(2, 'Example', 'One'),
(3, 'Example', 'Two'),
(4, 'Example', 'Three');

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `number_of_rows` int(11) NOT NULL,
  `number_of_columns` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `seats_booked` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `route_id`, `date`, `time`, `number_of_rows`, `number_of_columns`, `capacity`, `seats_booked`) VALUES
(1, 1, '2022-02-09', '13:16:57', 30, 6, 180, 8),
(2, 2, '2022-02-09', '23:19:59', 45, 10, 450, 2),
(3, 3, '2022-01-05', '07:24:00', 35, 9, 315, 1),
(4, 17, '2022-02-13', '18:40:00', 29, 6, 174, 5),
(6, 6, '2022-01-04', '10:36:00', 34, 8, 272, 0),
(7, 7, '2022-02-13', '00:05:00', 35, 6, 210, 0),
(8, 8, '2021-12-28', '15:35:00', 30, 6, 180, 1),
(9, 9, '2022-02-02', '19:30:00', 36, 9, 324, 0),
(10, 18, '2022-02-01', '18:29:00', 35, 8, 280, 1),
(11, 5, '2021-12-14', '13:53:00', 54, 4, 216, 0),
(12, 3, '2022-01-01', '00:00:00', 50, 9, 450, 0),
(14, 4, '2022-01-01', '01:00:00', 40, 6, 240, 0),
(15, 6, '2022-01-01', '00:00:00', 34, 7, 238, 0),
(16, 13, '2021-12-29', '16:25:00', 40, 8, 320, 0),
(17, 14, '2022-01-01', '17:14:00', 30, 6, 180, 0),
(18, 15, '2021-12-28', '17:41:00', 45, 10, 450, 0),
(19, 16, '2021-12-27', '17:45:00', 35, 9, 315, 0),
(20, 19, '2022-02-22', '09:56:00', 30, 6, 180, 1);

-- --------------------------------------------------------

--
-- Table structure for table `flight_bookings`
--

CREATE TABLE `flight_bookings` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `flight_id` int(11) NOT NULL,
  `seat_row` enum('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31','32','33','34','35','36','37','38','39','40','41','42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59','60','61','62','63','64','65','66','67','68','69','70','71','72','73','74','75','76','77','78','79','80','81','82','83','84','85','86','87','88','89','90') NOT NULL,
  `seat_column` enum('A','B','C','D','E','F','G','H','J','K') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `flight_bookings`
--

INSERT INTO `flight_bookings` (`id`, `first_name`, `last_name`, `flight_id`, `seat_row`, `seat_column`) VALUES
(3, 'Sssssssssssssssssssssssssssssssssssssss', 'Egtrewhtrfsdgfwer', 10, '11', 'C'),
(4, 'Stjftydgftrdsgf', 'Last', 3, '30', 'B'),
(13, 'Dfghgh', 'Sdfhther', 4, '1', 'A'),
(14, 'Erhywyterw', 'Erywerywer', 4, '1', 'C'),
(15, 'Gfhsth', 'Fdsg', 4, '29', 'C'),
(16, 'Ddeg', 'Ftrhsdh', 20, '29', 'C'),
(17, 'Test', 'Test', 2, '45', 'K'),
(18, 'Ewgtfr', 'Reshy', 2, '45', 'J'),
(19, 'Test', 'Test', 4, '22', 'D'),
(20, 'Booking Back', 'To Normal', 4, '24', 'B');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `price` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `origin`, `destination`, `price`) VALUES
(1, 'City A', 'City B', 100),
(2, 'City A', 'City C', 264),
(3, 'City A', 'City D', 343),
(4, 'City B', 'City C', 576),
(5, 'City H', 'City C', 267),
(6, 'City F', 'City J', 775),
(7, 'City E', 'City G', 57),
(8, 'City I', 'City J', 890),
(9, 'City J', 'City K', 864),
(13, 'City Y', 'City S', 100),
(14, 'City A', 'City B', 120),
(15, 'City H', 'City Y', 397),
(16, 'City F', 'City K', 457),
(17, 'City S', 'City C', 100),
(18, 'City B', 'City G', 300),
(19, 'City F', 'City H', 75);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_credentials`
--
ALTER TABLE `admin_credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blacklist`
--
ALTER TABLE `blacklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flights_ibfk_1` (`route_id`);

--
-- Indexes for table `flight_bookings`
--
ALTER TABLE `flight_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flight_bookings_ibfk_1` (`flight_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_credentials`
--
ALTER TABLE `admin_credentials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blacklist`
--
ALTER TABLE `blacklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `flight_bookings`
--
ALTER TABLE `flight_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `flights_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `flight_bookings`
--
ALTER TABLE `flight_bookings`
  ADD CONSTRAINT `flight_bookings_ibfk_1` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
