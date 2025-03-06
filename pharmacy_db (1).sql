-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2024 at 07:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pharmacy_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cosmetics`
--

CREATE TABLE `cosmetics` (
  `cosmetic_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cosmetics`
--

INSERT INTO `cosmetics` (`cosmetic_id`, `name`, `price`, `quantity`, `cost_price`, `description`, `company_id`, `category`, `expiry_date`) VALUES
(1, 'shampoo', 1000.00, 194, 500.00, 'nhfhc', 1, 'b', '2024-10-25');

-- --------------------------------------------------------

--
-- Table structure for table `cosmetic_profits`
--

CREATE TABLE `cosmetic_profits` (
  `profit_id` int(11) NOT NULL,
  `cosmetic_sale_id` int(11) NOT NULL,
  `cosmetic_name` varchar(255) NOT NULL,
  `profit` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cosmetic_profits`
--

INSERT INTO `cosmetic_profits` (`profit_id`, `cosmetic_sale_id`, `cosmetic_name`, `profit`, `created_at`) VALUES
(36, 2, 'shampoo', 500.00, '2024-10-08 04:46:27'),
(37, 3, 'shampoo', 500.00, '2024-10-08 04:50:31'),
(38, 4, 'shampoo', 500.00, '2024-10-08 11:38:05'),
(39, 5, 'shampoo', 500.00, '2024-10-08 12:15:22'),
(40, 6, 'shampoo', 500.00, '2024-10-08 12:16:56'),
(41, 7, 'shampoo', 500.00, '2024-10-08 12:17:16');

-- --------------------------------------------------------

--
-- Table structure for table `cosmetic_sales`
--

