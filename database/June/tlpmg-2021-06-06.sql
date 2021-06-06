-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 06, 2021 at 07:46 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tlpmg`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin'),
(2, 'ronie', 'ronie');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `item_code` varchar(128) NOT NULL,
  `item_name` varchar(256) NOT NULL,
  `qty` int(12) NOT NULL,
  `item_description` text NOT NULL,
  `item_price` decimal(12,2) NOT NULL,
  `market_original_price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `barcode`, `item_code`, `item_name`, `qty`, `item_description`, `item_price`, `market_original_price`) VALUES
(1, '09151215325', 'S-ITEM', 'Sample Item', -14, 'desc', '12.00', '18.00'),
(2, '23523521', 'bareta', 'bareta_bareta', 101, 'sample', '20.00', '10.00'),
(3, '0981241535235', 'dishwasher', 'dishwash', -80, 'sample', '90.00', '10.00'),
(4, '0256242523', 'shampoo', 'sun silk', 100, 'Shampopo', '9.00', '7.00'),
(5, '098989898989', 'COMP-RAM', '2GB ATI DDR2 RAM MODULE', 5, 'For Old Computers', '250.00', '100.00'),
(6, '525252352352', 'COMP-HEATSINK', 'ATI HEAT SINK FAN', 10, 'FOR OLD COMPUTERS', '70.00', '50.00'),
(7, '35009124124153', 'MED', 'BAND AID ', -10, 'for small scratches', '7.00', '5.00');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_cost`
--

CREATE TABLE `inventory_cost` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `total_cost` int(12) NOT NULL,
  `date_added` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `inventory_cost`
--

INSERT INTO `inventory_cost` (`id`, `item_id`, `total_cost`, `date_added`) VALUES
(3, 1, 12, '2020-07-06'),
(4, 2, 200, '2020-07-31'),
(5, 3, 22, '2020-07-31'),
(6, 2, 100, '2020-08-11'),
(7, 4, 100, '2020-08-11'),
(10, 5, 400, '2021-06-05'),
(11, 5, 0, '2021-06-05'),
(12, 5, 1000, '2021-06-05'),
(13, 5, 2000, '2021-06-05'),
(14, 4, 0, '2021-06-05'),
(15, 1, 20000, '2021-06-05'),
(16, 5, 300, '2021-06-05'),
(17, 6, 7000, '2021-06-05'),
(18, 7, 364, '2021-06-05'),
(19, 7, 420, '2021-06-05'),
(20, 2, 0, '2021-06-05'),
(21, 3, 20000, '2021-06-05');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `full_name` varchar(256) NOT NULL,
  `transaction_date` date NOT NULL,
  `address` varchar(256) NOT NULL,
  `phone_num` varchar(256) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id`, `full_name`, `transaction_date`, `address`, `phone_num`, `total_amount`, `amount_paid`) VALUES
(1, 'Juan Cruz', '2020-08-08', 'Angeles City', '09090912098', '144.00', '144.00'),
(2, 'Ronie Bituin', '2020-08-11', 'Angeles City', '09090912098', '4.00', '4.00'),
(3, 'Juan Cruz121212', '2020-08-11', 'Angeles City', '09090912098', '144.00', '144.00'),
(4, 'Ronie C. Bituin bituin', '2020-08-11', 'Angeles City', '09090912098', '360.00', '360.00'),
(5, 'Juan Cruz', '2020-08-11', 'Angeles City', '09090912098', '4385.00', '4385.00'),
(6, 'Juan Cruz', '2021-06-05', 'Angeles City', '09090912098', '22620.00', '22620.00');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_lists`
--

CREATE TABLE `transaction_lists` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty` int(12) NOT NULL,
  `adjusted_price` decimal(12,2) NOT NULL,
  `transaction_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_lists`
--

INSERT INTO `transaction_lists` (`id`, `transaction_id`, `item_id`, `qty`, `adjusted_price`, `transaction_date`, `subtotal`) VALUES
(35, 1, 2, 12, '12.00', '2020-08-11', '144.00'),
(36, 2, 1, 2, '2.00', '2020-08-08', '4.00'),
(37, 3, 2, 12, '12.00', '2020-08-11', '144.00'),
(38, 4, 3, 12, '30.00', '2020-08-11', '360.00'),
(39, 5, 1, 52, '52.00', '2020-08-11', '2704.00'),
(40, 5, 2, 41, '41.00', '2020-08-11', '1681.00'),
(41, 6, 7, 70, '60.00', '2021-06-05', '4200.00'),
(42, 6, 3, 200, '50.00', '2021-06-05', '10000.00'),
(43, 6, 1, 20, '421.00', '2021-06-05', '8420.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_cost`
--
ALTER TABLE `inventory_cost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_lists`
--
ALTER TABLE `transaction_lists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventory_cost`
--
ALTER TABLE `inventory_cost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaction_lists`
--
ALTER TABLE `transaction_lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory_cost`
--
ALTER TABLE `inventory_cost`
  ADD CONSTRAINT `inventory_cost_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_lists`
--
ALTER TABLE `transaction_lists`
  ADD CONSTRAINT `transaction_lists_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