CREATE TABLE `cosmetic_sales` (
  `sale_id` int(11) NOT NULL,
  `cosmetic_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `quantity_sold` int(11) NOT NULL DEFAULT 0,
  `sale_date` datetime DEFAULT current_timestamp(),
  `pharmacist_id` int(11) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_mobile` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cosmetic_sales`
--

INSERT INTO `cosmetic_sales` (`sale_id`, `cosmetic_id`, `total_amount`, `quantity_sold`, `sale_date`, `pharmacist_id`, `customer_name`, `customer_mobile`) VALUES
(2, 0, 1000.00, 0, '2024-10-08 07:46:27', 4, 'ebisa', '0910675086'),
(3, 0, 1000.00, 0, '2024-10-08 07:50:31', 1, 'ebisa', '0910675086'),
(4, 0, 1000.00, 0, '2024-10-08 14:38:05', 1, 'ebisa', '0995764468'),
(5, 0, 1000.00, 0, '2024-10-08 15:15:22', 1, 'bonsa takale', '0921122141'),
(6, 0, 1000.00, 0, '2024-10-08 15:16:56', 1, 'bonsa takale', '0921122141'),
(7, 0, 1000.00, 0, '2024-10-08 15:17:16', 1, 'ebisa', '0995764468');

-- --------------------------------------------------------

--
-- Table structure for table `cosmetic_sale_items`
--

CREATE TABLE `cosmetic_sale_items` (
  `item_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `cosmetic_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cosmetic_sale_items`
--

INSERT INTO `cosmetic_sale_items` (`item_id`, `sale_id`, `cosmetic_name`, `price`, `quantity`, `total`) VALUES
(1, 2, 'shampoo', 1000.00, 1, 1000.00),
(2, 3, 'shampoo', 1000.00, 1, 1000.00),
(3, 4, 'shampoo', 1000.00, 1, 1000.00),
(4, 5, 'shampoo', 1000.00, 1, 1000.00),
(5, 6, 'shampoo', 1000.00, 1, 1000.00),
(6, 7, 'shampoo', 1000.00, 1, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('employee','manager') DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `username`, `password`, `role`, `created_at`, `security_question`, `security_answer`) VALUES
(1, 'mamo', '$2y$10$WiScvrhvSB7TGGEizfyHs.AFMNhQhg5W3jBkQOzbFf8XhqEPzVqla', 'manager', '2024-10-08 03:20:23', 'What is your favorite color?', 'Green'),
(4, 'meseret', '$2y$10$1u9C4stMgN3WyF/4yjSllOpdOqgywhfSrmInmMe2zOc7I3T.RPXui', 'employee', '2024-10-08 04:28:08', 'What is your favorite color?', 'Green');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(15) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issued_medicines`
--

CREATE TABLE `issued_medicines` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `issued_quantity` int(11) NOT NULL,
  `issue_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loss_adjustments`
--

CREATE TABLE `loss_adjustments` (
  `id` int(11) NOT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loss_adjustments`
--

INSERT INTO `loss_adjustments` (`id`, `medicine_id`, `quantity`, `reason`, `description`, `created_at`) VALUES
(1, 1, 5, 'broken', '', '2024-10-08 04:19:22'),
(2, 3, 5, 'damaged', '', '2024-10-09 09:48:02');

-- --------------------------------------------------------

--
-- Table structure for table `medicine`
--

CREATE TABLE `medicine` (
  `medicine_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `company_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `pin` varchar(100) NOT NULL,
  `batch_number` varchar(255) NOT NULL,
  `date` date DEFAULT NULL,
  `doc_number` varchar(50) DEFAULT NULL,
  `issued_to` varchar(100) DEFAULT NULL,
  `quantity_received` int(11) DEFAULT 0,
  `quantity_issued` int(11) DEFAULT 0,
  `loss_adjustment` int(11) DEFAULT 0,
  `balance` int(11) DEFAULT 0,
  `remarks` varchar(255) DEFAULT NULL,
  `facility_name` varchar(255) DEFAULT NULL,
  `product_info` varchar(255) DEFAULT NULL,
  `max_stock` int(11) DEFAULT NULL,
  `emergency_order_point` int(11) DEFAULT NULL,
  `amc` int(11) DEFAULT NULL,
  `strength` varchar(50) DEFAULT NULL,
  `dosage_form` varchar(100) DEFAULT NULL,
  `received_from` varchar(255) DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `initial_quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicine`
--

INSERT INTO `medicine` (`medicine_id`, `name`, `company_id`, `quantity`, `price`, `expiry_date`, `description`, `category`, `pin`, `batch_number`, `date`, `doc_number`, `issued_to`, `quantity_received`, `quantity_issued`, `loss_adjustment`, `balance`, `remarks`, `facility_name`, `product_info`, `max_stock`, `emergency_order_point`, `amc`, `strength`, `dosage_form`, `received_from`, `cost_price`, `initial_quantity`) VALUES
(1, 'advil', 1, 169, 500.00, '2024-10-06', 'Manufactured by FDC limitedB-8,MIDIC industrial', 'Pain & Pyretic', '', '12345', '2024-10-08', NULL, NULL, 0, 0, 0, 0, NULL, 'uganda', NULL, NULL, NULL, NULL, NULL, 'Strip', NULL, 200.00, 200),
(3, 'panadol', 1, 94, 600.00, '2024-10-19', 'Manufactured by FDC limitedB-8,MIDIC industrial', 'Antibiotics', '', '0911', '2024-10-09', NULL, NULL, 0, 0, 0, 0, NULL, 'dxhz', NULL, NULL, NULL, NULL, NULL, 'efg', NULL, 400.00, 200);

-- --------------------------------------------------------

--
-- Table structure for table `pharmacist`
--

CREATE TABLE `pharmacist` (
  `pharmacist_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacist`
--

INSERT INTO `pharmacist` (`pharmacist_id`, `name`, `username`, `password`, `email`, `role`) VALUES
(5, NULL, 'meseret takale', '$2y$10$/nLyotPnReAFVf/uLLw.l.H3JlSR4SwCHUjtgqEv5BhawK0sFTA5m', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_company`
--

CREATE TABLE `pharmacy_company` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pharmacy_company`
--

INSERT INTO `pharmacy_company` (`company_id`, `company_name`, `address`, `contact_number`) VALUES
(1, 'Obed Drug store', 'Dirre', '0921122141');

-- --------------------------------------------------------

--
-- Table structure for table `profits`
--

CREATE TABLE `profits` (
  `profit_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `profit` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cosmetic_name` varchar(255) DEFAULT NULL,
  `cosmetic_sale_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profits`
--

INSERT INTO `profits` (`profit_id`, `sale_id`, `medicine_name`, `profit`, `created_at`, `cosmetic_name`, `cosmetic_sale_id`) VALUES
(101, 16, 'advil', 3000.00, '2024-10-08 04:46:58', NULL, NULL),
(102, 17, 'advil', 300.00, '2024-10-08 04:50:19', NULL, NULL),
(103, 18, 'advil', 300.00, '2024-10-08 04:52:08', NULL, NULL),
(104, 19, 'advil', 3000.00, '2024-10-08 04:53:13', NULL, NULL),
(105, 20, 'advil', 300.00, '2024-10-08 11:40:59', NULL, NULL),
(106, 21, 'advil', 600.00, '2024-10-08 12:16:40', NULL, NULL),
(107, 22, 'advil', 300.00, '2024-10-09 09:41:48', NULL, NULL),
(108, 23, 'panadol', 20000.00, '2024-10-09 09:47:23', NULL, NULL),
(109, 24, 'panadol', 200.00, '2024-10-09 09:53:07', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `invoice_date` datetime DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_mobile` varchar(20) NOT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `quantity_sold` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `employee_id`, `total_amount`, `sale_date`, `invoice_date`, `customer_name`, `customer_mobile`, `medicine_id`, `cost_price`, `quantity_sold`) VALUES
(16, 4, 5000.00, '2024-10-08 07:46:58', NULL, 'bonsa takale', '0921122141', NULL, 0.00, NULL),
(17, 1, 500.00, '2024-10-08 07:50:19', NULL, 'eba', '0921122141', NULL, 0.00, NULL),
(18, 1, 500.00, '2024-10-08 07:52:08', NULL, 'bonsa takale', '0921122141', NULL, 0.00, NULL),
(19, 1, 5000.00, '2024-10-08 07:53:13', NULL, 'ebisa', '0910675086', NULL, 0.00, NULL),
(20, 1, 500.00, '2024-10-08 14:40:59', NULL, 'eba', '0921122141', NULL, 0.00, NULL),
(21, 1, 1000.00, '2024-10-08 15:16:40', NULL, 'ebisa', '0910675086', NULL, 0.00, NULL),
(22, 1, 500.00, '2024-10-09 12:41:48', NULL, 'eba', '0921122141', NULL, 0.00, NULL),
(23, 1, 60000.00, '2024-10-09 12:47:23', NULL, 'ebisa', '0910675086', NULL, 0.00, NULL),
(24, 1, 600.00, '2024-10-09 12:53:07', NULL, 'betty', '0912345678', NULL, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `detail_id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `sale_item_id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `medicine_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `quantity_sold` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`sale_item_id`, `sale_id`, `medicine_name`, `price`, `quantity`, `total`, `quantity_sold`) VALUES
(1, 16, 'advil', 500.00, 10, 5000.00, 0),
(2, 17, 'advil', 500.00, 1, 500.00, 0),
(3, 18, 'advil', 500.00, 1, 500.00, 0),
(4, 19, 'advil', 500.00, 10, 5000.00, 0),
(5, 20, 'advil', 500.00, 1, 500.00, 0),
(6, 21, 'advil', 500.00, 2, 1000.00, 0),
(7, 22, 'advil', 500.00, 1, 500.00, 0),
(8, 23, 'panadol', 600.00, 100, 60000.00, 0),
(9, 24, 'panadol', 600.00, 1, 600.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `pin` varchar(50) NOT NULL,
  `batch_number` varchar(50) NOT NULL,
  `expiry_date` date NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('active','expired','unsellable') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cosmetics`
--
ALTER TABLE `cosmetics`
  ADD PRIMARY KEY (`cosmetic_id`);

--
-- Indexes for table `cosmetic_profits`
--
ALTER TABLE `cosmetic_profits`
  ADD PRIMARY KEY (`profit_id`),
  ADD KEY `cosmetic_sale_id` (`cosmetic_sale_id`);

--
-- Indexes for table `cosmetic_sales`
--
ALTER TABLE `cosmetic_sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `fk_employee_id` (`pharmacist_id`);

--
-- Indexes for table `cosmetic_sale_items`
--
ALTER TABLE `cosmetic_sale_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `issued_medicines`
--
ALTER TABLE `issued_medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `loss_adjustments`
--
ALTER TABLE `loss_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `medicine`
--
ALTER TABLE `medicine`
  ADD PRIMARY KEY (`medicine_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `pharmacist`
--
ALTER TABLE `pharmacist`
  ADD PRIMARY KEY (`pharmacist_id`);

--
-- Indexes for table `pharmacy_company`
--
ALTER TABLE `pharmacy_company`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `profits`
--
ALTER TABLE `profits`
  ADD PRIMARY KEY (`profit_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `fk_cosmetic_sale` (`cosmetic_sale_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `fk_medicine` (`medicine_id`),
  ADD KEY `fk_employee` (`employee_id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`sale_item_id`),
  ADD KEY `sale_id` (`sale_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cosmetics`
--
ALTER TABLE `cosmetics`
  MODIFY `cosmetic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cosmetic_profits`
--
ALTER TABLE `cosmetic_profits`
  MODIFY `profit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `cosmetic_sales`
--
ALTER TABLE `cosmetic_sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cosmetic_sale_items`
--
ALTER TABLE `cosmetic_sale_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issued_medicines`
--
ALTER TABLE `issued_medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loss_adjustments`
--
ALTER TABLE `loss_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medicine`
--
ALTER TABLE `medicine`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pharmacist`
--
ALTER TABLE `pharmacist`
  MODIFY `pharmacist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pharmacy_company`
--
ALTER TABLE `pharmacy_company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `profits`
--
ALTER TABLE `profits`
  MODIFY `profit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `sale_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cosmetic_profits`
--
ALTER TABLE `cosmetic_profits`
  ADD CONSTRAINT `cosmetic_profits_ibfk_1` FOREIGN KEY (`cosmetic_sale_id`) REFERENCES `cosmetic_sales` (`sale_id`) ON DELETE CASCADE;

--
-- Constraints for table `cosmetic_sales`
--
ALTER TABLE `cosmetic_sales`
  ADD CONSTRAINT `fk_employee_id` FOREIGN KEY (`pharmacist_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE;

--
-- Constraints for table `cosmetic_sale_items`
--
ALTER TABLE `cosmetic_sale_items`
  ADD CONSTRAINT `cosmetic_sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `cosmetic_sales` (`sale_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`);

--
-- Constraints for table `issued_medicines`
--
ALTER TABLE `issued_medicines`
  ADD CONSTRAINT `issued_medicines_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`medicine_id`) ON DELETE CASCADE;

--
-- Constraints for table `loss_adjustments`
--
ALTER TABLE `loss_adjustments`
  ADD CONSTRAINT `loss_adjustments_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`medicine_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medicine`
--
ALTER TABLE `medicine`
  ADD CONSTRAINT `medicine_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `pharmacy_company` (`company_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profits`
--
ALTER TABLE `profits`
  ADD CONSTRAINT `fk_cosmetic_sale` FOREIGN KEY (`cosmetic_sale_id`) REFERENCES `cosmetic_sales` (`sale_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profits_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_medicine` FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`medicine_id`);

--
-- Constraints for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `sale_details_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`),
  ADD CONSTRAINT `sale_details_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`medicine_id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`sale_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
