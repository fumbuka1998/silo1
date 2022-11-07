-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 02, 2022 at 08:52 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `epmtz_derm`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `account_id` int(11) NOT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `account_name` varchar(255) NOT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `account_group_id` int(11) DEFAULT NULL,
  `account_code` int(11) DEFAULT NULL,
  `opening_balance` float NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `account_groups`
--

CREATE TABLE `account_groups` (
  `account_group_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `group_nature_id` int(11) DEFAULT NULL,
  `group_code` int(11) DEFAULT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `account_groups`
--

INSERT INTO `account_groups` (`account_group_id`, `group_name`, `description`, `parent_id`, `group_nature_id`, `group_code`, `level`) VALUES
(1, 'BALANCE SHEET', 'This is the preregistered parent group', NULL, 1, 0, 1),
(2, 'STATEMENT OF INCOME(P&L)', 'This is the preregistered parent group', NULL, 2, 0, 1),
(3, 'ASSETS', 'This is the preregistered account nature', 1, 1, 0, 2),
(4, 'LIABILITIES', 'This is the preregistered account nature', 1, 1, 0, 2),
(5, 'EQUITY', 'This is the preregistered account nature', 1, 1, 0, 2),
(6, 'REVENUE', 'This is the preregistered account nature', 2, 2, 0, 2),
(7, 'OTHER REVENUES', 'This is the preregistered account nature', 2, 2, 0, 2),
(8, 'COGS', 'This is the preregistered account nature', 2, 2, 0, 2),
(9, 'EXPENSES', 'This is the preregistered account nature', 2, 2, 0, 2),
(10, 'CURRENT ASSETS', 'This is the preregistered account nature level', 1, 3, 0, 3),
(11, 'NON CURRENT ASSETS', 'This is the preregistered account nature level', 1, 3, 0, 3),
(12, 'FIXED ASSETS', 'This is the preregistered account nature level', 1, 3, 0, 3),
(13, 'CURRENT LIABILITIES', 'This is the preregistered account nature level', 1, 4, 0, 3),
(14, 'NON CURRENT LIABILITIES', 'This is the preregistered account nature level', 1, 4, 0, 3),
(15, 'DIRECT EXPENSES', 'This is the preregistered account nature level', 2, 9, NULL, 3),
(16, 'INDIRECT EXPENSES', 'This is the preregistered account nature level', 2, 9, NULL, 3),
(17, 'TAXES', 'This is the preregistered account nature', 2, 2, NULL, 2),
(18, 'BANK', 'This is the preregistered account nature level\r\n', 1, 10, NULL, 4),
(19, 'CASH IN HAND', 'This is the preregistered account nature level\r\n', 1, 10, NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `activity_name` varchar(100) NOT NULL,
  `weight_percentage` float NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `allowances`
--

CREATE TABLE `allowances` (
  `id` int(11) NOT NULL,
  `allowance_name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `approval_chain_levels`
--

CREATE TABLE `approval_chain_levels` (
  `id` int(11) NOT NULL,
  `approval_module_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `level_name` varchar(100) NOT NULL DEFAULT 'Senior',
  `label` varchar(20) NOT NULL,
  `change_source` tinyint(4) NOT NULL DEFAULT 0,
  `special_level` tinyint(4) DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `approval_modules`
--

CREATE TABLE `approval_modules` (
  `id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `approval_modules`
--

INSERT INTO `approval_modules` (`id`, `module_name`) VALUES
(1, 'General Requisition'),
(2, 'Project Requisition'),
(3, 'Payment Request'),
(4, 'Payroll Approval');

-- --------------------------------------------------------

--
-- Table structure for table `approved_invoice_payment_cancellations`
--

CREATE TABLE `approved_invoice_payment_cancellations` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_approval_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `remarks` varchar(300) DEFAULT NULL,
  `cancelled_by` int(11) NOT NULL,
  `cancelled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `approved_requisition_payment_cancellations`
--

CREATE TABLE `approved_requisition_payment_cancellations` (
  `id` int(11) NOT NULL,
  `requisition_approval_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `remarks` varchar(300) DEFAULT NULL,
  `cancelled_by` int(11) NOT NULL,
  `cancelled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `approved_sub_contract_payment_cancellations`
--

CREATE TABLE `approved_sub_contract_payment_cancellations` (
  `id` int(11) NOT NULL,
  `sub_contract_payment_requisition_approval_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `cancelled_by` int(11) NOT NULL,
  `cancelled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `asset_item_id` int(11) NOT NULL,
  `asset_code` varchar(255) DEFAULT NULL,
  `book_value` int(11) NOT NULL,
  `useful_life` varchar(100) DEFAULT NULL,
  `salvage_value` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','disposed') NOT NULL,
  `registration_date` date DEFAULT NULL,
  `description` text NOT NULL,
  `ownership` enum('OWNED','HIRED') NOT NULL DEFAULT 'OWNED',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_cost_center_assignments`
--

CREATE TABLE `asset_cost_center_assignments` (
  `id` int(11) NOT NULL,
  `assignment_date` date NOT NULL,
  `location_id` int(11) NOT NULL,
  `source_project_id` int(11) DEFAULT NULL,
  `destination_project_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_cost_center_assignment_items`
--

CREATE TABLE `asset_cost_center_assignment_items` (
  `id` int(11) NOT NULL,
  `asset_sub_location_history_id` int(11) NOT NULL,
  `asset_cost_center_assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_depreciation_rates`
--

CREATE TABLE `asset_depreciation_rates` (
  `id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_depreciation_rate_items`
--

CREATE TABLE `asset_depreciation_rate_items` (
  `id` int(11) NOT NULL,
  `asset_depreciation_rate_id` int(11) NOT NULL,
  `asset_group_id` int(11) NOT NULL,
  `rate` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_groups`
--

CREATE TABLE `asset_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `project_nature_id` int(11) DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_handovers`
--

CREATE TABLE `asset_handovers` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `handover_date` date NOT NULL,
  `handler_id` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_handover_items`
--

CREATE TABLE `asset_handover_items` (
  `id` int(11) NOT NULL,
  `asset_handover_id` int(11) NOT NULL,
  `asset_sub_location_history_id` int(11) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_items`
--

CREATE TABLE `asset_items` (
  `id` int(11) NOT NULL,
  `asset_group_id` int(11) DEFAULT NULL,
  `asset_name` varchar(300) NOT NULL,
  `part_number` varchar(300) DEFAULT NULL,
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `asset_sub_location_histories`
--

CREATE TABLE `asset_sub_location_histories` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `book_value` double NOT NULL DEFAULT 0,
  `sub_location_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `received_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `attachment_name` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `employee_id` int(11) NOT NULL,
  `type` enum('check in','check out') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `account_number` varchar(100) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `swift_code` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `branch_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cancelled_purchase_orders`
--

CREATE TABLE `cancelled_purchase_orders` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `cancellation_date` date NOT NULL,
  `reason` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `casual_labour_budgets`
--

CREATE TABLE `casual_labour_budgets` (
  `budget_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `casual_labour_type_id` int(11) NOT NULL,
  `rate_mode` enum('daily','hourly','monthly') NOT NULL,
  `duration` varchar(10) NOT NULL,
  `no_of_workers` double NOT NULL,
  `rate` double NOT NULL,
  `description` text NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `casual_labour_types`
--

CREATE TABLE `casual_labour_types` (
  `type_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `category_parameters`
--

CREATE TABLE `category_parameters` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `alternative_phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `closed_purchase_orders`
--

CREATE TABLE `closed_purchase_orders` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `closing_date` date NOT NULL,
  `closing_remarks` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `company_details`
--

CREATE TABLE `company_details` (
  `id` int(11) NOT NULL,
  `company_name` varchar(300) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `vrn` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `tagline` text DEFAULT NULL,
  `corporate_color` varchar(50) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `company_documents`
--

CREATE TABLE `company_documents` (
  `id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contractors`
--

CREATE TABLE `contractors` (
  `id` int(11) NOT NULL,
  `contractor_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `alternative_phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contractor_accounts`
--

CREATE TABLE `contractor_accounts` (
  `id` int(11) NOT NULL,
  `contractor_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contras`
--

CREATE TABLE `contras` (
  `contra_id` int(11) NOT NULL,
  `contra_date` date NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `credit_account_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `remarks` text NOT NULL,
  `employee_id` int(11) NOT NULL,
  `datetime_posted` timestamp NOT NULL DEFAULT current_timestamp(),
  `confidentiality_chain_position` int(11) DEFAULT 0,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `exchange_rate` double NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contra_items`
--

CREATE TABLE `contra_items` (
  `contra_item_id` int(11) NOT NULL,
  `contra_id` int(11) NOT NULL,
  `debit_account_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `amount` float NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cost_centers`
--

CREATE TABLE `cost_centers` (
  `id` int(11) NOT NULL,
  `cost_center_name` varchar(150) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cost_center_accounts`
--

CREATE TABLE `cost_center_accounts` (
  `id` int(11) NOT NULL,
  `cost_center_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cost_center_imprest_voucher_items`
--

CREATE TABLE `cost_center_imprest_voucher_items` (
  `id` int(11) NOT NULL,
  `cost_center_id` int(11) NOT NULL,
  `imprest_voucher_service_item_id` int(11) DEFAULT NULL,
  `imprest_voucher_cash_item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cost_center_payment_voucher_items`
--

CREATE TABLE `cost_center_payment_voucher_items` (
  `id` int(11) NOT NULL,
  `cost_center_id` int(11) NOT NULL,
  `payment_voucher_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cost_center_purchase_orders`
--

CREATE TABLE `cost_center_purchase_orders` (
  `id` int(11) NOT NULL,
  `cost_center_id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cost_center_requisitions`
--

CREATE TABLE `cost_center_requisitions` (
  `id` int(11) NOT NULL,
  `cost_center_id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `currency_id` int(11) NOT NULL,
  `currency_name` varchar(60) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `rate_to_native` float NOT NULL DEFAULT 1,
  `is_native` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`currency_id`, `currency_name`, `symbol`, `rate_to_native`, `is_native`) VALUES
(1, 'Tanzanian Shillings', 'TSH', 1, 1),
(2, 'US Dollars', '$', 1, 0),
(5, 'EURO', '€', 1, 0),
(6, 'Pound ', '£', 1, 0),
(7, 'Dirham', 'AED', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(45) NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `department_payment_voucher_items`
--

CREATE TABLE `department_payment_voucher_items` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `payment_voucher_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `deployments`
--

CREATE TABLE `deployments` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `registration_number` varchar(20) NOT NULL,
  `driver` varchar(50) NOT NULL,
  `relax_station` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `deployment_attachments`
--

CREATE TABLE `deployment_attachments` (
  `id` int(11) NOT NULL,
  `deployment_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `deployment_category_parameters`
--

CREATE TABLE `deployment_category_parameters` (
  `id` int(11) NOT NULL,
  `category_parameter_id` int(11) NOT NULL,
  `deployment_id` int(11) NOT NULL,
  `answer` enum('YES','NO') DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `deployment_persons`
--

CREATE TABLE `deployment_persons` (
  `id` int(11) NOT NULL,
  `deployment_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `first_name` varchar(20) NOT NULL,
  `middle_name` varchar(20) DEFAULT NULL,
  `last_name` varchar(20) NOT NULL,
  `gender` enum('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
  `date_of_birth` date DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `alternative_phone` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `active` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Insert For Default Employee
--

INSERT INTO `employees` (`employee_id`, `department_id`, `position_id`, `first_name`, `middle_name`, `last_name`, `gender`, `date_of_birth`, `phone`, `alternative_phone`, `email`, `address`, `active`) VALUES (1, NULL, NULL, 'System', NULL, 'Admin', 'MALE', NULL, '', '', NULL, NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `employees_avatars`
--

CREATE TABLE `employees_avatars` (
  `avatar_id` int(11) NOT NULL,
  `avatar_name` varchar(60) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `datetime_uploaded` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employees_contracts`
--

CREATE TABLE `employees_contracts` (
  `contract_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `salary` float NOT NULL DEFAULT 0,
  `registrar_id` int(11) NOT NULL,
  `date_registered` date NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_accounts`
--

CREATE TABLE `employee_accounts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_allowances`
--

CREATE TABLE `employee_allowances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `allowance_id` int(11) NOT NULL,
  `allowance_amount` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_approval_chain_levels`
--

CREATE TABLE `employee_approval_chain_levels` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `approval_chain_level_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_banks`
--

CREATE TABLE `employee_banks` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL DEFAULT 0,
  `bank_id` int(11) NOT NULL,
  `branch` varchar(40) NOT NULL,
  `swift_code` varchar(40) NOT NULL,
  `account_no` varchar(50) NOT NULL,
  `start_date` varchar(50) NOT NULL,
  `end_date` varchar(50) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_confidentiality_levels`
--

CREATE TABLE `employee_confidentiality_levels` (
  `level_id` int(11) NOT NULL,
  `level_name` varchar(200) NOT NULL,
  `chain_position` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



--
-- Default Data
--

INSERT INTO `employee_confidentiality_levels` (`level_id`, `level_name`, `chain_position`, `created_by`, `created_at`) VALUES (1, 'DEFAULT', '0', '1', '2019-07-13 15:46:06');

-- --------------------------------------------------------


--
-- Table structure for table `employee_contracts`
--

CREATE TABLE `employee_contracts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_contract_closes`
--

CREATE TABLE `employee_contract_closes` (
  `id` int(11) NOT NULL,
  `employee_contract_id` int(11) NOT NULL,
  `close_date` date NOT NULL,
  `reason` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_designations`
--

CREATE TABLE `employee_designations` (
  `id` int(11) NOT NULL,
  `employee_contract_id` int(11) NOT NULL DEFAULT 0,
  `department_id` int(11) NOT NULL,
  `job_position_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_loans`
--

CREATE TABLE `employee_loans` (
  `id` int(11) NOT NULL,
  `loan_account_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `loan_approved_date` varchar(100) NOT NULL,
  `loan_deduction_start_date` varchar(100) NOT NULL,
  `total_loan_amount` float NOT NULL,
  `monthly_deduction_amount` float NOT NULL,
  `loan_balance_amount` float NOT NULL,
  `loan_application_form_path` varchar(255) DEFAULT NULL,
  `description` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'PENDING',
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_loan_repay`
--

CREATE TABLE `employee_loan_repay` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `employee_loan_id` int(11) NOT NULL,
  `paid_amount` float NOT NULL,
  `loan_balance_amount` float NOT NULL,
  `paid_date` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_salaries`
--

CREATE TABLE `employee_salaries` (
  `id` int(11) NOT NULL,
  `employee_contract_id` int(11) NOT NULL DEFAULT 0,
  `payroll_no` varchar(100) DEFAULT NULL,
  `salary` float NOT NULL DEFAULT 0,
  `subsistance` float NOT NULL DEFAULT 0,
  `responsibility` float NOT NULL DEFAULT 0,
  `currency_id` int(11) NOT NULL,
  `payment_mode` enum('bank','cash') NOT NULL,
  `tax_details` enum('taxable','non_taxable') NOT NULL,
  `ssf_contribution` enum('contribution','non_contribution') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee_ssfs`
--

CREATE TABLE `employee_ssfs` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `ssf_id` int(11) NOT NULL,
  `ssf_no` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enquiries`
--

CREATE TABLE `enquiries` (
  `id` int(11) NOT NULL,
  `enquiry_date` date NOT NULL,
  `enquiry_to` int(11) NOT NULL,
  `enquiry_for` int(11) NOT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `required_date` date NOT NULL,
  `comments` varchar(500) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('PENDING','REQUESTED') NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_asset_items`
--

CREATE TABLE `enquiry_asset_items` (
  `id` int(11) NOT NULL,
  `enquiry_id` int(11) NOT NULL,
  `asset_item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `remarks` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_material_items`
--

CREATE TABLE `enquiry_material_items` (
  `id` int(11) NOT NULL,
  `enquiry_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `remarks` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_service_items`
--

CREATE TABLE `enquiry_service_items` (
  `id` int(11) NOT NULL,
  `enquiry_id` int(11) NOT NULL,
  `description` varchar(300) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `measurement_unit_id` int(11) NOT NULL,
  `remarks` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `epm_v1_sessions`
--

CREATE TABLE `epm_v1_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_budgets`
--

CREATE TABLE `equipment_budgets` (
  `id` int(11) NOT NULL,
  `asset_item_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `rate_mode` varchar(100) NOT NULL,
  `rate` double NOT NULL,
  `duration` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_hiring_orders`
--

CREATE TABLE `equipment_hiring_orders` (
  `id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `required_date` date NOT NULL,
  `comments` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_hiring_order_items`
--

CREATE TABLE `equipment_hiring_order_items` (
  `id` int(11) NOT NULL,
  `asset_group_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `rate_mode` enum('daily','hourly') NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rate_updates`
--

CREATE TABLE `exchange_rate_updates` (
  `id` int(11) NOT NULL,
  `update_date` date NOT NULL,
  `currency_id` int(11) NOT NULL,
  `exchange_rate` float(10,3) NOT NULL,
  `updater_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `external_material_transfers`
--

CREATE TABLE `external_material_transfers` (
  `transfer_id` int(11) NOT NULL,
  `source_location_id` int(11) NOT NULL,
  `destination_location_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `transfer_date` date NOT NULL,
  `comments` text NOT NULL,
  `vehicle_number` varchar(20) DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `status` enum('ON TRANSIT','RECEIVED','CANCELLED') NOT NULL DEFAULT 'ON TRANSIT',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `external_material_transfer_grns`
--

CREATE TABLE `external_material_transfer_grns` (
  `id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `external_material_transfer_items`
--

CREATE TABLE `external_material_transfer_items` (
  `item_id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `source_sub_location_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `external_transfer_asset_items`
--

CREATE TABLE `external_transfer_asset_items` (
  `id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `source_sub_location_history_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `fingerprints`
--

CREATE TABLE `fingerprints` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `fingerprint` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_notes`
--

CREATE TABLE `goods_received_notes` (
  `grn_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `receive_date` date NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_note_asset_item_rejects`
--

CREATE TABLE `goods_received_note_asset_item_rejects` (
  `id` int(11) NOT NULL,
  `purchase_order_asset_item_id` int(11) DEFAULT NULL,
  `delivery_asset_item_id` int(11) DEFAULT NULL,
  `grn_id` int(11) NOT NULL,
  `rejected_quantity` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `goods_received_note_material_stock_items`
--

CREATE TABLE `goods_received_note_material_stock_items` (
  `item_id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `rejected_quantity` double DEFAULT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grn_asset_sub_location_histories`
--

CREATE TABLE `grn_asset_sub_location_histories` (
  `id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `asset_sub_location_history_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grn_invoices`
--

CREATE TABLE `grn_invoices` (
  `id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grn_received_services`
--

CREATE TABLE `grn_received_services` (
  `service_reception_id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `sub_location_Id` int(11) NOT NULL,
  `purchase_order_service_item_id` int(11) NOT NULL,
  `received_quantity` double NOT NULL,
  `rejected_quantity` double DEFAULT NULL,
  `rate` double NOT NULL,
  `remarks` varchar(500) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hifs`
--

CREATE TABLE `hifs` (
  `id` int(11) NOT NULL,
  `official_hif_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `hif_name` varchar(100) NOT NULL,
  `employer_deduction_percent` int(11) NOT NULL,
  `employee_deduction_percent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hired_assets`
--

CREATE TABLE `hired_assets` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `sub_location_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `asset_id` int(11) NOT NULL,
  `hired_date` date NOT NULL,
  `hiring_cost` double NOT NULL,
  `dead_line` date NOT NULL,
  `type` enum('SUPPLIERS','CLIENTS') NOT NULL DEFAULT 'SUPPLIERS',
  `status` enum('ACTIVE','INACTIVE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hired_equipments`
--

CREATE TABLE `hired_equipments` (
  `id` int(11) NOT NULL,
  `equipment_code` varchar(255) NOT NULL,
  `asset_group_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `rate_mode` enum('daily','hourly') NOT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `equipment_receipt_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hired_equipment_costs`
--

CREATE TABLE `hired_equipment_costs` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `hired_equipment_id` int(11) NOT NULL,
  `rate_mode` enum('daily','hourly') NOT NULL,
  `rate` double NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hired_equipment_receipts`
--

CREATE TABLE `hired_equipment_receipts` (
  `id` int(11) NOT NULL,
  `receipt_date` date NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `hiring_order_id` int(11) DEFAULT NULL,
  `comments` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `hse_certificates`
--

CREATE TABLE `hse_certificates` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` enum('EMPLOYEE','COMPANY') NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprests`
--

CREATE TABLE `imprests` (
  `id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `remarks` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_cash_items`
--

CREATE TABLE `imprest_cash_items` (
  `id` int(11) NOT NULL,
  `imprest_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_grns`
--

CREATE TABLE `imprest_grns` (
  `id` int(11) NOT NULL,
  `imprest_id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_material_items`
--

CREATE TABLE `imprest_material_items` (
  `id` int(11) NOT NULL,
  `imprest_id` int(11) NOT NULL,
  `goods_received_note_material_stock_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_vouchers`
--

CREATE TABLE `imprest_vouchers` (
  `id` int(11) NOT NULL,
  `imprest_date` date DEFAULT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `exchange_rate` double NOT NULL DEFAULT 1,
  `credit_account_id` int(11) NOT NULL,
  `debit_account_id` int(11) NOT NULL,
  `vat_inclusive` enum('VAT PRICED','VAT COMPONENT') DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `handler_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_asset_items`
--

CREATE TABLE `imprest_voucher_asset_items` (
  `id` int(11) NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL,
  `requisition_approval_asset_item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_cash_items`
--

CREATE TABLE `imprest_voucher_cash_items` (
  `id` int(11) NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL,
  `requisition_approval_cash_item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_contras`
--

CREATE TABLE `imprest_voucher_contras` (
  `id` int(11) NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL,
  `contra_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_material_items`
--

CREATE TABLE `imprest_voucher_material_items` (
  `id` int(11) NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL,
  `requisition_approval_material_item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_retired_cash`
--

CREATE TABLE `imprest_voucher_retired_cash` (
  `id` int(11) NOT NULL,
  `imprest_voucher_retirement_id` int(11) NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL,
  `imprest_voucher_cash_item_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_retired_services`
--

CREATE TABLE `imprest_voucher_retired_services` (
  `id` int(11) NOT NULL,
  `imprest_voucher_retirement_id` int(11) NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL,
  `imprest_voucher_service_item_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_retirements`
--

CREATE TABLE `imprest_voucher_retirements` (
  `id` int(11) NOT NULL,
  `retirement_date` date NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL DEFAULT 1,
  `sub_location_id` int(11) NOT NULL DEFAULT 1,
  `remarks` varchar(300) DEFAULT NULL,
  `is_examined` int(11) DEFAULT 0,
  `retirement_to` int(11) DEFAULT NULL,
  `examination_date` date DEFAULT NULL,
  `examined_by` int(11) DEFAULT NULL,
  `vat_inclusive` enum('VAT PRICED','VAT COMPONENT') DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_retirement_asset_items`
--

CREATE TABLE `imprest_voucher_retirement_asset_items` (
  `id` int(11) NOT NULL,
  `imprest_voucher_retirement_id` int(11) NOT NULL,
  `asset_item_id` int(11) NOT NULL,
  `book_value` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_retirement_grns`
--

CREATE TABLE `imprest_voucher_retirement_grns` (
  `id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `imprest_voucher_retirement_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_retirement_material_items`
--

CREATE TABLE `imprest_voucher_retirement_material_items` (
  `id` int(11) NOT NULL,
  `imprest_voucher_retirement_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `imprest_voucher_service_items`
--

CREATE TABLE `imprest_voucher_service_items` (
  `id` int(11) NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL,
  `requisition_approval_service_item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `incident_date` date NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `type` enum('NEAR MISS','ACCIDENT','BREAKDOWN','') NOT NULL,
  `causative_agent` enum('MECHANICAL','THIRD PARTY','WHEATHER CONDITION','TEAM MEMBER') NOT NULL,
  `location` varchar(50) NOT NULL,
  `is_reported` enum('YES','NO','') NOT NULL,
  `site_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `incident_job_cards`
--

CREATE TABLE `incident_job_cards` (
  `id` int(11) NOT NULL,
  `job_card_id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inspections`
--

CREATE TABLE `inspections` (
  `id` int(11) NOT NULL,
  `status` enum('Active','Overdue','Closed') NOT NULL,
  `inspection_date` date NOT NULL,
  `site_id` int(11) NOT NULL,
  `inspector_id` int(11) NOT NULL,
  `inspection_type` varchar(20) DEFAULT NULL,
  `location` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_categories`
--

CREATE TABLE `inspection_categories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `inspection_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_category_parameters`
--

CREATE TABLE `inspection_category_parameters` (
  `id` int(11) NOT NULL,
  `inspection_category_id` int(11) NOT NULL,
  `category_parameter_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_category_parameter_types`
--

CREATE TABLE `inspection_category_parameter_types` (
  `id` int(11) NOT NULL,
  `inspection_category_parameter_id` int(11) NOT NULL,
  `parameter_type_id` int(11) NOT NULL,
  `is_checked` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_job_cards`
--

CREATE TABLE `inspection_job_cards` (
  `id` int(11) NOT NULL,
  `job_card_id` int(11) NOT NULL,
  `inspection_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `internal_material_transfers`
--

CREATE TABLE `internal_material_transfers` (
  `transfer_id` int(11) NOT NULL,
  `transfer_date` date NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `internal_material_transfer_items`
--

CREATE TABLE `internal_material_transfer_items` (
  `item_id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `source_sub_location_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `internal_transfer_asset_items`
--

CREATE TABLE `internal_transfer_asset_items` (
  `id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `asset_sub_location_history_id` int(11) NOT NULL,
  `source_sub_location_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_locations`
--

CREATE TABLE `inventory_locations` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(100) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `vat_inclusive` int(11) NOT NULL DEFAULT 0,
  `vat_percentage` int(11) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `payment_terms` enum('due_on_receipt','net_ten','net_twenty','net_thirty','set_manually') DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_journal_voucher_items`
--

CREATE TABLE `invoice_journal_voucher_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `journal_voucher_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_payment_vouchers`
--

CREATE TABLE `invoice_payment_vouchers` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `job_cards`
--

CREATE TABLE `job_cards` (
  `id` int(11) NOT NULL,
  `priority` enum('High','Medium','Low','') NOT NULL,
  `date` date NOT NULL,
  `is_closed` int(11) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `job_card_labours`
--

CREATE TABLE `job_card_labours` (
  `id` int(11) NOT NULL,
  `job_card_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `job_card_services`
--

CREATE TABLE `job_card_services` (
  `id` int(11) NOT NULL,
  `job_card_labour_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `job_positions`
--

CREATE TABLE `job_positions` (
  `job_position_id` int(11) NOT NULL,
  `position_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_contras`
--

CREATE TABLE `journal_contras` (
  `id` int(11) NOT NULL,
  `contra_id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_payment_vouchers`
--

CREATE TABLE `journal_payment_vouchers` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_receipts`
--

CREATE TABLE `journal_receipts` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_vouchers`
--

CREATE TABLE `journal_vouchers` (
  `journal_id` int(11) NOT NULL,
  `transaction_date` date NOT NULL,
  `currency_id` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `journal_type` enum('SALES','CASH PAYMENT','PURCHASE','CASH RECEIPT','PURCHASE RETURN','SALES RETURN','JOURNAL') NOT NULL,
  `confidentiality_chain_position` int(11) DEFAULT 0,
  `remarks` varchar(500) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_voucher_attachments`
--

CREATE TABLE `journal_voucher_attachments` (
  `id` int(11) NOT NULL,
  `journal_voucher_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_voucher_credit_accounts`
--

CREATE TABLE `journal_voucher_credit_accounts` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `journal_voucher_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `narration` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_voucher_items`
--

CREATE TABLE `journal_voucher_items` (
  `item_id` int(11) NOT NULL,
  `journal_voucher_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `debit_account_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `narration` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_voucher_item_approved_cash_request_items`
--

CREATE TABLE `journal_voucher_item_approved_cash_request_items` (
  `id` int(11) NOT NULL,
  `journal_voucher_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `rate` double NOT NULL,
  `requisition_approval_cash_item_id` int(11) DEFAULT NULL,
  `requisition_approval_service_item_id` int(11) DEFAULT NULL,
  `requisition_approval_material_item_id` int(11) DEFAULT NULL,
  `requisition_approval_asset_item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_voucher_item_approved_invoice_items`
--

CREATE TABLE `journal_voucher_item_approved_invoice_items` (
  `id` int(11) NOT NULL,
  `journal_voucher_item_id` int(11) NOT NULL,
  `purchase_order_payment_request_approval_invoice_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `journal_voucher_item_approved_sub_contract_requisition_items`
--

CREATE TABLE `journal_voucher_item_approved_sub_contract_requisition_items` (
  `id` int(11) NOT NULL,
  `journal_voucher_item_id` int(11) NOT NULL,
  `sub_contract_payment_requisition_approval_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `loan_type` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_invoices`
--

CREATE TABLE `maintenance_invoices` (
  `id` int(11) NOT NULL,
  `outgoing_invoice_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_services`
--

CREATE TABLE `maintenance_services` (
  `service_id` int(11) NOT NULL,
  `service_date` date NOT NULL,
  `currency_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `location` varchar(500) NOT NULL,
  `remarks` varchar(500) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_service_items`
--

CREATE TABLE `maintenance_service_items` (
  `item_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `measurement_unit_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `description` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_service_receipts`
--

CREATE TABLE `maintenance_service_receipts` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `maintenance_service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_average_prices`
--

CREATE TABLE `material_average_prices` (
  `average_price_id` int(11) NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `transaction_date` datetime NOT NULL DEFAULT current_timestamp(),
  `sub_location_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `material_stock_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `average_price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_budgets`
--

CREATE TABLE `material_budgets` (
  `budget_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `material_item_id` int(11) NOT NULL,
  `quantity` double DEFAULT 0,
  `rate` double DEFAULT 0,
  `description` text DEFAULT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_costs`
--

CREATE TABLE `material_costs` (
  `material_cost_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `cost_date` date NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `source_sub_location_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL,
  `description` text DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `is_updated` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_cost_center_assignments`
--

CREATE TABLE `material_cost_center_assignments` (
  `id` int(11) NOT NULL,
  `assignment_date` date NOT NULL,
  `location_id` int(11) NOT NULL,
  `source_project_id` int(11) DEFAULT NULL,
  `destination_project_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_cost_center_assignment_items`
--

CREATE TABLE `material_cost_center_assignment_items` (
  `id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `material_cost_center_assignment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_disposals`
--

CREATE TABLE `material_disposals` (
  `id` int(11) NOT NULL,
  `disposal_date` date NOT NULL,
  `location_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_disposal_items`
--

CREATE TABLE `material_disposal_items` (
  `id` int(11) NOT NULL,
  `disposal_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `sub_location_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_items`
--

CREATE TABLE `material_items` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(500) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `part_number` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `thumbnail_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_item_categories`
--

CREATE TABLE `material_item_categories` (
  `category_id` int(11) NOT NULL,
  `project_nature_id` int(11) DEFAULT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  `tree_level` int(11) NOT NULL DEFAULT 1,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Default Data
--

INSERT INTO `material_item_categories` (`category_id`, `project_nature_id`, `parent_category_id`, `tree_level`, `category_name`, `description`) VALUES (1, NULL, NULL, '1', 'General Items', NULL);

--
-- Table structure for table `material_opening_stocks`
--

CREATE TABLE `material_opening_stocks` (
  `opening_stock_id` int(11) NOT NULL,
  `sub_location_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `material_stocks`
--

CREATE TABLE `material_stocks` (
  `stock_id` int(11) NOT NULL,
  `date_received` datetime NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `sub_location_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL DEFAULT 0,
  `project_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `measurement_units`
--

CREATE TABLE `measurement_units` (
  `unit_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `miscellaneous_budgets`
--

CREATE TABLE `miscellaneous_budgets` (
  `budget_id` int(11) NOT NULL,
  `expense_account_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `amount` double NOT NULL,
  `description` text DEFAULT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `official_hifs`
--

CREATE TABLE `official_hifs` (
  `id` int(11) NOT NULL,
  `hif_name` varchar(100) NOT NULL,
  `employee_deduction_percentage` varchar(10) NOT NULL,
  `employer_deduction_percentage` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `official_ssfs`
--

CREATE TABLE `official_ssfs` (
  `id` int(11) NOT NULL,
  `ssf_name` varchar(100) NOT NULL,
  `employee_deduction_percentage` varchar(10) NOT NULL,
  `employer_deduction_percentage` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ordered_pre_orders`
--

CREATE TABLE `ordered_pre_orders` (
  `ordered_pre_order_id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `outgoing_invoices`
--

CREATE TABLE `outgoing_invoices` (
  `id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `reference` varchar(500) NOT NULL,
  `invoice_no` varchar(100) NOT NULL,
  `invoice_to` int(11) NOT NULL,
  `vat_percentage` int(11) NOT NULL DEFAULT 0,
  `currency_id` int(11) NOT NULL,
  `vat_inclusive` int(1) NOT NULL DEFAULT 0,
  `payment_terms` enum('due_on_receipt','net_ten','net_twenty','net_thirty','set_manually') DEFAULT NULL,
  `bank_details` varchar(1000) NOT NULL,
  `notes` varchar(10000) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `outgoing_invoice_items`
--

CREATE TABLE `outgoing_invoice_items` (
  `item_id` int(11) NOT NULL,
  `outgoing_invoice_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `measurement_unit_id` int(11) DEFAULT NULL,
  `rate` double NOT NULL,
  `description` varchar(1000) NOT NULL,
  `maintenance_service_item_id` int(11) DEFAULT NULL,
  `stock_sale_asset_item_id` int(11) DEFAULT NULL,
  `project_certificate_id` int(11) DEFAULT NULL,
  `stock_sale_material_item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `owned_equipment_costs`
--

CREATE TABLE `owned_equipment_costs` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `asset_id` int(11) NOT NULL,
  `rate_mode` enum('daily','hourly') NOT NULL,
  `rate` double NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `parameter_types`
--

CREATE TABLE `parameter_types` (
  `id` int(11) NOT NULL,
  `category_parameter_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_request_approval_journal_vouchers`
--

CREATE TABLE `payment_request_approval_journal_vouchers` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_approval_id` int(11) NOT NULL,
  `journal_voucher_id` int(11) NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_vouchers`
--

CREATE TABLE `payment_vouchers` (
  `payment_voucher_id` int(11) NOT NULL,
  `cheque_number` varchar(100) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `reference` varchar(60) DEFAULT NULL,
  `credit_account_id` int(11) DEFAULT NULL,
  `payee` varchar(100) NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `exchange_rate` double NOT NULL DEFAULT 1,
  `vat_percentage` double NOT NULL DEFAULT 0,
  `withholding_tax` double DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `is_printed` int(11) DEFAULT NULL,
  `confidentiality_chain_position` int(11) DEFAULT 0,
  `employee_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_voucher_credit_accounts`
--

CREATE TABLE `payment_voucher_credit_accounts` (
  `id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `amount` double NOT NULL,
  `narration` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_voucher_grns`
--

CREATE TABLE `payment_voucher_grns` (
  `id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_voucher_items`
--

CREATE TABLE `payment_voucher_items` (
  `payment_voucher_item_id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL,
  `debit_account_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `vat_amount` double NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_voucher_item_approved_cash_request_items`
--

CREATE TABLE `payment_voucher_item_approved_cash_request_items` (
  `id` int(11) NOT NULL,
  `payment_voucher_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `rate` double NOT NULL,
  `requisition_approval_cash_item_id` int(11) DEFAULT NULL,
  `requisition_approval_service_item_id` int(11) DEFAULT NULL,
  `requisition_approval_material_item_id` int(11) DEFAULT NULL,
  `requisition_approval_asset_item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_voucher_item_approved_invoice_items`
--

CREATE TABLE `payment_voucher_item_approved_invoice_items` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_approval_invoice_item_id` int(11) NOT NULL,
  `payment_voucher_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_voucher_item_approved_sub_contract_requisition_items`
--

CREATE TABLE `payment_voucher_item_approved_sub_contract_requisition_items` (
  `id` int(11) NOT NULL,
  `payment_voucher_item_id` int(11) NOT NULL,
  `sub_contract_payment_requisition_approval_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(11) NOT NULL,
  `payroll_for` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `approval_module_id` int(11) NOT NULL DEFAULT 4,
  `foward_to` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `approved_by` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_approvals`
--

CREATE TABLE `payroll_approvals` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `approved_date` date NOT NULL,
  `approving_coments` varchar(255) DEFAULT NULL,
  `approval_chain_level_id` int(11) NOT NULL,
  `returned_chain_level_id` int(11) DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `is_final` int(11) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_employee_allowances`
--

CREATE TABLE `payroll_employee_allowances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `allowance_name` varchar(100) NOT NULL,
  `allowance_amount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_employee_basic_info`
--

CREATE TABLE `payroll_employee_basic_info` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `basic_salary` float NOT NULL,
  `gross_salary` float NOT NULL,
  `deducted_nssf` float NOT NULL,
  `taxable_amount` float NOT NULL,
  `paye` float NOT NULL,
  `heslb_loan` float DEFAULT NULL,
  `heslb_loan_repay` float DEFAULT NULL,
  `heslb_loan_balance` float DEFAULT NULL,
  `company_loan` float DEFAULT NULL,
  `company_loan_repay` float DEFAULT NULL,
  `company_loan_balance` float DEFAULT NULL,
  `advance_payment` float DEFAULT NULL,
  `net_pay` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_employer_deductions`
--

CREATE TABLE `payroll_employer_deductions` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `deduction_name` varchar(100) NOT NULL,
  `deduction_amount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_journal_vouchers`
--

CREATE TABLE `payroll_journal_vouchers` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `journal_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_payments`
--

CREATE TABLE `payroll_payments` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `loan_name` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_payment_vouchers`
--

CREATE TABLE `payroll_payment_vouchers` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL,
  `payment_name` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permanent_labour_budgets`
--

CREATE TABLE `permanent_labour_budgets` (
  `budget_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `job_position_id` int(11) NOT NULL,
  `rate_mode` enum('daily','hourly','monthly') NOT NULL,
  `duration` double NOT NULL,
  `no_of_staff` int(5) NOT NULL,
  `allowance_rate` double NOT NULL,
  `salary_rate` double NOT NULL,
  `description` text NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permanent_labour_costs`
--

CREATE TABLE `permanent_labour_costs` (
  `permanent_labour_cost_id` int(11) NOT NULL,
  `project_team_member_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `working_mode` enum('date_range','hours','single_day') NOT NULL,
  `cost_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `duration` float NOT NULL DEFAULT 1,
  `salary_rate` double NOT NULL,
  `allowance_rate` double NOT NULL,
  `description` text NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `name`) VALUES
(1, 'Human Resources'),
(2, 'Clients'),
(4, 'Projects'),
(7, 'Procurements'),
(8, 'Inventory'),
(9, 'Finance'),
(10, 'Tenders'),
(12, 'Administrative Actions'),
(14, 'Assets'),
(15, 'Requisitions'),
(16, 'Executive Reports'),
(17, 'Contractors');

-- --------------------------------------------------------

--
-- Table structure for table `permission_privileges`
--

CREATE TABLE `permission_privileges` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `privilege` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permission_privileges`
--

INSERT INTO `permission_privileges` (`id`, `parent_id`, `privilege`) VALUES
(1, 1, 'Employee List'),
(2, 1, 'Payroll'),
(3, 1, 'Human Resource Settings'),
(4, 10, 'Tender Actions'),
(5, 10, 'Tenders Settings'),
(6, 2, 'Client Actions'),
(7, 4, 'Project Actions'),
(8, 4, 'Budgets'),
(9, 4, 'Planning'),
(10, 4, 'Finance'),
(11, 4, 'Project Team'),
(12, 4, 'Sub Contracts'),
(13, 4, 'Contract Reviews'),
(14, 4, 'Certificates'),
(15, 4, 'Projects Settings'),
(16, 17, 'Contractors List'),
(17, 17, 'Contractors Setting'),
(18, 17, 'Contractor Actions'),
(19, 15, 'Requisition Actions'),
(20, 7, 'Procurement Actions'),
(21, 7, 'Procurement Reports'),
(23, 8, 'Store Operations'),
(28, 8, 'Inventory Actions'),
(34, 14, 'Asset Actions'),
(35, 14, 'Assets Settings'),
(38, 9, 'Contra'),
(39, 9, 'Statements'),
(40, 9, 'Finance Actions'),
(41, 9, 'Approved Payments'),
(44, 12, 'Company Details'),
(45, 12, 'Approval Settings'),
(46, 12, 'Audit Trail'),
(47, 9, 'Receipts'),
(48, 9, 'Accounts'),
(49, 9, 'Make Payment'),
(50, 15, 'Approval Chain'),
(51, 7, 'Purchase Orders'),
(52, 7, 'Payment Request'),
(53, 7, 'Vendors'),
(54, 7, 'GRNs'),
(55, 9, 'Cheque List'),
(56, 14, 'Assets Reports'),
(57, 7, 'Orders Approval'),
(58, 1, 'Timesheet'),
(59, 1, 'Register Employee'),
(60, 8, 'Inventory Settings'),
(61, 8, 'Inventory Reports'),
(62, 8, 'All Locations'),
(63, 9, 'Finance Settings'),
(64, 4, 'Project Reports'),
(65, 9, 'Debts'),
(66, 9, 'Update Exchange Rate'),
(67, 9, 'Payroll Payments'),
(68, 14, 'Hire Assets'),
(69, 4, 'Edit material cost');

-- --------------------------------------------------------

--
-- Table structure for table `procurement_attachments`
--

CREATE TABLE `procurement_attachments` (
  `id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL,
  `reffering_id` int(11) NOT NULL,
  `reffering_to` enum('ORDER','P-INV','O-INV','GRN') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `project_name` text NOT NULL,
  `description` text NOT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `site_location` varchar(200) DEFAULT NULL,
  `reference_number` varchar(60) DEFAULT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_accounts`
--

CREATE TABLE `project_accounts` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_attachments`
--

CREATE TABLE `project_attachments` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_categories`
--

CREATE TABLE `project_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(45) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_certificates`
--

CREATE TABLE `project_certificates` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `certificate_number` varchar(50) NOT NULL,
  `certificate_date` date NOT NULL,
  `certified_amount` double NOT NULL,
  `comments` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_certificate_invoices`
--

CREATE TABLE `project_certificate_invoices` (
  `id` int(11) NOT NULL,
  `project_certificate_id` int(11) NOT NULL,
  `outgoing_invoice_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_certificate_receipts`
--

CREATE TABLE `project_certificate_receipts` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `certificate_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_closures`
--

CREATE TABLE `project_closures` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `closure_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_contract_reviews`
--

CREATE TABLE `project_contract_reviews` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `review_date` date NOT NULL,
  `plus_or_minus_duration` enum('plus','minus') NOT NULL DEFAULT 'plus',
  `plus_or_minus_contract_sum` enum('plus','minus') NOT NULL DEFAULT 'plus',
  `duration_type` enum('days','months','years') NOT NULL DEFAULT 'months',
  `duration_variation` int(11) NOT NULL,
  `contract_sum_variation` double NOT NULL,
  `reason` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_imprest_voucher_items`
--

CREATE TABLE `project_imprest_voucher_items` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `imprest_voucher_service_item_id` int(11) DEFAULT NULL,
  `imprest_voucher_cash_item_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_payment_voucher_items`
--

CREATE TABLE `project_payment_voucher_items` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `payment_voucher_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plans`
--

CREATE TABLE `project_plans` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `budget` int(11) DEFAULT NULL,
  `equipment_n_material_budget` double DEFAULT 0,
  `labour_budget` double DEFAULT 0,
  `currency_id` int(11) DEFAULT NULL,
  `exchange_rate` double DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plan_tasks`
--

CREATE TABLE `project_plan_tasks` (
  `id` int(11) NOT NULL,
  `project_plan_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `output_per_day` double NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plan_task_casual_labour_budgets`
--

CREATE TABLE `project_plan_task_casual_labour_budgets` (
  `id` int(11) NOT NULL,
  `project_plan_task_id` int(11) NOT NULL,
  `casual_labour_type_id` int(11) NOT NULL,
  `rate_mode` enum('daily','hourly','monthly') NOT NULL,
  `duration` double NOT NULL,
  `no_of_workers` int(11) NOT NULL,
  `rate` double NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plan_task_equipment_budgets`
--

CREATE TABLE `project_plan_task_equipment_budgets` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `project_plan_task_id` int(11) DEFAULT NULL,
  `rate_mode` enum('daily','hourly','monthly') NOT NULL,
  `rate` double NOT NULL,
  `duration` double NOT NULL,
  `quantity` double NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plan_task_executions`
--

CREATE TABLE `project_plan_task_executions` (
  `id` int(11) NOT NULL,
  `project_plan_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `execution_date` date NOT NULL,
  `executed_quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plan_task_execution_casual_labour`
--

CREATE TABLE `project_plan_task_execution_casual_labour` (
  `id` int(11) NOT NULL,
  `plan_task_execution_id` int(11) NOT NULL,
  `casual_labour_type_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `rate_mode` enum('Daily','Hourly','Monthly') NOT NULL,
  `rate` int(11) NOT NULL,
  `no_of_workers` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `description` varchar(300) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plan_task_execution_equipments`
--

CREATE TABLE `project_plan_task_execution_equipments` (
  `id` int(11) NOT NULL,
  `plan_task_execution_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `rate_mode` enum('Daily','Hourly','Monthly') NOT NULL,
  `rate` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` varchar(300) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plan_task_execution_material_costs`
--

CREATE TABLE `project_plan_task_execution_material_costs` (
  `id` int(11) NOT NULL,
  `plan_task_execution_id` int(11) NOT NULL,
  `material_cost_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_plan_task_material_budgets`
--

CREATE TABLE `project_plan_task_material_budgets` (
  `id` int(11) NOT NULL,
  `project_plan_task_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_purchase_orders`
--

CREATE TABLE `project_purchase_orders` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_requisitions`
--

CREATE TABLE `project_requisitions` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_special_budgets`
--

CREATE TABLE `project_special_budgets` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `labour_amount` double NOT NULL,
  `material_amount` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_team_members`
--

CREATE TABLE `project_team_members` (
  `member_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `job_position_id` int(11) NOT NULL,
  `manager_access` tinyint(1) NOT NULL DEFAULT 0,
  `employee_id` int(11) NOT NULL,
  `date_assigned` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `assignor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `order_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `location_id` int(11) NOT NULL,
  `stakeholder_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `comments` text NOT NULL,
  `vat_inclusive` enum('VAT PRICED','VAT COMPONENT') DEFAULT NULL,
  `vat_percentage` double NOT NULL DEFAULT 18,
  `freight` double NOT NULL DEFAULT 0,
  `inspection_and_other_charges` double NOT NULL DEFAULT 0,
  `status` enum('PENDING','RECEIVED','CLOSED','CANCELLED') NOT NULL,
  `is_printed` int(11) DEFAULT NULL,
  `employee_id` int(11) NOT NULL,
  `handler_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_asset_items`
--

CREATE TABLE `purchase_order_asset_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `asset_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_grns`
--

CREATE TABLE `purchase_order_grns` (
  `id` int(11) NOT NULL,
  `goods_received_note_id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `freight` double DEFAULT 0,
  `insurance` double DEFAULT 0,
  `other_charges` double DEFAULT 0,
  `import_duty` double DEFAULT 0,
  `vat` double DEFAULT 0,
  `cpf` double DEFAULT 0,
  `rdl` double DEFAULT 0,
  `wharfage` double DEFAULT 0,
  `service_fee` double DEFAULT 0,
  `clearance_charges` double NOT NULL DEFAULT 0,
  `clearance_vat` double NOT NULL DEFAULT 0,
  `clearance_currency_id` int(11) NOT NULL DEFAULT 1,
  `factor` double NOT NULL DEFAULT 1,
  `exchange_rate` double NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_invoices`
--

CREATE TABLE `purchase_order_invoices` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_material_items`
--

CREATE TABLE `purchase_order_material_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_material_item_grn_items`
--

CREATE TABLE `purchase_order_material_item_grn_items` (
  `id` int(11) NOT NULL,
  `goods_received_note_item_id` int(11) NOT NULL,
  `purchase_order_material_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payment_requests`
--

CREATE TABLE `purchase_order_payment_requests` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `finalized_date` date DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `requester_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `approval_module_id` int(11) NOT NULL,
  `forward_to` int(11) DEFAULT NULL,
  `finalizer_id` int(11) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payment_request_approvals`
--

CREATE TABLE `purchase_order_payment_request_approvals` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_id` int(11) NOT NULL,
  `approval_chain_level_id` int(11) NOT NULL,
  `approval_date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `forward_to` int(11) DEFAULT NULL,
  `is_final` tinyint(1) NOT NULL DEFAULT 0,
  `is_printed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payment_request_approval_cash_items`
--

CREATE TABLE `purchase_order_payment_request_approval_cash_items` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_approval_id` int(11) NOT NULL,
  `purchase_order_payment_request_cash_item_id` int(11) NOT NULL,
  `approved_amount` double NOT NULL,
  `claimed_by` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payment_request_approval_invoice_items`
--

CREATE TABLE `purchase_order_payment_request_approval_invoice_items` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_approval_id` int(11) NOT NULL,
  `purchase_order_payment_request_invoice_item_id` int(11) NOT NULL,
  `approved_amount` double NOT NULL,
  `claimed_by` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payment_request_approval_payment_vouchers`
--

CREATE TABLE `purchase_order_payment_request_approval_payment_vouchers` (
  `id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL,
  `purchase_order_payment_request_approval_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payment_request_attachments`
--

CREATE TABLE `purchase_order_payment_request_attachments` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payment_request_cash_items`
--

CREATE TABLE `purchase_order_payment_request_cash_items` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `requested_amount` double NOT NULL,
  `claimed_by` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_payment_request_invoice_items`
--

CREATE TABLE `purchase_order_payment_request_invoice_items` (
  `id` int(11) NOT NULL,
  `purchase_order_payment_request_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `requested_amount` double NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_service_items`
--

CREATE TABLE `purchase_order_service_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `description` varchar(100) NOT NULL,
  `measurement_unit_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `receipts`
--

CREATE TABLE `receipts` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `debit_account_id` int(11) NOT NULL,
  `credit_account_id` int(11) NOT NULL,
  `receipt_date` date NOT NULL,
  `reference` varchar(100) NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `exchange_rate` double NOT NULL,
  `withholding_tax` double DEFAULT NULL,
  `remarks` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `receipt_items`
--

CREATE TABLE `receipt_items` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `registered_certificates`
--

CREATE TABLE `registered_certificates` (
  `id` int(11) NOT NULL,
  `hse_certificate_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rejected_payrolls`
--

CREATE TABLE `rejected_payrolls` (
  `id` int(11) NOT NULL,
  `payroll_id` int(11) NOT NULL,
  `current_level` int(11) NOT NULL,
  `reject_coments` int(11) NOT NULL,
  `status` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisitions`
--

CREATE TABLE `requisitions` (
  `requisition_id` int(11) NOT NULL,
  `approval_module_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `request_date` date NOT NULL,
  `required_date` date DEFAULT NULL,
  `finalized_date` date DEFAULT NULL,
  `requester_id` int(11) NOT NULL,
  `finalizer_id` int(11) DEFAULT NULL,
  `requesting_comments` text DEFAULT NULL,
  `finalizing_comments` text DEFAULT NULL,
  `vat_inclusive` enum('VAT PRICED','VAT COMPONENT') DEFAULT NULL,
  `vat_percentage` double NOT NULL DEFAULT 18,
  `confidentiality_chain_position` int(11) DEFAULT 0,
  `freight` double NOT NULL DEFAULT 0,
  `inspection_and_other_charges` double NOT NULL DEFAULT 0,
  `foward_to` int(11) DEFAULT NULL,
  `status` enum('INCOMPLETE','PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING',
  `is_printed` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approvals`
--

CREATE TABLE `requisition_approvals` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `approved_date` date NOT NULL,
  `approving_comments` text NOT NULL,
  `approval_chain_level_id` int(11) NOT NULL,
  `returned_chain_level_id` int(11) DEFAULT NULL,
  `has_sources` tinyint(4) NOT NULL DEFAULT 0,
  `vat_inclusive` enum('VAT PRICED','VAT COMPONENT') DEFAULT NULL,
  `vat_percentage` double NOT NULL DEFAULT 18,
  `freight` double NOT NULL DEFAULT 0,
  `inspection_and_other_charges` double NOT NULL DEFAULT 0,
  `forward_to` int(11) DEFAULT NULL,
  `is_final` tinyint(4) NOT NULL DEFAULT 0,
  `is_printed` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approval_asset_items`
--

CREATE TABLE `requisition_approval_asset_items` (
  `id` int(11) NOT NULL,
  `requisition_approval_id` int(11) NOT NULL,
  `requisition_asset_item_id` int(11) NOT NULL,
  `source_type` enum('vendor','cash','store','imprest') NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `payee` varchar(100) DEFAULT NULL,
  `approved_quantity` double NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `approved_rate` double NOT NULL,
  `currency_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approval_cash_items`
--

CREATE TABLE `requisition_approval_cash_items` (
  `id` int(11) NOT NULL,
  `requisition_approval_id` int(11) NOT NULL,
  `requisition_cash_item_id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `approved_quantity` double NOT NULL,
  `approved_rate` double NOT NULL,
  `payee` varchar(100) DEFAULT NULL,
  `currency_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approval_cash_item_expense_accounts`
--

CREATE TABLE `requisition_approval_cash_item_expense_accounts` (
  `id` int(11) NOT NULL,
  `expense_account_id` int(11) DEFAULT NULL,
  `requisition_approval_id` int(11) NOT NULL,
  `requisition_cash_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approval_imprest_vouchers`
--

CREATE TABLE `requisition_approval_imprest_vouchers` (
  `id` int(11) NOT NULL,
  `requisition_approval_id` int(11) NOT NULL,
  `imprest_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approval_material_items`
--

CREATE TABLE `requisition_approval_material_items` (
  `id` int(11) NOT NULL,
  `requisition_approval_id` int(11) NOT NULL,
  `requisition_material_item_id` int(11) NOT NULL,
  `source_type` enum('store','vendor','cash','imprest') DEFAULT 'vendor',
  `vendor_id` int(11) DEFAULT NULL,
  `payee` varchar(100) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `approved_quantity` double NOT NULL,
  `approved_rate` double NOT NULL,
  `currency_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approval_material_item_expense_accounts`
--

CREATE TABLE `requisition_approval_material_item_expense_accounts` (
  `id` int(11) NOT NULL,
  `expense_account_id` int(11) DEFAULT NULL,
  `requisition_approval_id` int(11) NOT NULL,
  `requisition_material_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approval_payment_vouchers`
--

CREATE TABLE `requisition_approval_payment_vouchers` (
  `id` int(11) NOT NULL,
  `requisition_approval_id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_approval_service_items`
--

CREATE TABLE `requisition_approval_service_items` (
  `id` int(11) NOT NULL,
  `requisition_approval_id` int(11) NOT NULL,
  `requisition_service_item_id` int(11) NOT NULL,
  `approved_quantity` double NOT NULL,
  `approved_rate` double NOT NULL,
  `payee` varchar(100) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `source_type` enum('vendor','cash','imprest') NOT NULL,
  `vendor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_asset_items`
--

CREATE TABLE `requisition_asset_items` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `asset_item_id` int(11) NOT NULL,
  `requested_quantity` double NOT NULL,
  `requested_rate` double NOT NULL,
  `source_type` enum('vendor','cash','store','imprest') NOT NULL,
  `payee` varchar(100) DEFAULT NULL,
  `requested_vendor_id` int(11) DEFAULT NULL,
  `requested_account_id` int(11) DEFAULT NULL,
  `requested_location_id` int(11) DEFAULT NULL,
  `requested_currency_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_attachments`
--

CREATE TABLE `requisition_attachments` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_cash_items`
--

CREATE TABLE `requisition_cash_items` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `measurement_unit_id` int(11) NOT NULL,
  `requested_quantity` double DEFAULT 0,
  `requested_rate` double DEFAULT NULL,
  `payee` varchar(100) DEFAULT NULL,
  `requested_currency_id` int(11) NOT NULL DEFAULT 1,
  `requested_account_id` int(11) DEFAULT NULL,
  `expense_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_cash_item_tasks`
--

CREATE TABLE `requisition_cash_item_tasks` (
  `id` int(11) NOT NULL,
  `requisition_item_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_equipment_items`
--

CREATE TABLE `requisition_equipment_items` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `asset_group_id` int(11) NOT NULL,
  `requested_quantity` float DEFAULT 0,
  `requested_rate` float DEFAULT NULL,
  `rate_mode` enum('daily','hourly') NOT NULL,
  `duration` varchar(10) NOT NULL,
  `requested_currency_id` int(11) NOT NULL DEFAULT 1,
  `expense_account_id` int(11) DEFAULT NULL,
  `requested_vendor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_equipment_item_tasks`
--

CREATE TABLE `requisition_equipment_item_tasks` (
  `id` int(11) NOT NULL,
  `requisition_item_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_material_items`
--

CREATE TABLE `requisition_material_items` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `requested_quantity` double DEFAULT 0,
  `requested_rate` double DEFAULT NULL,
  `requested_currency_id` int(11) NOT NULL DEFAULT 1,
  `expense_account_id` int(11) DEFAULT NULL,
  `source_type` enum('store','vendor','cash','imprest') DEFAULT 'vendor',
  `payee` varchar(100) DEFAULT NULL,
  `requested_vendor_id` int(11) DEFAULT NULL,
  `requested_location_id` int(11) DEFAULT NULL,
  `requested_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_material_item_tasks`
--

CREATE TABLE `requisition_material_item_tasks` (
  `id` int(11) NOT NULL,
  `requisition_item_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_purchase_orders`
--

CREATE TABLE `requisition_purchase_orders` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_service_items`
--

CREATE TABLE `requisition_service_items` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `measurement_unit_id` int(11) NOT NULL,
  `requested_quantity` double NOT NULL,
  `requested_rate` double NOT NULL,
  `payee` varchar(100) DEFAULT NULL,
  `source_type` enum('vendor','cash','imprest') NOT NULL,
  `requested_account_id` int(11) DEFAULT NULL,
  `requested_vendor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `revised_tasks`
--

CREATE TABLE `revised_tasks` (
  `id` int(11) NOT NULL,
  `revision_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `description` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `revision`
--

CREATE TABLE `revision` (
  `id` int(11) NOT NULL,
  `revision_date` date NOT NULL,
  `project_id` int(11) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site_diary_compliances`
--

CREATE TABLE `site_diary_compliances` (
  `id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site_diary_compliance_statuses`
--

CREATE TABLE `site_diary_compliance_statuses` (
  `id` int(11) NOT NULL,
  `site_diary_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `site_topics`
--

CREATE TABLE `site_topics` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ssfs`
--

CREATE TABLE `ssfs` (
  `id` int(11) NOT NULL,
  `ssf_name` varchar(100) NOT NULL,
  `employer_deduction_percent` int(11) NOT NULL,
  `employee_deduction_percent` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ssf_groups`
--

CREATE TABLE `ssf_groups` (
  `id` int(11) NOT NULL,
  `employer_name` varchar(255) NOT NULL,
  `employer_no` varchar(255) NOT NULL,
  `regional_code` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `ssf_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ssf_group_stations`
--

CREATE TABLE `ssf_group_stations` (
  `id` int(11) NOT NULL,
  `ssf_group_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stakeholders`
--

CREATE TABLE `stakeholders` (
  `stakeholder_id` int(11) NOT NULL,
  `stakeholder_name` varchar(100) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `alternative_phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stakeholder_evaluation_factors`
--

CREATE TABLE `stakeholder_evaluation_factors` (
  `id` int(11) NOT NULL,
  `general_experience` enum('Registered 1-2 years','Registered 2-3 years','Registered 3 years and above') DEFAULT NULL,
  `certificate_of_completion` enum('1 Certificate From a recognised Institution','2 Certificate from a recognised Institution','1 Certificate from a non-recognised Institution','2 Certificate from a non-recognised institution') DEFAULT NULL,
  `two_team_supervisors_with_atleast_a_bachelor_degree` enum('1 Supervisor within relevant field','2 Supervisor within relevant field','1 Supervisor not in relevant field') DEFAULT NULL,
  `financial_capacity_of_at_least_payment_of_workers_for_one_month` enum('Contract amounts under 5 Million','Contracts amounts between 5 and 20 Million','Contract amounts above 20 Million') DEFAULT NULL,
  `proof_of_training_of_casual_laborers` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stakeholder_evaluation_scores`
--

CREATE TABLE `stakeholder_evaluation_scores` (
  `id` int(11) NOT NULL,
  `stakeholder_id` int(11) NOT NULL,
  `stakeholder_evaluation_factor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stakeholder_invoices`
--

CREATE TABLE `stakeholder_invoices` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `stakeholder_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_disposal_asset_items`
--

CREATE TABLE `stock_disposal_asset_items` (
  `id` int(11) NOT NULL,
  `disposal_id` int(11) NOT NULL,
  `asset_sub_location_history_id` int(11) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_sales`
--

CREATE TABLE `stock_sales` (
  `id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL DEFAULT 1,
  `sale_date` date NOT NULL,
  `reference` varchar(200) DEFAULT NULL,
  `stakeholder_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `comments` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_sales_asset_items`
--

CREATE TABLE `stock_sales_asset_items` (
  `id` int(11) NOT NULL,
  `stock_sale_id` int(11) NOT NULL,
  `asset_sub_location_history_id` int(11) NOT NULL,
  `price` double NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_sales_material_items`
--

CREATE TABLE `stock_sales_material_items` (
  `id` int(11) NOT NULL,
  `stock_sale_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `source_sub_location_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_sale_invoices`
--

CREATE TABLE `stock_sale_invoices` (
  `id` int(11) NOT NULL,
  `stock_sale_id` int(11) NOT NULL,
  `outgoing_invoice_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stock_sale_receipts`
--

CREATE TABLE `stock_sale_receipts` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `stock_sale_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `subtask_name` text NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `measurement_unit_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `rate` double NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contracts`
--

CREATE TABLE `sub_contracts` (
  `id` int(11) NOT NULL,
  `stakeholder_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `contract_name` varchar(255) NOT NULL,
  `contract_date` date NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contracts_items`
--

CREATE TABLE `sub_contracts_items` (
  `id` int(11) NOT NULL,
  `sub_contract_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `contract_sum` double NOT NULL,
  `vat_inclusive` tinyint(4) NOT NULL DEFAULT 0,
  `vat_percentage` int(11) NOT NULL DEFAULT 18,
  `description` text NOT NULL,
  `task_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_budgets`
--

CREATE TABLE `sub_contract_budgets` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_certificates`
--

CREATE TABLE `sub_contract_certificates` (
  `id` int(11) NOT NULL,
  `sub_contract_id` int(11) NOT NULL,
  `certificate_date` date DEFAULT NULL,
  `certificate_number` varchar(100) NOT NULL,
  `certified_amount` double NOT NULL,
  `vat_inclusive` tinyint(4) NOT NULL DEFAULT 0,
  `vat_percentage` int(11) NOT NULL DEFAULT 18,
  `remarks` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_certificate_payment_vouchers`
--

CREATE TABLE `sub_contract_certificate_payment_vouchers` (
  `id` int(11) NOT NULL,
  `sub_contract_certificate_id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_certificate_tasks`
--

CREATE TABLE `sub_contract_certificate_tasks` (
  `id` int(11) NOT NULL,
  `sub_contract_certificate_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_payment_requisitions`
--

CREATE TABLE `sub_contract_payment_requisitions` (
  `sub_contract_requisition_id` int(11) NOT NULL,
  `approval_module_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `required_date` date DEFAULT NULL,
  `finalized_date` date DEFAULT NULL,
  `requester_id` int(11) NOT NULL,
  `finalizer_id` int(11) DEFAULT NULL,
  `vat_inclusive` tinyint(4) NOT NULL DEFAULT 0,
  `vat_percentage` int(11) NOT NULL DEFAULT 18,
  `confidentiality_chain_position` int(11) DEFAULT 0,
  `requesting_comments` varchar(500) DEFAULT NULL,
  `finalizing_comments` varchar(500) DEFAULT NULL,
  `foward_to` int(11) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL DEFAULT 'PENDING'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_payment_requisition_approvals`
--

CREATE TABLE `sub_contract_payment_requisition_approvals` (
  `id` int(11) NOT NULL,
  `sub_contract_requisition_id` int(11) NOT NULL,
  `approval_chain_level_id` int(11) NOT NULL,
  `returned_chain_level_id` int(11) DEFAULT NULL,
  `approval_date` date NOT NULL,
  `currency_id` int(11) NOT NULL,
  `vat_inclusive` tinyint(4) NOT NULL DEFAULT 0,
  `vat_percentage` int(11) NOT NULL DEFAULT 18,
  `approving_comments` varchar(500) DEFAULT NULL,
  `forward_to` int(11) DEFAULT NULL,
  `is_final` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_payment_requisition_approval_items`
--

CREATE TABLE `sub_contract_payment_requisition_approval_items` (
  `id` int(11) NOT NULL,
  `sub_contract_payment_requisition_approval_id` int(11) NOT NULL,
  `sub_contract_payment_requisition_item_id` int(11) NOT NULL,
  `approved_amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_payment_requisition_approval_journal_vouchers`
--

CREATE TABLE `sub_contract_payment_requisition_approval_journal_vouchers` (
  `id` int(11) NOT NULL,
  `sub_contract_payment_requisition_approval_id` int(11) NOT NULL,
  `journal_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_payment_requisition_approval_payment_vouchers`
--

CREATE TABLE `sub_contract_payment_requisition_approval_payment_vouchers` (
  `id` int(11) NOT NULL,
  `sub_contract_payment_requisition_approval_id` int(11) NOT NULL,
  `payment_voucher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_payment_requisition_attachments`
--

CREATE TABLE `sub_contract_payment_requisition_attachments` (
  `id` int(11) NOT NULL,
  `sub_contract_payment_requisition_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_contract_payment_requisition_items`
--

CREATE TABLE `sub_contract_payment_requisition_items` (
  `id` int(11) NOT NULL,
  `sub_contract_requisition_id` int(11) NOT NULL,
  `certificate_id` int(11) NOT NULL,
  `requested_amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_locations`
--

CREATE TABLE `sub_locations` (
  `sub_location_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `sub_location_name` varchar(100) NOT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(30) NOT NULL,
  `datetime_logged` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_agent` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `action` enum('Unknown','Attempt Access','Login','Logout','Employee Registration','Employee Update','Employee Contract Registration','Employee Contract Update','User Creation','User Update','Client Registration','Client Update','Client Delete','Project Registration','Project Update','Project Manager Assignment','Project Manager Assignment Update','Project Manager Assignment Termination','Activity Registration','Activity Update','Activity Delete','Task Registration','Task Update','Task Delete','Location Registration','Location Update','Location Delete','Sub-Location Registration','Sub-Location Update','Sub-Location Delete','Material Item Registration','Material Item Update','Material Item Delete','Tool-Type Registration','Tool-Type Update','Tool-Type Delete','Equipment-Type Registration','Equipment-Type Update','Equipment-Type Delete','Stakeholder Registration','Stakeholder Update','Stakeholder Delete','Equipment Registration','Equipment Update','Equipment Delete','Tool Registration','Tool Update','Tool Delete','Material Budget Item Addition','Material Budget Item Update','Material Budget Item Delete','Miscellaneous Budget Item Addition','Miscellaneous Budget Item Update','Miscellaneous Budget Item Delete','Tools Budget Item Addition','Tools Budget Item Update','Tools Budget Item Delete','Department Registration','Department Update','Department Delete','Job Position Registration','Job Position Update','Job Position Delete','Material Item Category Addition','Material Item Category Update','Material Item Category Delete','Measurement Unit Category Addition','Measurement Unit Category Update','Measurement Unit Category Delete','Material Opening Stock Update','External Material Transfer Submission','External Material Transfer Update','External Material Transfer Receive','External Material Transfer Cancellation','Generate Audit Report','Print Audit Report','Internal Material Transfer Submission','Requisition Initiation','Requisition Submission','Requisition Update','Requisition Delete','Requisition Declination','Requisition Approval','Requisition Attachment Upload','Requisition Attachment Delete','Purchase Order Submission','Purchase Order Update','Purchase Order Delete','Purchase Order Close','Purchase Order Receive','Project Team Member Assignment','Project Team Member Update','Project Team Member Delete','Task Progress Update','Task Progress Delete','Task Material Cost Addition','Task Material Cost Update','Project Material Cost Addition','Project Material Cost Update','Project Material Cost Delete','Project Miscellaneous Cost Addition','Project Miscellaneous Cost Update','Project Miscellaneous Cost Delete','Account Creation','Account Update','Account Delete','Contra Entry','Contra Update','Contra Delete') NOT NULL DEFAULT 'Unknown'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `task_name` varchar(200) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `weight_percentage` float NOT NULL DEFAULT 0,
  `measurement_unit_id` int(11) NOT NULL DEFAULT 16,
  `quantity` double NOT NULL DEFAULT 1,
  `rate` double NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `predecessor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `task_payment_voucher_items`
--

CREATE TABLE `task_payment_voucher_items` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `payment_voucher_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `task_progress_updates`
--

CREATE TABLE `task_progress_updates` (
  `update_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `percentage` float NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tax_tables`
--

CREATE TABLE `tax_tables` (
  `id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tax_table_items`
--

CREATE TABLE `tax_table_items` (
  `id` int(11) NOT NULL,
  `minimum` varchar(50) NOT NULL,
  `maximum` varchar(50) NOT NULL,
  `rate` varchar(50) NOT NULL,
  `additional_amount` int(11) NOT NULL,
  `tax_table_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tenders`
--

CREATE TABLE `tenders` (
  `id` int(11) NOT NULL,
  `project_category_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `tender_name` varchar(255) NOT NULL,
  `date_announced` date DEFAULT NULL,
  `submission_deadline` date DEFAULT NULL,
  `date_procured` date NOT NULL,
  `procurement_cost` double NOT NULL,
  `procurement_currency_id` int(11) NOT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_attachments`
--

CREATE TABLE `tender_attachments` (
  `id` int(11) NOT NULL,
  `tender_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_awards`
--

CREATE TABLE `tender_awards` (
  `id` int(11) NOT NULL,
  `date_submitted` date NOT NULL,
  `tender_id` int(11) NOT NULL,
  `submitted_by` varchar(255) DEFAULT NULL,
  `awarded_contractor_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_components`
--

CREATE TABLE `tender_components` (
  `id` int(11) NOT NULL,
  `tender_id` int(11) NOT NULL,
  `lumpsum_price` double NOT NULL,
  `component_name` varchar(255) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_component_lumpsum_prices`
--

CREATE TABLE `tender_component_lumpsum_prices` (
  `id` int(11) NOT NULL,
  `tender_component_id` int(11) NOT NULL,
  `tender_lumpsum_price_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_component_material_prices`
--

CREATE TABLE `tender_component_material_prices` (
  `id` int(11) NOT NULL,
  `tender_component_id` int(11) NOT NULL,
  `tender_material_price_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_lumpsum_prices`
--

CREATE TABLE `tender_lumpsum_prices` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` double NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_material_prices`
--

CREATE TABLE `tender_material_prices` (
  `id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL,
  `description` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_requirements`
--

CREATE TABLE `tender_requirements` (
  `id` int(11) NOT NULL,
  `tender_requirement_type_id` int(11) NOT NULL,
  `tender_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_requirement_types`
--

CREATE TABLE `tender_requirement_types` (
  `id` int(11) NOT NULL,
  `requirement_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_sub_components`
--

CREATE TABLE `tender_sub_components` (
  `id` int(11) NOT NULL,
  `tender_component_id` int(11) NOT NULL,
  `sub_component_name` varchar(255) NOT NULL,
  `lumpsum_price` double NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tender_sub_component_material_prices`
--

CREATE TABLE `tender_sub_component_material_prices` (
  `id` int(11) NOT NULL,
  `tender_sub_component_id` int(11) NOT NULL,
  `tender_material_price_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `toolbox_talk_registers`
--

CREATE TABLE `toolbox_talk_registers` (
  `id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `toolbox_talk_register_participants`
--

CREATE TABLE `toolbox_talk_register_participants` (
  `id` int(11) NOT NULL,
  `toolbox_talk_register_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `toolbox_talk_register_topics`
--

CREATE TABLE `toolbox_talk_register_topics` (
  `id` int(11) NOT NULL,
  `toolbox_talk_register_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `type` enum('PUBLIC','DIRECT') NOT NULL,
  `status` enum('PUBLIC','OPEN','CLOSED') NOT NULL DEFAULT 'OPEN',
  `attachment_name` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `topic_carbon_copies`
--

CREATE TABLE `topic_carbon_copies` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `topic_conversations`
--

CREATE TABLE `topic_conversations` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `sender` int(11) NOT NULL,
  `recipient` int(11) DEFAULT NULL,
  `type` enum('CAPTION','COMMENT','REPLY') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `topic_conversation_logs`
--

CREATE TABLE `topic_conversation_logs` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `log_type` enum('SENDERS','RECIPIENTS') NOT NULL,
  `log_details` varchar(10000) NOT NULL,
  `datetime_posted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `topic_subjects`
--

CREATE TABLE `topic_subjects` (
  `id` int(11) NOT NULL,
  `subject_type` enum('ACTIVITY','TASK','POST') NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `topic_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `transferred_transfer_orders`
--

CREATE TABLE `transferred_transfer_orders` (
  `id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `requisition_approval_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_requisitions`
--

CREATE TABLE `transfer_requisitions` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `destination_location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_requisition_assets`
--

CREATE TABLE `transfer_requisition_assets` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unprocured_deliveries`
--

CREATE TABLE `unprocured_deliveries` (
  `delivery_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `delivery_date` date NOT NULL,
  `delivery_for` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `comments` varchar(500) DEFAULT NULL,
  `receiver_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unprocured_delivery_asset_items`
--

CREATE TABLE `unprocured_delivery_asset_items` (
  `item_id` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `asset_item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL,
  `remarks` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unprocured_delivery_grns`
--

CREATE TABLE `unprocured_delivery_grns` (
  `id` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `grn_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unprocured_delivery_material_items`
--

CREATE TABLE `unprocured_delivery_material_items` (
  `item_id` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `material_item_id` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `price` double NOT NULL,
  `remarks` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `unprocured_delivery_material_item_grn_items`
--

CREATE TABLE `unprocured_delivery_material_item_grn_items` (
  `id` int(11) NOT NULL,
  `unprocured_delivery_material_item_id` int(11) NOT NULL,
  `grn_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(255) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `confidentiality_level_id` int(11) DEFAULT 1,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



--
-- Default Data
--

INSERT INTO `users` (`user_id`, `username`, `password`, `employee_id`, `confidentiality_level_id`, `active`) VALUES (1, 'admin', '742166524b25cd97e4267a3d2ea1a4e26a327989', '1', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users_permissions`
--

CREATE TABLE `users_permissions` (
  `user_permission_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_permission_privileges`
--

CREATE TABLE `user_permission_privileges` (
  `id` int(11) NOT NULL,
  `user_permission_id` int(11) NOT NULL,
  `permission_privilege_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_employee_basic_detail`
-- (See below for the actual view)
--
CREATE TABLE `view_employee_basic_detail` (
`employee_id` int(11)
,`department_id` int(11)
,`employee_name` varchar(62)
,`title` varchar(100)
,`location` varchar(100)
,`basic_salary` float
,`end_date` date
,`start_date` date
,`close_date` date
);

-- --------------------------------------------------------

--
-- Table structure for table `withholding_taxes`
--

CREATE TABLE `withholding_taxes` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `credit_account_id` int(11) DEFAULT NULL,
  `stakeholder_id` int(11) DEFAULT NULL,
  `debit_account_id` int(11) NOT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `payment_voucher_item_id` int(11) DEFAULT NULL,
  `receipt_item_id` int(11) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `withheld_amount` double NOT NULL,
  `status` enum('PAID','PENDING') NOT NULL DEFAULT 'PENDING',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `withholding_taxes_payments`
--

CREATE TABLE `withholding_taxes_payments` (
  `id` int(11) NOT NULL,
  `withholding_tax_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `paid_amount` double NOT NULL,
  `remarks` varchar(500) DEFAULT NULL,
  `paid_by` int(11) NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure for view `view_employee_basic_detail`
--
DROP TABLE IF EXISTS `view_employee_basic_detail`;

CREATE ALGORITHM=UNDEFINED DEFINER=`epmtz`@`localhost` SQL SECURITY DEFINER VIEW `view_employee_basic_detail`  AS SELECT `employees`.`employee_id` AS `employee_id`, `employees`.`department_id` AS `department_id`, concat(ltrim(rtrim(`employees`.`first_name`)),' ',ltrim(rtrim(`employees`.`middle_name`)),' ',ltrim(rtrim(`employees`.`last_name`))) AS `employee_name`, `job_positions`.`position_name` AS `title`, `branches`.`branch_name` AS `location`, `employee_salaries`.`salary` AS `basic_salary`, `employee_contracts`.`end_date` AS `end_date`, `employee_contracts`.`start_date` AS `start_date`, `employee_contract_closes`.`close_date` AS `close_date` FROM ((((((`employee_contracts` left join `employees` on(`employee_contracts`.`employee_id` = `employees`.`employee_id`)) left join `job_positions` on(`employees`.`position_id` = `job_positions`.`job_position_id`)) left join `employee_designations` on(`employee_contracts`.`id` = `employee_designations`.`employee_contract_id`)) left join `branches` on(`employee_designations`.`branch_id` = `branches`.`id`)) left join `employee_salaries` on(`employee_contracts`.`id` = `employee_salaries`.`employee_contract_id`)) left join `employee_contract_closes` on(`employee_contracts`.`id` = `employee_contract_closes`.`employee_contract_id`)) ORDER BY `employee_salaries`.`salary` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `account_group_id` (`account_group_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `account_groups`
--
ALTER TABLE `account_groups`
  ADD PRIMARY KEY (`account_group_id`),
  ADD KEY `group_nature_id` (`group_nature_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `allowances`
--
ALTER TABLE `allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`created_by`);

--
-- Indexes for table `approval_chain_levels`
--
ALTER TABLE `approval_chain_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_module_id` (`approval_module_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `approval_modules`
--
ALTER TABLE `approval_modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approved_invoice_payment_cancellations`
--
ALTER TABLE `approved_invoice_payment_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_payment_request_approval_id` (`purchase_order_payment_request_approval_id`),
  ADD KEY `cancelled_by` (`cancelled_by`);

--
-- Indexes for table `approved_requisition_payment_cancellations`
--
ALTER TABLE `approved_requisition_payment_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`),
  ADD KEY `cancelled_by` (`cancelled_by`);

--
-- Indexes for table `approved_sub_contract_payment_cancellations`
--
ALTER TABLE `approved_sub_contract_payment_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cancelled_by` (`cancelled_by`),
  ADD KEY `sub_contracts_payment_requisition_approval_id` (`sub_contract_payment_requisition_approval_id`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_by` (`created_by`),
  ADD KEY `asset_item_id` (`asset_item_id`);

--
-- Indexes for table `asset_cost_center_assignments`
--
ALTER TABLE `asset_cost_center_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `destination_project_id` (`destination_project_id`),
  ADD KEY `source_project_id` (`source_project_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `asset_cost_center_assignment_items`
--
ALTER TABLE `asset_cost_center_assignment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_cost_center_assignment_id` (`asset_cost_center_assignment_id`),
  ADD KEY `asset_sub_location_history_id` (`asset_sub_location_history_id`);

--
-- Indexes for table `asset_depreciation_rates`
--
ALTER TABLE `asset_depreciation_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_by` (`created_by`),
  ADD KEY `c_by_2` (`created_by`);

--
-- Indexes for table `asset_depreciation_rate_items`
--
ALTER TABLE `asset_depreciation_rate_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_depreciation_rate_id` (`asset_depreciation_rate_id`,`asset_group_id`),
  ADD KEY `asset_group_id` (`asset_group_id`),
  ADD KEY `asset_depreciation_rate_id_2` (`asset_depreciation_rate_id`);

--
-- Indexes for table `asset_groups`
--
ALTER TABLE `asset_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `group_nature_id` (`project_nature_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `asset_handovers`
--
ALTER TABLE `asset_handovers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `employee_id` (`handler_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `asset_handover_items`
--
ALTER TABLE `asset_handover_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_handover_id` (`asset_handover_id`),
  ADD KEY `asset_sub_location_history_id` (`asset_sub_location_history_id`);

--
-- Indexes for table `asset_items`
--
ALTER TABLE `asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_group_id` (`asset_group_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `asset_sub_location_histories`
--
ALTER TABLE `asset_sub_location_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `sub_location_id` (`sub_location_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_id_fk` (`bank_id`),
  ADD KEY `account_id_fk` (`account_id`),
  ADD KEY `creator_fk` (`created_by`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cancelled_purchase_orders`
--
ALTER TABLE `cancelled_purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `casual_labour_budgets`
--
ALTER TABLE `casual_labour_budgets`
  ADD PRIMARY KEY (`budget_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `casual_employee_type_id` (`casual_labour_type_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `casual_labour_types`
--
ALTER TABLE `casual_labour_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `category_parameters`
--
ALTER TABLE `category_parameters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `closed_purchase_orders`
--
ALTER TABLE `closed_purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `company_details`
--
ALTER TABLE `company_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `company_documents`
--
ALTER TABLE `company_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachment_id` (`attachment_id`);

--
-- Indexes for table `contractors`
--
ALTER TABLE `contractors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contractor_accounts`
--
ALTER TABLE `contractor_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `contractor_id` (`contractor_id`);

--
-- Indexes for table `contras`
--
ALTER TABLE `contras`
  ADD PRIMARY KEY (`contra_id`),
  ADD KEY `credit_account_id` (`credit_account_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `stakeholder_id` (`stakeholder_id`);

--
-- Indexes for table `contra_items`
--
ALTER TABLE `contra_items`
  ADD PRIMARY KEY (`contra_item_id`),
  ADD KEY `contra_id` (`contra_id`),
  ADD KEY `debit_account_id` (`debit_account_id`),
  ADD KEY `stakeholder_id` (`stakeholder_id`);

--
-- Indexes for table `cost_centers`
--
ALTER TABLE `cost_centers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cost_center_accounts`
--
ALTER TABLE `cost_center_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cost_center_id` (`cost_center_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `cost_center_imprest_voucher_items`
--
ALTER TABLE `cost_center_imprest_voucher_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_service_fk` (`imprest_voucher_service_item_id`),
  ADD KEY `imprest_cash_fk` (`imprest_voucher_cash_item_id`),
  ADD KEY `cost_center_fk` (`cost_center_id`);

--
-- Indexes for table `cost_center_payment_voucher_items`
--
ALTER TABLE `cost_center_payment_voucher_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_item_id` (`payment_voucher_item_id`),
  ADD KEY `cost_center_id` (`cost_center_id`);

--
-- Indexes for table `cost_center_purchase_orders`
--
ALTER TABLE `cost_center_purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cost_center_id` (`cost_center_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `cost_center_requisitions`
--
ALTER TABLE `cost_center_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cost_center_id` (`cost_center_id`),
  ADD KEY `requisition_id` (`requisition_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`currency_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `department_payment_voucher_items`
--
ALTER TABLE `department_payment_voucher_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_item_id` (`payment_voucher_item_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `deployments`
--
ALTER TABLE `deployments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `deployment_attachments`
--
ALTER TABLE `deployment_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deployment_id` (`deployment_id`),
  ADD KEY `deployment_attachments_ibfk_1` (`attachment_id`);

--
-- Indexes for table `deployment_category_parameters`
--
ALTER TABLE `deployment_category_parameters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deployment_id` (`deployment_id`),
  ADD KEY `category_parameter_id` (`category_parameter_id`);

--
-- Indexes for table `deployment_persons`
--
ALTER TABLE `deployment_persons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deployment_id` (`deployment_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `employees_avatars`
--
ALTER TABLE `employees_avatars`
  ADD PRIMARY KEY (`avatar_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employees_contracts`
--
ALTER TABLE `employees_contracts`
  ADD PRIMARY KEY (`contract_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `registered_by` (`registrar_id`);

--
-- Indexes for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id_fk` (`employee_id`),
  ADD KEY `acc_id_fk` (`account_id`),
  ADD KEY `emplo_creator_fk` (`created_by`);

--
-- Indexes for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id_fk` (`employee_id`),
  ADD KEY `allowance_id_fk` (`allowance_id`),
  ADD KEY `creator_id_fk` (`created_by`);

--
-- Indexes for table `employee_approval_chain_levels`
--
ALTER TABLE `employee_approval_chain_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_chain_level_id` (`approval_chain_level_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `employee_banks`
--
ALTER TABLE `employee_banks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `c_by` (`created_by`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `employee_confidentiality_levels`
--
ALTER TABLE `employee_confidentiality_levels`
  ADD PRIMARY KEY (`level_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `employee_contract_closes`
--
ALTER TABLE `employee_contract_closes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_contract_id` (`employee_contract_id`);

--
-- Indexes for table `employee_designations`
--
ALTER TABLE `employee_designations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_contract_id` (`employee_contract_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `job_position_id` (`job_position_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `employee_loans`
--
ALTER TABLE `employee_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loan_id_fk` (`loan_id`),
  ADD KEY `employee_table_fk` (`employee_id`),
  ADD KEY `creator_table_fk` (`created_by`),
  ADD KEY `emp_loan_account_fk` (`loan_account_id`);

--
-- Indexes for table `employee_loan_repay`
--
ALTER TABLE `employee_loan_repay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_loan_id_fk` (`employee_loan_id`),
  ADD KEY `creator_emp_fk` (`created_by`),
  ADD KEY `loan_fk` (`loan_id`),
  ADD KEY `employee_fk_id` (`employee_id`);

--
-- Indexes for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_contract_id` (`employee_contract_id`);

--
-- Indexes for table `employee_ssfs`
--
ALTER TABLE `employee_ssfs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `ssf_id_fk` (`ssf_id`);

--
-- Indexes for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `cost_center_id` (`cost_center_id`),
  ADD KEY `enquiry_to` (`enquiry_to`);

--
-- Indexes for table `enquiry_asset_items`
--
ALTER TABLE `enquiry_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enquiry_id` (`enquiry_id`),
  ADD KEY `asset_item_id` (`asset_item_id`);

--
-- Indexes for table `enquiry_material_items`
--
ALTER TABLE `enquiry_material_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enquiry_id` (`enquiry_id`),
  ADD KEY `material_item_id` (`material_item_id`);

--
-- Indexes for table `enquiry_service_items`
--
ALTER TABLE `enquiry_service_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enquiry_id` (`enquiry_id`),
  ADD KEY `mesurement_unit_id` (`measurement_unit_id`);

--
-- Indexes for table `epm_v1_sessions`
--
ALTER TABLE `epm_v1_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `last_activity_idx` (`last_activity`);

--
-- Indexes for table `equipment_budgets`
--
ALTER TABLE `equipment_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `asset_item_id` (`asset_item_id`);

--
-- Indexes for table `equipment_hiring_orders`
--
ALTER TABLE `equipment_hiring_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `equipment_hiring_order_items`
--
ALTER TABLE `equipment_hiring_order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exchange_rate_updates`
--
ALTER TABLE `exchange_rate_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `updated_by` (`updater_id`);

--
-- Indexes for table `external_material_transfers`
--
ALTER TABLE `external_material_transfers`
  ADD PRIMARY KEY (`transfer_id`),
  ADD KEY `source_id` (`source_location_id`),
  ADD KEY `destination_id` (`destination_location_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `external_material_transfer_grns`
--
ALTER TABLE `external_material_transfer_grns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `transfer_id` (`transfer_id`);

--
-- Indexes for table `external_material_transfer_items`
--
ALTER TABLE `external_material_transfer_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `transfer_id` (`transfer_id`),
  ADD KEY `material_id` (`material_item_id`),
  ADD KEY `source_sub_location_id` (`source_sub_location_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `external_transfer_asset_items`
--
ALTER TABLE `external_transfer_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `source_sub_location_id` (`source_sub_location_history_id`),
  ADD KEY `transfer_id` (`transfer_id`);

--
-- Indexes for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD PRIMARY KEY (`grn_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `goods_received_note_asset_item_rejects`
--
ALTER TABLE `goods_received_note_asset_item_rejects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `purchase_order_asset_item_id` (`purchase_order_asset_item_id`),
  ADD KEY `delivery_asset_item_id` (`delivery_asset_item_id`);

--
-- Indexes for table `goods_received_note_material_stock_items`
--
ALTER TABLE `goods_received_note_material_stock_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `stock_id` (`stock_id`);

--
-- Indexes for table `grn_asset_sub_location_histories`
--
ALTER TABLE `grn_asset_sub_location_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `asset_sub_location_history_id` (`asset_sub_location_history_id`);

--
-- Indexes for table `grn_invoices`
--
ALTER TABLE `grn_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `grn_received_services`
--
ALTER TABLE `grn_received_services`
  ADD PRIMARY KEY (`service_reception_id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `purchase_order_service_item_id` (`purchase_order_service_item_id`),
  ADD KEY `sub_location_Id` (`sub_location_Id`);

--
-- Indexes for table `hifs`
--
ALTER TABLE `hifs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hired_assets`
--
ALTER TABLE `hired_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hired_asset_project_id_fk` (`project_id`),
  ADD KEY `hired_asset_vendor_id_fk` (`vendor_id`),
  ADD KEY `hired_asset_sub_location_id_fk` (`sub_location_id`),
  ADD KEY `hired_asset_asset_id_fk` (`asset_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `hired_equipments`
--
ALTER TABLE `hired_equipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_receipt_id` (`equipment_receipt_id`),
  ADD KEY `asset_group_id` (`asset_group_id`);

--
-- Indexes for table `hired_equipment_costs`
--
ALTER TABLE `hired_equipment_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `asset_id` (`hired_equipment_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `hired_equipment_receipts`
--
ALTER TABLE `hired_equipment_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `hse_certificates`
--
ALTER TABLE `hse_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `imprests`
--
ALTER TABLE `imprests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprests_ibfk_5` (`created_by`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`);

--
-- Indexes for table `imprest_cash_items`
--
ALTER TABLE `imprest_cash_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_id` (`imprest_id`);

--
-- Indexes for table `imprest_grns`
--
ALTER TABLE `imprest_grns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `imprest_id` (`imprest_id`);

--
-- Indexes for table `imprest_material_items`
--
ALTER TABLE `imprest_material_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_item_id` (`goods_received_note_material_stock_item_id`),
  ADD KEY `imprest_material_items_ibfk_1` (`imprest_id`);

--
-- Indexes for table `imprest_vouchers`
--
ALTER TABLE `imprest_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `credit_account_id` (`credit_account_id`),
  ADD KEY `debit_account_id` (`debit_account_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `handler_id` (`handler_id`);

--
-- Indexes for table `imprest_voucher_asset_items`
--
ALTER TABLE `imprest_voucher_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_item_id` (`requisition_approval_asset_item_id`),
  ADD KEY `imprest_material_items_ibfk_1` (`imprest_voucher_id`);

--
-- Indexes for table `imprest_voucher_cash_items`
--
ALTER TABLE `imprest_voucher_cash_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_id` (`imprest_voucher_id`),
  ADD KEY `requisition_approval_cash_item_id` (`requisition_approval_cash_item_id`);

--
-- Indexes for table `imprest_voucher_contras`
--
ALTER TABLE `imprest_voucher_contras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_voucher_id` (`imprest_voucher_id`),
  ADD KEY `contra_id` (`contra_id`);

--
-- Indexes for table `imprest_voucher_material_items`
--
ALTER TABLE `imprest_voucher_material_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_item_id` (`requisition_approval_material_item_id`),
  ADD KEY `imprest_material_items_ibfk_1` (`imprest_voucher_id`);

--
-- Indexes for table `imprest_voucher_retired_cash`
--
ALTER TABLE `imprest_voucher_retired_cash`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_voucher_id` (`imprest_voucher_id`),
  ADD KEY `imprest_voucher_retired_cash_ibfk_1` (`imprest_voucher_retirement_id`),
  ADD KEY `imprest_voucher_retired_cash_ibfk_2` (`imprest_voucher_cash_item_id`);

--
-- Indexes for table `imprest_voucher_retired_services`
--
ALTER TABLE `imprest_voucher_retired_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_voucher_id` (`imprest_voucher_id`),
  ADD KEY `imprest_voucher_retired_services_ibfk_2` (`imprest_voucher_service_item_id`),
  ADD KEY `imprest_voucher_retired_services_ibfk_1` (`imprest_voucher_retirement_id`);

--
-- Indexes for table `imprest_voucher_retirements`
--
ALTER TABLE `imprest_voucher_retirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_voucher_id` (`imprest_voucher_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `examined_by` (`examined_by`),
  ADD KEY `sub_location_id` (`sub_location_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `retirement_to` (`retirement_to`);

--
-- Indexes for table `imprest_voucher_retirement_asset_items`
--
ALTER TABLE `imprest_voucher_retirement_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_voucher_retirement_id` (`imprest_voucher_retirement_id`),
  ADD KEY `asset_item_id` (`asset_item_id`);

--
-- Indexes for table `imprest_voucher_retirement_grns`
--
ALTER TABLE `imprest_voucher_retirement_grns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `imprest_voucher_retirement_grns_ibfk_2` (`imprest_voucher_retirement_id`);

--
-- Indexes for table `imprest_voucher_retirement_material_items`
--
ALTER TABLE `imprest_voucher_retirement_material_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imprest_voucher_retirement_id` (`imprest_voucher_retirement_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `imprest_voucher_service_items`
--
ALTER TABLE `imprest_voucher_service_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_item_id` (`requisition_approval_service_item_id`),
  ADD KEY `imprest_material_items_ibfk_1` (`imprest_voucher_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `incident_job_cards`
--
ALTER TABLE `incident_job_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_card_id` (`job_card_id`),
  ADD KEY `incident_id` (`incident_id`);

--
-- Indexes for table `inspections`
--
ALTER TABLE `inspections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `inspector_id` (`inspector_id`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `inspection_categories`
--
ALTER TABLE `inspection_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `inspection_id` (`inspection_id`);

--
-- Indexes for table `inspection_category_parameters`
--
ALTER TABLE `inspection_category_parameters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspection_category_id` (`inspection_category_id`),
  ADD KEY `category_parameter_id` (`category_parameter_id`);

--
-- Indexes for table `inspection_category_parameter_types`
--
ALTER TABLE `inspection_category_parameter_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspection_category_parameter_types_ibfk_1` (`inspection_category_parameter_id`),
  ADD KEY `parameter_type_id` (`parameter_type_id`);

--
-- Indexes for table `inspection_job_cards`
--
ALTER TABLE `inspection_job_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspection_id` (`inspection_id`),
  ADD KEY `job_card_id` (`job_card_id`);

--
-- Indexes for table `internal_material_transfers`
--
ALTER TABLE `internal_material_transfers`
  ADD PRIMARY KEY (`transfer_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `internal_material_transfer_items`
--
ALTER TABLE `internal_material_transfer_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `transfer_id` (`transfer_id`),
  ADD KEY `source_sub_location_id` (`source_sub_location_id`),
  ADD KEY `stock_id` (`stock_id`);

--
-- Indexes for table `internal_transfer_asset_items`
--
ALTER TABLE `internal_transfer_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `source_sub_location_id` (`source_sub_location_id`),
  ADD KEY `asset_sub_location_history_id` (`asset_sub_location_history_id`),
  ADD KEY `transfer_id` (`transfer_id`);

--
-- Indexes for table `inventory_locations`
--
ALTER TABLE `inventory_locations`
  ADD PRIMARY KEY (`location_id`),
  ADD UNIQUE KEY `project_id_2` (`project_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `invoice_journal_voucher_items`
--
ALTER TABLE `invoice_journal_voucher_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_voucher_item_id` (`journal_voucher_item_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `invoice_payment_vouchers`
--
ALTER TABLE `invoice_payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `job_card_labours`
--
ALTER TABLE `job_card_labours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `job_card_id` (`job_card_id`);

--
-- Indexes for table `job_card_services`
--
ALTER TABLE `job_card_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `job_card_id` (`job_card_labour_id`);

--
-- Indexes for table `job_positions`
--
ALTER TABLE `job_positions`
  ADD PRIMARY KEY (`job_position_id`);

--
-- Indexes for table `journal_contras`
--
ALTER TABLE `journal_contras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contra_id` (`contra_id`),
  ADD KEY `journal_id` (`journal_id`);

--
-- Indexes for table `journal_payment_vouchers`
--
ALTER TABLE `journal_payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_id` (`journal_id`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`);

--
-- Indexes for table `journal_receipts`
--
ALTER TABLE `journal_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `journal_voucher_id` (`journal_id`);

--
-- Indexes for table `journal_vouchers`
--
ALTER TABLE `journal_vouchers`
  ADD PRIMARY KEY (`journal_id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `journal_voucher_attachments`
--
ALTER TABLE `journal_voucher_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_voucher_id` (`journal_voucher_id`),
  ADD KEY `attachment_id` (`attachment_id`);

--
-- Indexes for table `journal_voucher_credit_accounts`
--
ALTER TABLE `journal_voucher_credit_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_voucher_id` (`journal_voucher_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `stakeholder_id` (`stakeholder_id`);

--
-- Indexes for table `journal_voucher_items`
--
ALTER TABLE `journal_voucher_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `debit_account_id` (`debit_account_id`),
  ADD KEY `journal_voucher_id` (`journal_voucher_id`),
  ADD KEY `stakeholder_id` (`stakeholder_id`);

--
-- Indexes for table `journal_voucher_item_approved_cash_request_items`
--
ALTER TABLE `journal_voucher_item_approved_cash_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_voucher_item_id` (`journal_voucher_item_id`),
  ADD KEY `requisition_approval_asset_item_id` (`requisition_approval_asset_item_id`),
  ADD KEY `requisition_approval_cash_item_id` (`requisition_approval_cash_item_id`),
  ADD KEY `requisition_approval_material_item_id` (`requisition_approval_material_item_id`),
  ADD KEY `requisition_approval_service_item_id` (`requisition_approval_service_item_id`);

--
-- Indexes for table `journal_voucher_item_approved_invoice_items`
--
ALTER TABLE `journal_voucher_item_approved_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_voucher_item_id` (`journal_voucher_item_id`),
  ADD KEY `purchase_order_payment_request_approval_invoice_item_id` (`purchase_order_payment_request_approval_invoice_item_id`);

--
-- Indexes for table `journal_voucher_item_approved_sub_contract_requisition_items`
--
ALTER TABLE `journal_voucher_item_approved_sub_contract_requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_voucher_item_id` (`journal_voucher_item_id`),
  ADD KEY `sub_contract_payment_requisition_approval_item_id` (`sub_contract_payment_requisition_approval_item_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_fk` (`created_by`);

--
-- Indexes for table `maintenance_invoices`
--
ALTER TABLE `maintenance_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `out_fk` (`outgoing_invoice_id`),
  ADD KEY `services_fk` (`service_id`);

--
-- Indexes for table `maintenance_services`
--
ALTER TABLE `maintenance_services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `client_fk` (`client_id`),
  ADD KEY `created_by_employee_fk` (`created_by`),
  ADD KEY `currency_fk_id` (`currency_id`);

--
-- Indexes for table `maintenance_service_items`
--
ALTER TABLE `maintenance_service_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `service_id_fk` (`service_id`),
  ADD KEY `unity_id_fk` (`measurement_unit_id`);

--
-- Indexes for table `maintenance_service_receipts`
--
ALTER TABLE `maintenance_service_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `maintenance_service_id` (`maintenance_service_id`);

--
-- Indexes for table `material_average_prices`
--
ALTER TABLE `material_average_prices`
  ADD PRIMARY KEY (`average_price_id`),
  ADD KEY `sub_location_id` (`sub_location_id`),
  ADD KEY `material_item_id` (`material_item_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `material_stock_id` (`material_stock_id`);

--
-- Indexes for table `material_budgets`
--
ALTER TABLE `material_budgets`
  ADD PRIMARY KEY (`budget_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `material_id` (`material_item_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `material_costs`
--
ALTER TABLE `material_costs`
  ADD PRIMARY KEY (`material_cost_id`),
  ADD KEY `task_id` (`project_id`),
  ADD KEY `material_id` (`material_item_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `source_sub_location_id` (`source_sub_location_id`),
  ADD KEY `task_id_2` (`task_id`);

--
-- Indexes for table `material_cost_center_assignments`
--
ALTER TABLE `material_cost_center_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `source_project_id` (`source_project_id`),
  ADD KEY `destination_project_id` (`destination_project_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `material_cost_center_assignment_items`
--
ALTER TABLE `material_cost_center_assignment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_cost_center_assignment_id` (`material_cost_center_assignment_id`),
  ADD KEY `stock_id` (`stock_id`);

--
-- Indexes for table `material_disposals`
--
ALTER TABLE `material_disposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `material_disposals_ibfk_2` (`location_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `material_disposal_items`
--
ALTER TABLE `material_disposal_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disposal_id` (`disposal_id`),
  ADD KEY `material_item_id` (`material_item_id`),
  ADD KEY `sub_location_id` (`sub_location_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `material_items`
--
ALTER TABLE `material_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `material_item_categories`
--
ALTER TABLE `material_item_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_category_id` (`parent_category_id`),
  ADD KEY `project_nature_id` (`project_nature_id`);

--
-- Indexes for table `material_opening_stocks`
--
ALTER TABLE `material_opening_stocks`
  ADD PRIMARY KEY (`opening_stock_id`),
  ADD KEY `sub_store_id` (`sub_location_id`),
  ADD KEY `material_item_id` (`item_id`),
  ADD KEY `stock_id` (`stock_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `material_stocks`
--
ALTER TABLE `material_stocks`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `sub_store_id` (`sub_location_id`),
  ADD KEY `material_item_id` (`item_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `measurement_units`
--
ALTER TABLE `measurement_units`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `miscellaneous_budgets`
--
ALTER TABLE `miscellaneous_budgets`
  ADD PRIMARY KEY (`budget_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `expense_account_id` (`expense_account_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `official_hifs`
--
ALTER TABLE `official_hifs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `official_ssfs`
--
ALTER TABLE `official_ssfs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ordered_pre_orders`
--
ALTER TABLE `ordered_pre_orders`
  ADD PRIMARY KEY (`ordered_pre_order_id`),
  ADD UNIQUE KEY `purchase_order_id_2` (`purchase_order_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `outgoing_invoices`
--
ALTER TABLE `outgoing_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_to_fk` (`invoice_to`),
  ADD KEY `employee_created_by_fk` (`created_by`),
  ADD KEY `curency_as_fk` (`currency_id`);

--
-- Indexes for table `outgoing_invoice_items`
--
ALTER TABLE `outgoing_invoice_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `outgoing_invoice_fk` (`outgoing_invoice_id`),
  ADD KEY `measurement_fk` (`measurement_unit_id`),
  ADD KEY `maintenance_service_item_id` (`maintenance_service_item_id`),
  ADD KEY `project_certificate_id` (`project_certificate_id`),
  ADD KEY `stock_sale_asset_item_id` (`stock_sale_asset_item_id`),
  ADD KEY `stock_sale_material_item_id` (`stock_sale_material_item_id`);

--
-- Indexes for table `owned_equipment_costs`
--
ALTER TABLE `owned_equipment_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `asset_id` (`asset_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `parameter_types`
--
ALTER TABLE `parameter_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_parameter_id` (`category_parameter_id`);

--
-- Indexes for table `payment_request_approval_journal_vouchers`
--
ALTER TABLE `payment_request_approval_journal_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `junction_payment_voucher_fk` (`purchase_order_payment_request_approval_id`),
  ADD KEY `junction_journal_voucher_fk` (`journal_voucher_id`);

--
-- Indexes for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  ADD PRIMARY KEY (`payment_voucher_id`),
  ADD KEY `credit_account_id` (`credit_account_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `is_printed` (`is_printed`);

--
-- Indexes for table `payment_voucher_credit_accounts`
--
ALTER TABLE `payment_voucher_credit_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`),
  ADD KEY `stakeholder_id` (`stakeholder_id`);

--
-- Indexes for table `payment_voucher_grns`
--
ALTER TABLE `payment_voucher_grns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_id` (`grn_id`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`);

--
-- Indexes for table `payment_voucher_items`
--
ALTER TABLE `payment_voucher_items`
  ADD PRIMARY KEY (`payment_voucher_item_id`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`),
  ADD KEY `debit_account_id` (`debit_account_id`),
  ADD KEY `stakeholder_id` (`stakeholder_id`);

--
-- Indexes for table `payment_voucher_item_approved_cash_request_items`
--
ALTER TABLE `payment_voucher_item_approved_cash_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_item_id` (`payment_voucher_item_id`),
  ADD KEY `requisition_approval_asset_item_id` (`requisition_approval_asset_item_id`),
  ADD KEY `requisition_approval_cash_item_id` (`requisition_approval_cash_item_id`),
  ADD KEY `requisition_approval_material_item_id` (`requisition_approval_material_item_id`),
  ADD KEY `requisition_approval_service_item_id` (`requisition_approval_service_item_id`);

--
-- Indexes for table `payment_voucher_item_approved_invoice_items`
--
ALTER TABLE `payment_voucher_item_approved_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_payment_request_approval_invoice_item_id` (`purchase_order_payment_request_approval_invoice_item_id`),
  ADD KEY `payment_voucher_item_approved_invoice_items_ibfk_2` (`payment_voucher_item_id`);

--
-- Indexes for table `payment_voucher_item_approved_sub_contract_requisition_items`
--
ALTER TABLE `payment_voucher_item_approved_sub_contract_requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_item_id` (`payment_voucher_item_id`),
  ADD KEY `sub_contract_payment_requisition_approval_item_id` (`sub_contract_payment_requisition_approval_item_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foward_to_fk` (`foward_to`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `payroll_creator_fk` (`created_by`),
  ADD KEY `department_id_fk` (`department_id`),
  ADD KEY `approval_module_fk` (`approval_module_id`);

--
-- Indexes for table `payroll_approvals`
--
ALTER TABLE `payroll_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_payroll_fk` (`payroll_id`),
  ADD KEY `approval_chain_fk` (`approval_chain_level_id`),
  ADD KEY `approval_creator_fk` (`created_by`);

--
-- Indexes for table `payroll_employee_allowances`
--
ALTER TABLE `payroll_employee_allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id_foreign` (`employee_id`),
  ADD KEY `payroll_foreign` (`payroll_id`);

--
-- Indexes for table `payroll_employee_basic_info`
--
ALTER TABLE `payroll_employee_basic_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `basic_info_payroll_id_fk` (`payroll_id`),
  ADD KEY `payroll_employee_id_fk` (`employee_id`);

--
-- Indexes for table `payroll_employer_deductions`
--
ALTER TABLE `payroll_employer_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_deduct_fk` (`payroll_id`),
  ADD KEY `employee_ded_fk` (`employee_id`);

--
-- Indexes for table `payroll_journal_vouchers`
--
ALTER TABLE `payroll_journal_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_id` (`payroll_id`),
  ADD KEY `journal_voucher_id` (`journal_voucher_id`);

--
-- Indexes for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_fk_id` (`payroll_id`);

--
-- Indexes for table `payroll_payment_vouchers`
--
ALTER TABLE `payroll_payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_payroll_fk` (`payroll_id`),
  ADD KEY `payment_payment_voucher_fk` (`payment_voucher_id`),
  ADD KEY `aliyetengeneza_id_fk` (`created_by`);

--
-- Indexes for table `permanent_labour_budgets`
--
ALTER TABLE `permanent_labour_budgets`
  ADD PRIMARY KEY (`budget_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `permanent_employee_type_id` (`job_position_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `permanent_labour_costs`
--
ALTER TABLE `permanent_labour_costs`
  ADD PRIMARY KEY (`permanent_labour_cost_id`),
  ADD KEY `project_team_member_id` (`project_team_member_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`);

--
-- Indexes for table `permission_privileges`
--
ALTER TABLE `permission_privileges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_fk` (`parent_id`);

--
-- Indexes for table `procurement_attachments`
--
ALTER TABLE `procurement_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachment_id` (`attachment_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD UNIQUE KEY `uniquie_project_name` (`project_name`(100)),
  ADD KEY `project_category_id` (`category_id`),
  ADD KEY `client_id` (`stakeholder_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `project_accounts`
--
ALTER TABLE `project_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_attachments`
--
ALTER TABLE `project_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `attachment_id` (`attachment_id`);

--
-- Indexes for table `project_categories`
--
ALTER TABLE `project_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `project_certificates`
--
ALTER TABLE `project_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_certificate_invoices`
--
ALTER TABLE `project_certificate_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_certificate_id` (`project_certificate_id`),
  ADD KEY `project_certificate_invoices_ibfk_1` (`outgoing_invoice_id`);

--
-- Indexes for table `project_certificate_receipts`
--
ALTER TABLE `project_certificate_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificate_id` (`certificate_id`),
  ADD KEY `receipt_id` (`receipt_id`);

--
-- Indexes for table `project_closures`
--
ALTER TABLE `project_closures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `closed_by` (`created_by`);

--
-- Indexes for table `project_contract_reviews`
--
ALTER TABLE `project_contract_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `project_imprest_voucher_items`
--
ALTER TABLE `project_imprest_voucher_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id_fk` (`project_id`),
  ADD KEY `impest_cash_fk` (`imprest_voucher_cash_item_id`),
  ADD KEY `p_imprest_service_fk` (`imprest_voucher_service_item_id`);

--
-- Indexes for table `project_payment_voucher_items`
--
ALTER TABLE `project_payment_voucher_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_item_id` (`payment_voucher_item_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_plans`
--
ALTER TABLE `project_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `project_plan_tasks`
--
ALTER TABLE `project_plan_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `project_plan_tasks_ibfk_1` (`project_plan_id`);

--
-- Indexes for table `project_plan_task_casual_labour_budgets`
--
ALTER TABLE `project_plan_task_casual_labour_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_plan_task_id`),
  ADD KEY `casual_employee_type_id` (`casual_labour_type_id`),
  ADD KEY `employee_id` (`created_by`);

--
-- Indexes for table `project_plan_task_equipment_budgets`
--
ALTER TABLE `project_plan_task_equipment_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`project_plan_task_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `asset_group_id_fk_5` (`asset_id`);

--
-- Indexes for table `project_plan_task_executions`
--
ALTER TABLE `project_plan_task_executions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_plan_task_executions_ibfk_2` (`project_plan_id`),
  ADD KEY `project_plan_task_executions_ibfk_3` (`task_id`),
  ADD KEY `project_plan_task_executions_ibfk_1` (`created_by`);

--
-- Indexes for table `project_plan_task_execution_casual_labour`
--
ALTER TABLE `project_plan_task_execution_casual_labour`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_plan_task_execution_casual_labour_ibfk_1` (`casual_labour_type_id`),
  ADD KEY `project_plan_task_execution_casual_labour_ibfk_2` (`plan_task_execution_id`);

--
-- Indexes for table `project_plan_task_execution_equipments`
--
ALTER TABLE `project_plan_task_execution_equipments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_plan_task_execution_equipments_ibfk_1` (`plan_task_execution_id`),
  ADD KEY `project_plan_task_execution_equipments_ibfk_2` (`asset_id`);

--
-- Indexes for table `project_plan_task_execution_material_costs`
--
ALTER TABLE `project_plan_task_execution_material_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_plan_task_id` (`plan_task_execution_id`),
  ADD KEY `material_cost_id` (`material_cost_id`);

--
-- Indexes for table `project_plan_task_material_budgets`
--
ALTER TABLE `project_plan_task_material_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_plan_task_id` (`project_plan_task_id`),
  ADD KEY `material_item_id` (`material_item_id`);

--
-- Indexes for table `project_purchase_orders`
--
ALTER TABLE `project_purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `purchase_order` (`purchase_order_id`);

--
-- Indexes for table `project_requisitions`
--
ALTER TABLE `project_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `requisition_id` (`requisition_id`);

--
-- Indexes for table `project_special_budgets`
--
ALTER TABLE `project_special_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `project_team_members`
--
ALTER TABLE `project_team_members`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `assigned_by` (`assignor_id`),
  ADD KEY `job_position_id` (`job_position_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `vendor_id` (`stakeholder_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `handler_id` (`handler_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `is_printed` (`is_printed`);

--
-- Indexes for table `purchase_order_asset_items`
--
ALTER TABLE `purchase_order_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `asset_item_id` (`asset_item_id`);

--
-- Indexes for table `purchase_order_grns`
--
ALTER TABLE `purchase_order_grns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`goods_received_note_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `purchase_order_invoices`
--
ALTER TABLE `purchase_order_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `purchase_order_material_items`
--
ALTER TABLE `purchase_order_material_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `purchase_order_id` (`order_id`),
  ADD KEY `material_item_id` (`material_item_id`);

--
-- Indexes for table `purchase_order_material_item_grn_items`
--
ALTER TABLE `purchase_order_material_item_grn_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `goods_received_note_item_id` (`goods_received_note_item_id`),
  ADD KEY `purchase_order_material_item_id` (`purchase_order_material_item_id`);

--
-- Indexes for table `purchase_order_payment_requests`
--
ALTER TABLE `purchase_order_payment_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `created_by` (`requester_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `approval_module_id` (`approval_module_id`),
  ADD KEY `forward_to` (`forward_to`);

--
-- Indexes for table `purchase_order_payment_request_approvals`
--
ALTER TABLE `purchase_order_payment_request_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `purchase_order_payment_request_id` (`purchase_order_payment_request_id`),
  ADD KEY `approval_chain_level_id` (`approval_chain_level_id`),
  ADD KEY `forward_to` (`forward_to`),
  ADD KEY `is_printed` (`is_printed`);

--
-- Indexes for table `purchase_order_payment_request_approval_cash_items`
--
ALTER TABLE `purchase_order_payment_request_approval_cash_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_payment_request_approval_id` (`purchase_order_payment_request_approval_id`),
  ADD KEY `purchase_order_payment_request_cash_item_id` (`purchase_order_payment_request_cash_item_id`);

--
-- Indexes for table `purchase_order_payment_request_approval_invoice_items`
--
ALTER TABLE `purchase_order_payment_request_approval_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_payment_request_approval_id` (`purchase_order_payment_request_approval_id`),
  ADD KEY `purchase_order_payment_request_invoice_item_id` (`purchase_order_payment_request_invoice_item_id`);

--
-- Indexes for table `purchase_order_payment_request_approval_payment_vouchers`
--
ALTER TABLE `purchase_order_payment_request_approval_payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`),
  ADD KEY `purchase_order_payment_request_approval_id` (`purchase_order_payment_request_approval_id`);

--
-- Indexes for table `purchase_order_payment_request_attachments`
--
ALTER TABLE `purchase_order_payment_request_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_payment_request_attachments_ibfk_1` (`attachment_id`),
  ADD KEY `purchase_order_payment_request_attachments_ibfk_2` (`purchase_order_payment_request_id`);

--
-- Indexes for table `purchase_order_payment_request_cash_items`
--
ALTER TABLE `purchase_order_payment_request_cash_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_payment_request_id` (`purchase_order_payment_request_id`);

--
-- Indexes for table `purchase_order_payment_request_invoice_items`
--
ALTER TABLE `purchase_order_payment_request_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `purchase_order_payment_request_invoice_items_ibfk_2` (`purchase_order_payment_request_id`);

--
-- Indexes for table `purchase_order_service_items`
--
ALTER TABLE `purchase_order_service_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`order_id`),
  ADD KEY `measurement_unit_id` (`measurement_unit_id`);

--
-- Indexes for table `receipts`
--
ALTER TABLE `receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `debit_account_id` (`debit_account_id`),
  ADD KEY `credit_acount_id` (`credit_account_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `receipt_items`
--
ALTER TABLE `receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`);

--
-- Indexes for table `registered_certificates`
--
ALTER TABLE `registered_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hse_certificate_id` (`hse_certificate_id`);

--
-- Indexes for table `rejected_payrolls`
--
ALTER TABLE `rejected_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reject_payroll_id` (`payroll_id`),
  ADD KEY `current_level_id` (`current_level`),
  ADD KEY `rejector_fk` (`created_by`);

--
-- Indexes for table `requisitions`
--
ALTER TABLE `requisitions`
  ADD PRIMARY KEY (`requisition_id`),
  ADD KEY `employee_id` (`requester_id`),
  ADD KEY `approver_id` (`finalizer_id`),
  ADD KEY `approval_module_id` (`approval_module_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `foward_to` (`foward_to`);

--
-- Indexes for table `requisition_approvals`
--
ALTER TABLE `requisition_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_chain_level_id` (`approval_chain_level_id`),
  ADD KEY `employee_id` (`created_by`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `returned_chain_level_id` (`returned_chain_level_id`),
  ADD KEY `is_printed` (`is_printed`),
  ADD KEY `forward_to` (`forward_to`);

--
-- Indexes for table `requisition_approval_asset_items`
--
ALTER TABLE `requisition_approval_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_material_item_id` (`requisition_asset_item_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `requisition_approval_cash_items`
--
ALTER TABLE `requisition_approval_cash_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_material_item_id` (`requisition_cash_item_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`);

--
-- Indexes for table `requisition_approval_cash_item_expense_accounts`
--
ALTER TABLE `requisition_approval_cash_item_expense_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`),
  ADD KEY `requisition_material_item_id` (`requisition_cash_item_id`),
  ADD KEY `account_id` (`expense_account_id`);

--
-- Indexes for table `requisition_approval_imprest_vouchers`
--
ALTER TABLE `requisition_approval_imprest_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `imprest_id` (`imprest_voucher_id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`);

--
-- Indexes for table `requisition_approval_material_items`
--
ALTER TABLE `requisition_approval_material_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_material_item_id` (`requisition_material_item_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`);

--
-- Indexes for table `requisition_approval_material_item_expense_accounts`
--
ALTER TABLE `requisition_approval_material_item_expense_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`),
  ADD KEY `account_id` (`expense_account_id`),
  ADD KEY `requisition_approval_material_item_expense_accounts_ibfk_2` (`requisition_material_item_id`);

--
-- Indexes for table `requisition_approval_payment_vouchers`
--
ALTER TABLE `requisition_approval_payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_voucher_id` (`payment_voucher_id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`);

--
-- Indexes for table `requisition_approval_service_items`
--
ALTER TABLE `requisition_approval_service_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`),
  ADD KEY `requisition_service_item_id` (`requisition_service_item_id`),
  ADD KEY `approved_vendor_id` (`vendor_id`),
  ADD KEY `account_id` (`account_id`);

--
-- Indexes for table `requisition_asset_items`
--
ALTER TABLE `requisition_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_item_it` (`asset_item_id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `requested_currency_id` (`requested_currency_id`),
  ADD KEY `requested_account_id` (`requested_account_id`);

--
-- Indexes for table `requisition_attachments`
--
ALTER TABLE `requisition_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `attachment_id` (`attachment_id`);

--
-- Indexes for table `requisition_cash_items`
--
ALTER TABLE `requisition_cash_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `material_item_id` (`description`),
  ADD KEY `requested_currency_id` (`requested_currency_id`),
  ADD KEY `measurement_unit_id` (`measurement_unit_id`),
  ADD KEY `expense_account_id` (`expense_account_id`),
  ADD KEY `requested_account_id` (`requested_account_id`);

--
-- Indexes for table `requisition_cash_item_tasks`
--
ALTER TABLE `requisition_cash_item_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_item_id` (`requisition_item_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `requisition_equipment_items`
--
ALTER TABLE `requisition_equipment_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `vendor_id` (`requested_vendor_id`),
  ADD KEY `requested_currency_id` (`requested_currency_id`),
  ADD KEY `expense_account_id` (`expense_account_id`),
  ADD KEY `asset_group_id` (`asset_group_id`);

--
-- Indexes for table `requisition_equipment_item_tasks`
--
ALTER TABLE `requisition_equipment_item_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_item_id` (`requisition_item_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `requisition_material_items`
--
ALTER TABLE `requisition_material_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `material_item_id` (`material_item_id`),
  ADD KEY `vendor_id` (`requested_vendor_id`),
  ADD KEY `requested_currency_id` (`requested_currency_id`),
  ADD KEY `expense_account_id` (`expense_account_id`),
  ADD KEY `requisition_material_items_ibfk_8` (`requested_location_id`),
  ADD KEY `requested_account_id` (`requested_account_id`);

--
-- Indexes for table `requisition_material_item_tasks`
--
ALTER TABLE `requisition_material_item_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_item_id` (`requisition_item_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `requisition_purchase_orders`
--
ALTER TABLE `requisition_purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`);

--
-- Indexes for table `requisition_service_items`
--
ALTER TABLE `requisition_service_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `measurement_unit_id` (`measurement_unit_id`),
  ADD KEY `requested_vendor_id` (`requested_vendor_id`),
  ADD KEY `requested_account_id` (`requested_account_id`);

--
-- Indexes for table `revised_tasks`
--
ALTER TABLE `revised_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `revision_id` (`revision_id`);

--
-- Indexes for table `revision`
--
ALTER TABLE `revision`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `site_diary_compliances`
--
ALTER TABLE `site_diary_compliances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `site_diary_compliance_statuses`
--
ALTER TABLE `site_diary_compliance_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_diary_id` (`site_diary_id`);

--
-- Indexes for table `site_topics`
--
ALTER TABLE `site_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ssfs`
--
ALTER TABLE `ssfs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_by` (`created_by`);

--
-- Indexes for table `ssf_groups`
--
ALTER TABLE `ssf_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `c_by` (`created_by`),
  ADD KEY `ssf_id` (`ssf_id`);

--
-- Indexes for table `ssf_group_stations`
--
ALTER TABLE `ssf_group_stations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ssf_group_id` (`ssf_group_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `stakeholders`
--
ALTER TABLE `stakeholders`
  ADD PRIMARY KEY (`stakeholder_id`),
  ADD KEY `account_id` (`account_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `stakeholder_evaluation_factors`
--
ALTER TABLE `stakeholder_evaluation_factors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stakeholder_evaluation_scores`
--
ALTER TABLE `stakeholder_evaluation_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contractor_id` (`stakeholder_id`),
  ADD KEY `supplier_evaluation_factors_id` (`stakeholder_evaluation_factor_id`);

--
-- Indexes for table `stakeholder_invoices`
--
ALTER TABLE `stakeholder_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `vendor_id` (`stakeholder_id`);

--
-- Indexes for table `stock_disposal_asset_items`
--
ALTER TABLE `stock_disposal_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disposal_id` (`disposal_id`),
  ADD KEY `asset_sub_location_history_id` (`asset_sub_location_history_id`);

--
-- Indexes for table `stock_sales`
--
ALTER TABLE `stock_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `client_id` (`stakeholder_id`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `stock_sales_asset_items`
--
ALTER TABLE `stock_sales_asset_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_sub_location_history_id` (`asset_sub_location_history_id`),
  ADD KEY `stock_sale_id` (`stock_sale_id`);

--
-- Indexes for table `stock_sales_material_items`
--
ALTER TABLE `stock_sales_material_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_sale_id` (`stock_sale_id`),
  ADD KEY `material_item_id` (`material_item_id`),
  ADD KEY `source_sub_location_id` (`source_sub_location_id`);

--
-- Indexes for table `stock_sale_invoices`
--
ALTER TABLE `stock_sale_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_sale_id` (`stock_sale_id`),
  ADD KEY `stock_sale_invoices_ibfk_2` (`outgoing_invoice_id`);

--
-- Indexes for table `stock_sale_receipts`
--
ALTER TABLE `stock_sale_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `stock_sale_id` (`stock_sale_id`);

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `measurement_unit_id` (`measurement_unit_id`);

--
-- Indexes for table `sub_contracts`
--
ALTER TABLE `sub_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `sub_contractor_id` (`stakeholder_id`);

--
-- Indexes for table `sub_contracts_items`
--
ALTER TABLE `sub_contracts_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_contract_id` (`sub_contract_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `sub_contract_budgets`
--
ALTER TABLE `sub_contract_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sub_contract_certificates`
--
ALTER TABLE `sub_contract_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_contract_id` (`sub_contract_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `sub_contract_certificate_payment_vouchers`
--
ALTER TABLE `sub_contract_certificate_payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`),
  ADD KEY `sub_contract_certificate_id` (`sub_contract_certificate_id`);

--
-- Indexes for table `sub_contract_certificate_tasks`
--
ALTER TABLE `sub_contract_certificate_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_contract_certificate_id` (`sub_contract_certificate_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `sub_contract_payment_requisitions`
--
ALTER TABLE `sub_contract_payment_requisitions`
  ADD PRIMARY KEY (`sub_contract_requisition_id`),
  ADD KEY `approval_module_id` (`approval_module_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `finalizer_id` (`finalizer_id`),
  ADD KEY `requester_id` (`requester_id`),
  ADD KEY `sub_contracts_payment_requisitions_ibfk_3` (`foward_to`);

--
-- Indexes for table `sub_contract_payment_requisition_approvals`
--
ALTER TABLE `sub_contract_payment_requisition_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_chain_level_id` (`approval_chain_level_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `returned_chain_level_id` (`returned_chain_level_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `sub_contract_requisition_id` (`sub_contract_requisition_id`),
  ADD KEY `forward_to` (`forward_to`);

--
-- Indexes for table `sub_contract_payment_requisition_approval_items`
--
ALTER TABLE `sub_contract_payment_requisition_approval_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_contract_payment_requisition_approval_id` (`sub_contract_payment_requisition_approval_id`),
  ADD KEY `sub_contract_payment_requisition_item_id` (`sub_contract_payment_requisition_item_id`);

--
-- Indexes for table `sub_contract_payment_requisition_approval_journal_vouchers`
--
ALTER TABLE `sub_contract_payment_requisition_approval_journal_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_voucher_id` (`journal_voucher_id`),
  ADD KEY `sub_contract_payment_requisition_approval_id` (`sub_contract_payment_requisition_approval_id`);

--
-- Indexes for table `sub_contract_payment_requisition_approval_payment_vouchers`
--
ALTER TABLE `sub_contract_payment_requisition_approval_payment_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_contracts_payment_requisition_approval_id` (`sub_contract_payment_requisition_approval_id`),
  ADD KEY `payment_voucher_id` (`payment_voucher_id`);

--
-- Indexes for table `sub_contract_payment_requisition_attachments`
--
ALTER TABLE `sub_contract_payment_requisition_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sub_contract_payment_requisition_id` (`sub_contract_payment_requisition_id`),
  ADD KEY `sub_contract_payment_requisition_attachments_ibfk_2` (`attachment_id`);

--
-- Indexes for table `sub_contract_payment_requisition_items`
--
ALTER TABLE `sub_contract_payment_requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `certificate_no` (`certificate_id`),
  ADD KEY `sub_contract_payment_requisition_items_ibfk_3` (`sub_contract_requisition_id`);

--
-- Indexes for table `sub_locations`
--
ALTER TABLE `sub_locations`
  ADD PRIMARY KEY (`sub_location_id`),
  ADD KEY `store_id` (`location_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`employee_id`),
  ADD KEY `branch_id` (`department_id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `measurement_unit_id` (`measurement_unit_id`),
  ADD KEY `predecessor` (`predecessor`);

--
-- Indexes for table `task_payment_voucher_items`
--
ALTER TABLE `task_payment_voucher_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_voucher_item_id` (`payment_voucher_item_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `task_progress_updates`
--
ALTER TABLE `task_progress_updates`
  ADD PRIMARY KEY (`update_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `tax_tables`
--
ALTER TABLE `tax_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_table_items`
--
ALTER TABLE `tax_table_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tax_table_id` (`tax_table_id`);

--
-- Indexes for table `tenders`
--
ALTER TABLE `tenders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `procurement_currency_id` (`procurement_currency_id`),
  ADD KEY `supervisor_id` (`supervisor_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `project_category_id` (`project_category_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `tender_attachments`
--
ALTER TABLE `tender_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attachment_id` (`attachment_id`),
  ADD KEY `tender_id` (`tender_id`);

--
-- Indexes for table `tender_awards`
--
ALTER TABLE `tender_awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `tender_id` (`tender_id`),
  ADD KEY `awarderd_contractor_id` (`awarded_contractor_id`);

--
-- Indexes for table `tender_components`
--
ALTER TABLE `tender_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tender_id` (`tender_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `tender_component_lumpsum_prices`
--
ALTER TABLE `tender_component_lumpsum_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tender_component_id` (`tender_component_id`),
  ADD KEY `tender_component_lumpsum_price_id` (`tender_lumpsum_price_id`);

--
-- Indexes for table `tender_component_material_prices`
--
ALTER TABLE `tender_component_material_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tender_component_id` (`tender_component_id`),
  ADD KEY `tender_material_price_id` (`tender_material_price_id`);

--
-- Indexes for table `tender_lumpsum_prices`
--
ALTER TABLE `tender_lumpsum_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `tender_material_prices`
--
ALTER TABLE `tender_material_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_item_id` (`material_item_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `tender_requirements`
--
ALTER TABLE `tender_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tender_requirement_type_id` (`tender_requirement_type_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `tender_id` (`tender_id`);

--
-- Indexes for table `tender_requirement_types`
--
ALTER TABLE `tender_requirement_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `tender_sub_components`
--
ALTER TABLE `tender_sub_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tender_id` (`tender_component_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `tender_sub_component_material_prices`
--
ALTER TABLE `tender_sub_component_material_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tender_sub_component_id` (`tender_sub_component_id`),
  ADD KEY `tender_material_price_id` (`tender_material_price_id`);

--
-- Indexes for table `toolbox_talk_registers`
--
ALTER TABLE `toolbox_talk_registers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `site_id` (`site_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `toolbox_talk_register_participants`
--
ALTER TABLE `toolbox_talk_register_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `toolbox_talk_register_id` (`toolbox_talk_register_id`);

--
-- Indexes for table `toolbox_talk_register_topics`
--
ALTER TABLE `toolbox_talk_register_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `toolbox_talk_register_id` (`toolbox_talk_register_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `topic_carbon_copies`
--
ALTER TABLE `topic_carbon_copies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `topic_conversations`
--
ALTER TABLE `topic_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `recipient` (`recipient`),
  ADD KEY `sender` (`sender`);

--
-- Indexes for table `topic_conversation_logs`
--
ALTER TABLE `topic_conversation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `topic_subjects`
--
ALTER TABLE `topic_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `transferred_transfer_orders`
--
ALTER TABLE `transferred_transfer_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfer_id` (`transfer_id`),
  ADD KEY `requisition_approval_id` (`requisition_approval_id`);

--
-- Indexes for table `transfer_requisitions`
--
ALTER TABLE `transfer_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `destination_location_id` (`destination_location_id`);

--
-- Indexes for table `transfer_requisition_assets`
--
ALTER TABLE `transfer_requisition_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `unprocured_deliveries`
--
ALTER TABLE `unprocured_deliveries`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `delivery_for` (`delivery_for`),
  ADD KEY `location_id` (`location_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `unprocured_delivery_asset_items`
--
ALTER TABLE `unprocured_delivery_asset_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `delivery_id` (`delivery_id`),
  ADD KEY `asset_item_id` (`asset_item_id`);

--
-- Indexes for table `unprocured_delivery_grns`
--
ALTER TABLE `unprocured_delivery_grns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_id` (`delivery_id`),
  ADD KEY `grn_id` (`grn_id`);

--
-- Indexes for table `unprocured_delivery_material_items`
--
ALTER TABLE `unprocured_delivery_material_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `delivery_id` (`delivery_id`),
  ADD KEY `material_item_id` (`material_item_id`);

--
-- Indexes for table `unprocured_delivery_material_item_grn_items`
--
ALTER TABLE `unprocured_delivery_material_item_grn_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grn_item_id` (`grn_item_id`),
  ADD KEY `unprocured_delivery_material_item_id` (`unprocured_delivery_material_item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `confidentiality_level_id` (`confidentiality_level_id`);

--
-- Indexes for table `users_permissions`
--
ALTER TABLE `users_permissions`
  ADD PRIMARY KEY (`user_permission_id`),
  ADD KEY `employee_id` (`user_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `user_permission_privileges`
--
ALTER TABLE `user_permission_privileges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_privilege_id` (`permission_privilege_id`),
  ADD KEY `user_permission_privileges_ibfk_1` (`user_permission_id`);

--
-- Indexes for table `withholding_taxes`
--
ALTER TABLE `withholding_taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `debit_account_id` (`credit_account_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `debit_account_id_2` (`debit_account_id`),
  ADD KEY `withholding_taxes_ibfk_5` (`receipt_item_id`),
  ADD KEY `withholding_taxes_ibfk_4` (`payment_voucher_item_id`),
  ADD KEY `stakeholder_id` (`stakeholder_id`);

--
-- Indexes for table `withholding_taxes_payments`
--
ALTER TABLE `withholding_taxes_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `withholding_tax_id` (`withholding_tax_id`),
  ADD KEY `paid_by` (`paid_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_groups`
--
ALTER TABLE `account_groups`
  MODIFY `account_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allowances`
--
ALTER TABLE `allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_chain_levels`
--
ALTER TABLE `approval_chain_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_modules`
--
ALTER TABLE `approval_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `approved_invoice_payment_cancellations`
--
ALTER TABLE `approved_invoice_payment_cancellations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approved_requisition_payment_cancellations`
--
ALTER TABLE `approved_requisition_payment_cancellations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approved_sub_contract_payment_cancellations`
--
ALTER TABLE `approved_sub_contract_payment_cancellations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_cost_center_assignments`
--
ALTER TABLE `asset_cost_center_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_cost_center_assignment_items`
--
ALTER TABLE `asset_cost_center_assignment_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_depreciation_rates`
--
ALTER TABLE `asset_depreciation_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_depreciation_rate_items`
--
ALTER TABLE `asset_depreciation_rate_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_groups`
--
ALTER TABLE `asset_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_handovers`
--
ALTER TABLE `asset_handovers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_handover_items`
--
ALTER TABLE `asset_handover_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_items`
--
ALTER TABLE `asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_sub_location_histories`
--
ALTER TABLE `asset_sub_location_histories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cancelled_purchase_orders`
--
ALTER TABLE `cancelled_purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `casual_labour_budgets`
--
ALTER TABLE `casual_labour_budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `casual_labour_types`
--
ALTER TABLE `casual_labour_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category_parameters`
--
ALTER TABLE `category_parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `closed_purchase_orders`
--
ALTER TABLE `closed_purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_details`
--
ALTER TABLE `company_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_documents`
--
ALTER TABLE `company_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contractors`
--
ALTER TABLE `contractors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contractor_accounts`
--
ALTER TABLE `contractor_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contras`
--
ALTER TABLE `contras`
  MODIFY `contra_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contra_items`
--
ALTER TABLE `contra_items`
  MODIFY `contra_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_centers`
--
ALTER TABLE `cost_centers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_center_accounts`
--
ALTER TABLE `cost_center_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_center_imprest_voucher_items`
--
ALTER TABLE `cost_center_imprest_voucher_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_center_payment_voucher_items`
--
ALTER TABLE `cost_center_payment_voucher_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_center_purchase_orders`
--
ALTER TABLE `cost_center_purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cost_center_requisitions`
--
ALTER TABLE `cost_center_requisitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department_payment_voucher_items`
--
ALTER TABLE `department_payment_voucher_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deployments`
--
ALTER TABLE `deployments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deployment_attachments`
--
ALTER TABLE `deployment_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deployment_category_parameters`
--
ALTER TABLE `deployment_category_parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deployment_persons`
--
ALTER TABLE `deployment_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees_avatars`
--
ALTER TABLE `employees_avatars`
  MODIFY `avatar_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees_contracts`
--
ALTER TABLE `employees_contracts`
  MODIFY `contract_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_approval_chain_levels`
--
ALTER TABLE `employee_approval_chain_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_banks`
--
ALTER TABLE `employee_banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_confidentiality_levels`
--
ALTER TABLE `employee_confidentiality_levels`
  MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_contracts`
--
ALTER TABLE `employee_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_contract_closes`
--
ALTER TABLE `employee_contract_closes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_designations`
--
ALTER TABLE `employee_designations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_loans`
--
ALTER TABLE `employee_loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_loan_repay`
--
ALTER TABLE `employee_loan_repay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_ssfs`
--
ALTER TABLE `employee_ssfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiries`
--
ALTER TABLE `enquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiry_asset_items`
--
ALTER TABLE `enquiry_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiry_material_items`
--
ALTER TABLE `enquiry_material_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiry_service_items`
--
ALTER TABLE `enquiry_service_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment_budgets`
--
ALTER TABLE `equipment_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment_hiring_orders`
--
ALTER TABLE `equipment_hiring_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment_hiring_order_items`
--
ALTER TABLE `equipment_hiring_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_rate_updates`
--
ALTER TABLE `exchange_rate_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `external_material_transfers`
--
ALTER TABLE `external_material_transfers`
  MODIFY `transfer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `external_material_transfer_grns`
--
ALTER TABLE `external_material_transfer_grns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `external_material_transfer_items`
--
ALTER TABLE `external_material_transfer_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `external_transfer_asset_items`
--
ALTER TABLE `external_transfer_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  MODIFY `grn_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_received_note_asset_item_rejects`
--
ALTER TABLE `goods_received_note_asset_item_rejects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_received_note_material_stock_items`
--
ALTER TABLE `goods_received_note_material_stock_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn_asset_sub_location_histories`
--
ALTER TABLE `grn_asset_sub_location_histories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn_invoices`
--
ALTER TABLE `grn_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grn_received_services`
--
ALTER TABLE `grn_received_services`
  MODIFY `service_reception_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hifs`
--
ALTER TABLE `hifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hired_assets`
--
ALTER TABLE `hired_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hired_equipments`
--
ALTER TABLE `hired_equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hired_equipment_costs`
--
ALTER TABLE `hired_equipment_costs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hired_equipment_receipts`
--
ALTER TABLE `hired_equipment_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hse_certificates`
--
ALTER TABLE `hse_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprests`
--
ALTER TABLE `imprests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_cash_items`
--
ALTER TABLE `imprest_cash_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_grns`
--
ALTER TABLE `imprest_grns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_material_items`
--
ALTER TABLE `imprest_material_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_vouchers`
--
ALTER TABLE `imprest_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_asset_items`
--
ALTER TABLE `imprest_voucher_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_cash_items`
--
ALTER TABLE `imprest_voucher_cash_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_contras`
--
ALTER TABLE `imprest_voucher_contras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_material_items`
--
ALTER TABLE `imprest_voucher_material_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_retired_cash`
--
ALTER TABLE `imprest_voucher_retired_cash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_retired_services`
--
ALTER TABLE `imprest_voucher_retired_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_retirements`
--
ALTER TABLE `imprest_voucher_retirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_retirement_asset_items`
--
ALTER TABLE `imprest_voucher_retirement_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_retirement_grns`
--
ALTER TABLE `imprest_voucher_retirement_grns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_retirement_material_items`
--
ALTER TABLE `imprest_voucher_retirement_material_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `imprest_voucher_service_items`
--
ALTER TABLE `imprest_voucher_service_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_job_cards`
--
ALTER TABLE `incident_job_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspections`
--
ALTER TABLE `inspections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_categories`
--
ALTER TABLE `inspection_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_category_parameters`
--
ALTER TABLE `inspection_category_parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_category_parameter_types`
--
ALTER TABLE `inspection_category_parameter_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_job_cards`
--
ALTER TABLE `inspection_job_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internal_material_transfers`
--
ALTER TABLE `internal_material_transfers`
  MODIFY `transfer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internal_material_transfer_items`
--
ALTER TABLE `internal_material_transfer_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internal_transfer_asset_items`
--
ALTER TABLE `internal_transfer_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_locations`
--
ALTER TABLE `inventory_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_journal_voucher_items`
--
ALTER TABLE `invoice_journal_voucher_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_payment_vouchers`
--
ALTER TABLE `invoice_payment_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_card_labours`
--
ALTER TABLE `job_card_labours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_card_services`
--
ALTER TABLE `job_card_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_positions`
--
ALTER TABLE `job_positions`
  MODIFY `job_position_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_contras`
--
ALTER TABLE `journal_contras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_payment_vouchers`
--
ALTER TABLE `journal_payment_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_receipts`
--
ALTER TABLE `journal_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_vouchers`
--
ALTER TABLE `journal_vouchers`
  MODIFY `journal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_voucher_attachments`
--
ALTER TABLE `journal_voucher_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_voucher_credit_accounts`
--
ALTER TABLE `journal_voucher_credit_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_voucher_items`
--
ALTER TABLE `journal_voucher_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_voucher_item_approved_cash_request_items`
--
ALTER TABLE `journal_voucher_item_approved_cash_request_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_voucher_item_approved_invoice_items`
--
ALTER TABLE `journal_voucher_item_approved_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_voucher_item_approved_sub_contract_requisition_items`
--
ALTER TABLE `journal_voucher_item_approved_sub_contract_requisition_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_invoices`
--
ALTER TABLE `maintenance_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_services`
--
ALTER TABLE `maintenance_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_service_items`
--
ALTER TABLE `maintenance_service_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_service_receipts`
--
ALTER TABLE `maintenance_service_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_average_prices`
--
ALTER TABLE `material_average_prices`
  MODIFY `average_price_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_budgets`
--
ALTER TABLE `material_budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_costs`
--
ALTER TABLE `material_costs`
  MODIFY `material_cost_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_cost_center_assignments`
--
ALTER TABLE `material_cost_center_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_cost_center_assignment_items`
--
ALTER TABLE `material_cost_center_assignment_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_disposals`
--
ALTER TABLE `material_disposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_disposal_items`
--
ALTER TABLE `material_disposal_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_items`
--
ALTER TABLE `material_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_item_categories`
--
ALTER TABLE `material_item_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_opening_stocks`
--
ALTER TABLE `material_opening_stocks`
  MODIFY `opening_stock_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_stocks`
--
ALTER TABLE `material_stocks`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `measurement_units`
--
ALTER TABLE `measurement_units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `miscellaneous_budgets`
--
ALTER TABLE `miscellaneous_budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `official_hifs`
--
ALTER TABLE `official_hifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `official_ssfs`
--
ALTER TABLE `official_ssfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ordered_pre_orders`
--
ALTER TABLE `ordered_pre_orders`
  MODIFY `ordered_pre_order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outgoing_invoices`
--
ALTER TABLE `outgoing_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outgoing_invoice_items`
--
ALTER TABLE `outgoing_invoice_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `owned_equipment_costs`
--
ALTER TABLE `owned_equipment_costs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parameter_types`
--
ALTER TABLE `parameter_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_request_approval_journal_vouchers`
--
ALTER TABLE `payment_request_approval_journal_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  MODIFY `payment_voucher_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_voucher_credit_accounts`
--
ALTER TABLE `payment_voucher_credit_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_voucher_grns`
--
ALTER TABLE `payment_voucher_grns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_voucher_items`
--
ALTER TABLE `payment_voucher_items`
  MODIFY `payment_voucher_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_voucher_item_approved_cash_request_items`
--
ALTER TABLE `payment_voucher_item_approved_cash_request_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_voucher_item_approved_invoice_items`
--
ALTER TABLE `payment_voucher_item_approved_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_voucher_item_approved_sub_contract_requisition_items`
--
ALTER TABLE `payment_voucher_item_approved_sub_contract_requisition_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_approvals`
--
ALTER TABLE `payroll_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_employee_allowances`
--
ALTER TABLE `payroll_employee_allowances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_employee_basic_info`
--
ALTER TABLE `payroll_employee_basic_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_employer_deductions`
--
ALTER TABLE `payroll_employer_deductions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_journal_vouchers`
--
ALTER TABLE `payroll_journal_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_payment_vouchers`
--
ALTER TABLE `payroll_payment_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permanent_labour_budgets`
--
ALTER TABLE `permanent_labour_budgets`
  MODIFY `budget_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permanent_labour_costs`
--
ALTER TABLE `permanent_labour_costs`
  MODIFY `permanent_labour_cost_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `permission_privileges`
--
ALTER TABLE `permission_privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `procurement_attachments`
--
ALTER TABLE `procurement_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_accounts`
--
ALTER TABLE `project_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_attachments`
--
ALTER TABLE `project_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_categories`
--
ALTER TABLE `project_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_certificates`
--
ALTER TABLE `project_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_certificate_invoices`
--
ALTER TABLE `project_certificate_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_certificate_receipts`
--
ALTER TABLE `project_certificate_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_closures`
--
ALTER TABLE `project_closures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_contract_reviews`
--
ALTER TABLE `project_contract_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_imprest_voucher_items`
--
ALTER TABLE `project_imprest_voucher_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_payment_voucher_items`
--
ALTER TABLE `project_payment_voucher_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plans`
--
ALTER TABLE `project_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plan_tasks`
--
ALTER TABLE `project_plan_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plan_task_casual_labour_budgets`
--
ALTER TABLE `project_plan_task_casual_labour_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plan_task_equipment_budgets`
--
ALTER TABLE `project_plan_task_equipment_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plan_task_executions`
--
ALTER TABLE `project_plan_task_executions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plan_task_execution_casual_labour`
--
ALTER TABLE `project_plan_task_execution_casual_labour`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plan_task_execution_equipments`
--
ALTER TABLE `project_plan_task_execution_equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plan_task_execution_material_costs`
--
ALTER TABLE `project_plan_task_execution_material_costs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_plan_task_material_budgets`
--
ALTER TABLE `project_plan_task_material_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_purchase_orders`
--
ALTER TABLE `project_purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_requisitions`
--
ALTER TABLE `project_requisitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_special_budgets`
--
ALTER TABLE `project_special_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_team_members`
--
ALTER TABLE `project_team_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_asset_items`
--
ALTER TABLE `purchase_order_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_grns`
--
ALTER TABLE `purchase_order_grns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_invoices`
--
ALTER TABLE `purchase_order_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_material_items`
--
ALTER TABLE `purchase_order_material_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_material_item_grn_items`
--
ALTER TABLE `purchase_order_material_item_grn_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_payment_requests`
--
ALTER TABLE `purchase_order_payment_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_payment_request_approvals`
--
ALTER TABLE `purchase_order_payment_request_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_payment_request_approval_cash_items`
--
ALTER TABLE `purchase_order_payment_request_approval_cash_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_payment_request_approval_invoice_items`
--
ALTER TABLE `purchase_order_payment_request_approval_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_payment_request_approval_payment_vouchers`
--
ALTER TABLE `purchase_order_payment_request_approval_payment_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_payment_request_attachments`
--
ALTER TABLE `purchase_order_payment_request_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_payment_request_cash_items`
--
ALTER TABLE `purchase_order_payment_request_cash_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_payment_request_invoice_items`
--
ALTER TABLE `purchase_order_payment_request_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_service_items`
--
ALTER TABLE `purchase_order_service_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts`
--
ALTER TABLE `receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipt_items`
--
ALTER TABLE `receipt_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registered_certificates`
--
ALTER TABLE `registered_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rejected_payrolls`
--
ALTER TABLE `rejected_payrolls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisitions`
--
ALTER TABLE `requisitions`
  MODIFY `requisition_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approvals`
--
ALTER TABLE `requisition_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approval_asset_items`
--
ALTER TABLE `requisition_approval_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approval_cash_items`
--
ALTER TABLE `requisition_approval_cash_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approval_cash_item_expense_accounts`
--
ALTER TABLE `requisition_approval_cash_item_expense_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approval_imprest_vouchers`
--
ALTER TABLE `requisition_approval_imprest_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approval_material_items`
--
ALTER TABLE `requisition_approval_material_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approval_material_item_expense_accounts`
--
ALTER TABLE `requisition_approval_material_item_expense_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approval_payment_vouchers`
--
ALTER TABLE `requisition_approval_payment_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_approval_service_items`
--
ALTER TABLE `requisition_approval_service_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_asset_items`
--
ALTER TABLE `requisition_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_attachments`
--
ALTER TABLE `requisition_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_cash_items`
--
ALTER TABLE `requisition_cash_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_cash_item_tasks`
--
ALTER TABLE `requisition_cash_item_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_equipment_items`
--
ALTER TABLE `requisition_equipment_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_equipment_item_tasks`
--
ALTER TABLE `requisition_equipment_item_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_material_items`
--
ALTER TABLE `requisition_material_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_material_item_tasks`
--
ALTER TABLE `requisition_material_item_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_purchase_orders`
--
ALTER TABLE `requisition_purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_service_items`
--
ALTER TABLE `requisition_service_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revised_tasks`
--
ALTER TABLE `revised_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `revision`
--
ALTER TABLE `revision`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_diary_compliances`
--
ALTER TABLE `site_diary_compliances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_diary_compliance_statuses`
--
ALTER TABLE `site_diary_compliance_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_topics`
--
ALTER TABLE `site_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ssfs`
--
ALTER TABLE `ssfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ssf_groups`
--
ALTER TABLE `ssf_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ssf_group_stations`
--
ALTER TABLE `ssf_group_stations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stakeholders`
--
ALTER TABLE `stakeholders`
  MODIFY `stakeholder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stakeholder_evaluation_factors`
--
ALTER TABLE `stakeholder_evaluation_factors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stakeholder_evaluation_scores`
--
ALTER TABLE `stakeholder_evaluation_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stakeholder_invoices`
--
ALTER TABLE `stakeholder_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_disposal_asset_items`
--
ALTER TABLE `stock_disposal_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_sales`
--
ALTER TABLE `stock_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_sales_asset_items`
--
ALTER TABLE `stock_sales_asset_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_sales_material_items`
--
ALTER TABLE `stock_sales_material_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_sale_invoices`
--
ALTER TABLE `stock_sale_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_sale_receipts`
--
ALTER TABLE `stock_sale_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subtasks`
--
ALTER TABLE `subtasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contracts`
--
ALTER TABLE `sub_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contracts_items`
--
ALTER TABLE `sub_contracts_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_budgets`
--
ALTER TABLE `sub_contract_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_certificates`
--
ALTER TABLE `sub_contract_certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_certificate_payment_vouchers`
--
ALTER TABLE `sub_contract_certificate_payment_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_certificate_tasks`
--
ALTER TABLE `sub_contract_certificate_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_payment_requisitions`
--
ALTER TABLE `sub_contract_payment_requisitions`
  MODIFY `sub_contract_requisition_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_payment_requisition_approvals`
--
ALTER TABLE `sub_contract_payment_requisition_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_payment_requisition_approval_items`
--
ALTER TABLE `sub_contract_payment_requisition_approval_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_payment_requisition_approval_journal_vouchers`
--
ALTER TABLE `sub_contract_payment_requisition_approval_journal_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_payment_requisition_approval_payment_vouchers`
--
ALTER TABLE `sub_contract_payment_requisition_approval_payment_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_payment_requisition_attachments`
--
ALTER TABLE `sub_contract_payment_requisition_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_contract_payment_requisition_items`
--
ALTER TABLE `sub_contract_payment_requisition_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_locations`
--
ALTER TABLE `sub_locations`
  MODIFY `sub_location_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_payment_voucher_items`
--
ALTER TABLE `task_payment_voucher_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_progress_updates`
--
ALTER TABLE `task_progress_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_tables`
--
ALTER TABLE `tax_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_table_items`
--
ALTER TABLE `tax_table_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tenders`
--
ALTER TABLE `tenders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_attachments`
--
ALTER TABLE `tender_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_awards`
--
ALTER TABLE `tender_awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_components`
--
ALTER TABLE `tender_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_component_lumpsum_prices`
--
ALTER TABLE `tender_component_lumpsum_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_component_material_prices`
--
ALTER TABLE `tender_component_material_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_lumpsum_prices`
--
ALTER TABLE `tender_lumpsum_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_material_prices`
--
ALTER TABLE `tender_material_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_requirements`
--
ALTER TABLE `tender_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_requirement_types`
--
ALTER TABLE `tender_requirement_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_sub_components`
--
ALTER TABLE `tender_sub_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tender_sub_component_material_prices`
--
ALTER TABLE `tender_sub_component_material_prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `toolbox_talk_registers`
--
ALTER TABLE `toolbox_talk_registers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `toolbox_talk_register_participants`
--
ALTER TABLE `toolbox_talk_register_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `toolbox_talk_register_topics`
--
ALTER TABLE `toolbox_talk_register_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topic_carbon_copies`
--
ALTER TABLE `topic_carbon_copies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topic_conversations`
--
ALTER TABLE `topic_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topic_conversation_logs`
--
ALTER TABLE `topic_conversation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topic_subjects`
--
ALTER TABLE `topic_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transferred_transfer_orders`
--
ALTER TABLE `transferred_transfer_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfer_requisitions`
--
ALTER TABLE `transfer_requisitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfer_requisition_assets`
--
ALTER TABLE `transfer_requisition_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unprocured_deliveries`
--
ALTER TABLE `unprocured_deliveries`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unprocured_delivery_asset_items`
--
ALTER TABLE `unprocured_delivery_asset_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unprocured_delivery_grns`
--
ALTER TABLE `unprocured_delivery_grns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unprocured_delivery_material_items`
--
ALTER TABLE `unprocured_delivery_material_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unprocured_delivery_material_item_grn_items`
--
ALTER TABLE `unprocured_delivery_material_item_grn_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_permissions`
--
ALTER TABLE `users_permissions`
  MODIFY `user_permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_permission_privileges`
--
ALTER TABLE `user_permission_privileges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withholding_taxes`
--
ALTER TABLE `withholding_taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withholding_taxes_payments`
--
ALTER TABLE `withholding_taxes_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`account_group_id`) REFERENCES `account_groups` (`account_group_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `accounts_ibfk_2` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `accounts_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `account_groups`
--
ALTER TABLE `account_groups`
  ADD CONSTRAINT `account_groups_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `account_groups` (`account_group_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `account_groups_ibfk_2` FOREIGN KEY (`group_nature_id`) REFERENCES `account_groups` (`account_group_id`) ON UPDATE CASCADE;

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `allowances`
--
ALTER TABLE `allowances`
  ADD CONSTRAINT `creator_id` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `approval_chain_levels`
--
ALTER TABLE `approval_chain_levels`
  ADD CONSTRAINT `approval_chain_levels_ibfk_1` FOREIGN KEY (`approval_module_id`) REFERENCES `approval_modules` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `approval_chain_levels_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `approved_invoice_payment_cancellations`
--
ALTER TABLE `approved_invoice_payment_cancellations`
  ADD CONSTRAINT `approved_invoice_payment_cancellations_ibfk_1` FOREIGN KEY (`purchase_order_payment_request_approval_id`) REFERENCES `purchase_order_payment_request_approvals` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `approved_invoice_payment_cancellations_ibfk_2` FOREIGN KEY (`cancelled_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `approved_requisition_payment_cancellations`
--
ALTER TABLE `approved_requisition_payment_cancellations`
  ADD CONSTRAINT `approved_payment_cancellations_ibfk_1` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `approved_payment_cancellations_ibfk_2` FOREIGN KEY (`cancelled_by`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `approved_sub_contract_payment_cancellations`
--
ALTER TABLE `approved_sub_contract_payment_cancellations`
  ADD CONSTRAINT `approved_sub_contract_payment_cancellations_ibfk_1` FOREIGN KEY (`cancelled_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `approved_sub_contract_payment_cancellations_ibfk_2` FOREIGN KEY (`sub_contract_payment_requisition_approval_id`) REFERENCES `sub_contract_payment_requisition_approvals` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `assets_ibfk_4` FOREIGN KEY (`asset_item_id`) REFERENCES `asset_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_cost_center_assignments`
--
ALTER TABLE `asset_cost_center_assignments`
  ADD CONSTRAINT `asset_cost_center_assignments_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_cost_center_assignments_ibfk_2` FOREIGN KEY (`destination_project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_cost_center_assignments_ibfk_3` FOREIGN KEY (`source_project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_cost_center_assignments_ibfk_4` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_cost_center_assignment_items`
--
ALTER TABLE `asset_cost_center_assignment_items`
  ADD CONSTRAINT `asset_cost_center_assignment_items_ibfk_1` FOREIGN KEY (`asset_cost_center_assignment_id`) REFERENCES `asset_cost_center_assignments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_cost_center_assignment_items_ibfk_2` FOREIGN KEY (`asset_sub_location_history_id`) REFERENCES `asset_sub_location_histories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_groups`
--
ALTER TABLE `asset_groups`
  ADD CONSTRAINT `asset_groups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_groups_ibfk_2` FOREIGN KEY (`project_nature_id`) REFERENCES `project_categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_groups_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `asset_groups` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_handovers`
--
ALTER TABLE `asset_handovers`
  ADD CONSTRAINT `asset_handovers_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_handovers_ibfk_2` FOREIGN KEY (`handler_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_handovers_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_handover_items`
--
ALTER TABLE `asset_handover_items`
  ADD CONSTRAINT `asset_handover_items_ibfk_1` FOREIGN KEY (`asset_handover_id`) REFERENCES `asset_handovers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_handover_items_ibfk_2` FOREIGN KEY (`asset_sub_location_history_id`) REFERENCES `asset_sub_location_histories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_items`
--
ALTER TABLE `asset_items`
  ADD CONSTRAINT `asset_items_ibfk_1` FOREIGN KEY (`asset_group_id`) REFERENCES `asset_groups` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_items_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `asset_sub_location_histories`
--
ALTER TABLE `asset_sub_location_histories`
  ADD CONSTRAINT `asset_sub_location_histories_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_sub_location_histories_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_sub_location_histories_ibfk_3` FOREIGN KEY (`sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asset_sub_location_histories_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD CONSTRAINT `account_id_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bank_id_fk` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `creator_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `cancelled_purchase_orders`
--
ALTER TABLE `cancelled_purchase_orders`
  ADD CONSTRAINT `cancelled_purchase_orders_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cancelled_purchase_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `casual_labour_budgets`
--
ALTER TABLE `casual_labour_budgets`
  ADD CONSTRAINT `casual_labour_budgets_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `casual_labour_budgets_ibfk_2` FOREIGN KEY (`casual_labour_type_id`) REFERENCES `casual_labour_types` (`type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `casual_labour_budgets_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `casual_labour_budgets_ibfk_4` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `category_parameters`
--
ALTER TABLE `category_parameters`
  ADD CONSTRAINT `category_parameters_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `category_parameters_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `closed_purchase_orders`
--
ALTER TABLE `closed_purchase_orders`
  ADD CONSTRAINT `closed_purchase_orders_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `closed_purchase_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `company_details`
--
ALTER TABLE `company_details`
  ADD CONSTRAINT `company_details_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `company_documents`
--
ALTER TABLE `company_documents`
  ADD CONSTRAINT `company_documents_ibfk_1` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contractor_accounts`
--
ALTER TABLE `contractor_accounts`
  ADD CONSTRAINT `contractor_accounts_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `contractor_accounts_ibfk_2` FOREIGN KEY (`contractor_id`) REFERENCES `contractors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contras`
--
ALTER TABLE `contras`
  ADD CONSTRAINT `contras_ibfk_1` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `contras_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `contras_ibfk_3` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `contra_items`
--
ALTER TABLE `contra_items`
  ADD CONSTRAINT `contra_items_ibfk_1` FOREIGN KEY (`contra_id`) REFERENCES `contras` (`contra_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contra_items_ibfk_2` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `contra_items_ibfk_3` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `cost_center_accounts`
--
ALTER TABLE `cost_center_accounts`
  ADD CONSTRAINT `cost_center_accounts_ibfk_1` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cost_center_accounts_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `cost_center_imprest_voucher_items`
--
ALTER TABLE `cost_center_imprest_voucher_items`
  ADD CONSTRAINT `cost_center_fk` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`),
  ADD CONSTRAINT `imprest_cash_fk` FOREIGN KEY (`imprest_voucher_cash_item_id`) REFERENCES `imprest_voucher_cash_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_service_fk` FOREIGN KEY (`imprest_voucher_service_item_id`) REFERENCES `imprest_voucher_service_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `cost_center_payment_voucher_items`
--
ALTER TABLE `cost_center_payment_voucher_items`
  ADD CONSTRAINT `cost_center_payment_voucher_items_ibfk_1` FOREIGN KEY (`payment_voucher_item_id`) REFERENCES `payment_voucher_items` (`payment_voucher_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cost_center_payment_voucher_items_ibfk_2` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `cost_center_purchase_orders`
--
ALTER TABLE `cost_center_purchase_orders`
  ADD CONSTRAINT `cost_center_purchase_orders_ibfk_1` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cost_center_purchase_orders_ibfk_2` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cost_center_requisitions`
--
ALTER TABLE `cost_center_requisitions`
  ADD CONSTRAINT `cost_center_requisitions_ibfk_1` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cost_center_requisitions_ibfk_2` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `department_payment_voucher_items`
--
ALTER TABLE `department_payment_voucher_items`
  ADD CONSTRAINT `department_payment_voucher_items_ibfk_1` FOREIGN KEY (`payment_voucher_item_id`) REFERENCES `payment_voucher_items` (`payment_voucher_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `department_payment_voucher_items_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON UPDATE CASCADE;

--
-- Constraints for table `deployments`
--
ALTER TABLE `deployments`
  ADD CONSTRAINT `deployments_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `deployment_attachments`
--
ALTER TABLE `deployment_attachments`
  ADD CONSTRAINT `deployment_attachments_ibfk_1` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `deployment_attachments_ibfk_2` FOREIGN KEY (`deployment_id`) REFERENCES `deployments` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `deployment_category_parameters`
--
ALTER TABLE `deployment_category_parameters`
  ADD CONSTRAINT `deployment_category_parameters_ibfk_1` FOREIGN KEY (`deployment_id`) REFERENCES `deployments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `deployment_category_parameters_ibfk_2` FOREIGN KEY (`category_parameter_id`) REFERENCES `category_parameters` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `deployment_persons`
--
ALTER TABLE `deployment_persons`
  ADD CONSTRAINT `deployment_persons_ibfk_1` FOREIGN KEY (`deployment_id`) REFERENCES `deployments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `job_positions` (`job_position_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `employees_avatars`
--
ALTER TABLE `employees_avatars`
  ADD CONSTRAINT `employees_avatars_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employees_contracts`
--
ALTER TABLE `employees_contracts`
  ADD CONSTRAINT `employees_contracts_ibfk_1` FOREIGN KEY (`registrar_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employees_contracts_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD CONSTRAINT `acc_id_fk` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `emp_id_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `emplo_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `employee_allowances`
--
ALTER TABLE `employee_allowances`
  ADD CONSTRAINT `allowance_id_fk` FOREIGN KEY (`allowance_id`) REFERENCES `allowances` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `creator_id_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_id_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `employee_approval_chain_levels`
--
ALTER TABLE `employee_approval_chain_levels`
  ADD CONSTRAINT `employee_approval_chain_levels_ibfk_1` FOREIGN KEY (`approval_chain_level_id`) REFERENCES `approval_chain_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_approval_chain_levels_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_approval_chain_levels_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `employee_banks`
--
ALTER TABLE `employee_banks`
  ADD CONSTRAINT `employee_banks_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `banks` (`id`);

--
-- Constraints for table `employee_confidentiality_levels`
--
ALTER TABLE `employee_confidentiality_levels`
  ADD CONSTRAINT `employee_confidentiality_levels_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `employee_contracts`
--
ALTER TABLE `employee_contracts`
  ADD CONSTRAINT `employee_contracts_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `employee_contracts_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `employee_contract_closes`
--
ALTER TABLE `employee_contract_closes`
  ADD CONSTRAINT `employee_contract_closes_ibfk_1` FOREIGN KEY (`employee_contract_id`) REFERENCES `employee_contracts` (`id`);

--
-- Constraints for table `employee_designations`
--
ALTER TABLE `employee_designations`
  ADD CONSTRAINT `employee_designations_ibfk_1` FOREIGN KEY (`employee_contract_id`) REFERENCES `employee_contracts` (`id`),
  ADD CONSTRAINT `employee_designations_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `employee_designations_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`),
  ADD CONSTRAINT `employee_designations_ibfk_4` FOREIGN KEY (`job_position_id`) REFERENCES `job_positions` (`job_position_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_designations_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `employee_loans`
--
ALTER TABLE `employee_loans`
  ADD CONSTRAINT `creator_table_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `emp_loan_account_fk` FOREIGN KEY (`loan_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_table_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `loan_id_fk` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `employee_loan_repay`
--
ALTER TABLE `employee_loan_repay`
  ADD CONSTRAINT `creator_emp_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_fk_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_loan_id_fk` FOREIGN KEY (`employee_loan_id`) REFERENCES `employee_loans` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `loan_fk` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD CONSTRAINT `employee_salaries_ibfk_1` FOREIGN KEY (`employee_contract_id`) REFERENCES `employee_contracts` (`id`);

--
-- Constraints for table `employee_ssfs`
--
ALTER TABLE `employee_ssfs`
  ADD CONSTRAINT `employee_ssfs_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `ssf_id_fk` FOREIGN KEY (`ssf_id`) REFERENCES `ssfs` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `enquiries`
--
ALTER TABLE `enquiries`
  ADD CONSTRAINT `enquiries_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `enquiries_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `enquiries_ibfk_4` FOREIGN KEY (`cost_center_id`) REFERENCES `cost_centers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `enquiries_ibfk_5` FOREIGN KEY (`enquiry_to`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `enquiry_asset_items`
--
ALTER TABLE `enquiry_asset_items`
  ADD CONSTRAINT `enquiry_asset_items_ibfk_1` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enquiry_asset_items_ibfk_2` FOREIGN KEY (`asset_item_id`) REFERENCES `asset_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `enquiry_material_items`
--
ALTER TABLE `enquiry_material_items`
  ADD CONSTRAINT `enquiry_material_items_ibfk_1` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enquiry_material_items_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE;

--
-- Constraints for table `enquiry_service_items`
--
ALTER TABLE `enquiry_service_items`
  ADD CONSTRAINT `enquiry_service_items_ibfk_1` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `enquiry_service_items_ibfk_2` FOREIGN KEY (`measurement_unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE;

--
-- Constraints for table `equipment_budgets`
--
ALTER TABLE `equipment_budgets`
  ADD CONSTRAINT `equipment_budgets_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `equipment_budgets_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `equipment_budgets_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `equipment_budgets_ibfk_5` FOREIGN KEY (`asset_item_id`) REFERENCES `asset_items` (`id`);

--
-- Constraints for table `equipment_hiring_orders`
--
ALTER TABLE `equipment_hiring_orders`
  ADD CONSTRAINT `equipment_hiring_orders_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `exchange_rate_updates`
--
ALTER TABLE `exchange_rate_updates`
  ADD CONSTRAINT `exchange_rate_updates_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exchange_rate_updates_ibfk_2` FOREIGN KEY (`updater_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `external_material_transfers`
--
ALTER TABLE `external_material_transfers`
  ADD CONSTRAINT `external_material_transfers_ibfk_1` FOREIGN KEY (`source_location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `external_material_transfers_ibfk_2` FOREIGN KEY (`destination_location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `external_material_transfers_ibfk_3` FOREIGN KEY (`sender_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `external_material_transfers_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `external_material_transfer_grns`
--
ALTER TABLE `external_material_transfer_grns`
  ADD CONSTRAINT `external_material_transfer_grns_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `external_material_transfer_grns_ibfk_2` FOREIGN KEY (`transfer_id`) REFERENCES `external_material_transfers` (`transfer_id`) ON UPDATE CASCADE;

--
-- Constraints for table `external_material_transfer_items`
--
ALTER TABLE `external_material_transfer_items`
  ADD CONSTRAINT `external_material_transfer_items_ibfk_1` FOREIGN KEY (`transfer_id`) REFERENCES `external_material_transfers` (`transfer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `external_material_transfer_items_ibfk_2` FOREIGN KEY (`source_sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `external_material_transfer_items_ibfk_3` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `external_material_transfer_items_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `external_transfer_asset_items`
--
ALTER TABLE `external_transfer_asset_items`
  ADD CONSTRAINT `external_transfer_asset_items_ibfk_2` FOREIGN KEY (`source_sub_location_history_id`) REFERENCES `asset_sub_location_histories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `external_transfer_asset_items_ibfk_3` FOREIGN KEY (`transfer_id`) REFERENCES `external_material_transfers` (`transfer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `goods_received_notes`
--
ALTER TABLE `goods_received_notes`
  ADD CONSTRAINT `goods_received_notes_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `goods_received_notes_ibfk_4` FOREIGN KEY (`receiver_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `goods_received_note_asset_item_rejects`
--
ALTER TABLE `goods_received_note_asset_item_rejects`
  ADD CONSTRAINT `goods_received_note_asset_item_rejects_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `goods_received_note_asset_item_rejects_ibfk_2` FOREIGN KEY (`purchase_order_asset_item_id`) REFERENCES `purchase_order_asset_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `goods_received_note_asset_item_rejects_ibfk_3` FOREIGN KEY (`delivery_asset_item_id`) REFERENCES `unprocured_delivery_asset_items` (`item_id`) ON UPDATE CASCADE;

--
-- Constraints for table `goods_received_note_material_stock_items`
--
ALTER TABLE `goods_received_note_material_stock_items`
  ADD CONSTRAINT `goods_received_note_material_stock_items_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `goods_received_note_material_stock_items_ibfk_2` FOREIGN KEY (`stock_id`) REFERENCES `material_stocks` (`stock_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grn_asset_sub_location_histories`
--
ALTER TABLE `grn_asset_sub_location_histories`
  ADD CONSTRAINT `grn_asset_sub_location_histories_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `grn_asset_sub_location_histories_ibfk_2` FOREIGN KEY (`asset_sub_location_history_id`) REFERENCES `asset_sub_location_histories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `grn_invoices`
--
ALTER TABLE `grn_invoices`
  ADD CONSTRAINT `grn_invoices_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `grn_invoices_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grn_received_services`
--
ALTER TABLE `grn_received_services`
  ADD CONSTRAINT `grn_received_services_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `grn_received_services_ibfk_2` FOREIGN KEY (`purchase_order_service_item_id`) REFERENCES `purchase_order_service_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `grn_received_services_ibfk_3` FOREIGN KEY (`sub_location_Id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE;

--
-- Constraints for table `hired_assets`
--
ALTER TABLE `hired_assets`
  ADD CONSTRAINT `hired_asset_asset_id_fk` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `hired_asset_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `hired_asset_sub_location_id_fk` FOREIGN KEY (`sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `hired_asset_vendor_id_fk` FOREIGN KEY (`vendor_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `hired_assets_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON UPDATE CASCADE;

--
-- Constraints for table `hired_equipments`
--
ALTER TABLE `hired_equipments`
  ADD CONSTRAINT `hired_equipments_ibfk_1` FOREIGN KEY (`equipment_receipt_id`) REFERENCES `hired_equipment_receipts` (`id`),
  ADD CONSTRAINT `hired_equipments_ibfk_2` FOREIGN KEY (`asset_group_id`) REFERENCES `asset_groups` (`id`);

--
-- Constraints for table `hired_equipment_costs`
--
ALTER TABLE `hired_equipment_costs`
  ADD CONSTRAINT `hired_equipment_costs_ibfk_1` FOREIGN KEY (`hired_equipment_id`) REFERENCES `hired_equipments` (`id`),
  ADD CONSTRAINT `hired_equipment_costs_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `hired_equipment_costs_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`),
  ADD CONSTRAINT `hired_equipment_costs_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`);

--
-- Constraints for table `hired_equipment_receipts`
--
ALTER TABLE `hired_equipment_receipts`
  ADD CONSTRAINT `hired_equipment_receipts_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `stakeholders` (`stakeholder_id`),
  ADD CONSTRAINT `hired_equipment_receipts_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `hse_certificates`
--
ALTER TABLE `hse_certificates`
  ADD CONSTRAINT `hse_certificates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprests`
--
ALTER TABLE `imprests`
  ADD CONSTRAINT `imprests_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprests_ibfk_6` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_cash_items`
--
ALTER TABLE `imprest_cash_items`
  ADD CONSTRAINT `imprest_cash_items_ibfk_1` FOREIGN KEY (`imprest_id`) REFERENCES `imprests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `imprest_grns`
--
ALTER TABLE `imprest_grns`
  ADD CONSTRAINT `imprest_grns_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_grns_ibfk_2` FOREIGN KEY (`imprest_id`) REFERENCES `imprests` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_material_items`
--
ALTER TABLE `imprest_material_items`
  ADD CONSTRAINT `imprest_material_items_ibfk_1` FOREIGN KEY (`imprest_id`) REFERENCES `imprests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_material_items_ibfk_2` FOREIGN KEY (`goods_received_note_material_stock_item_id`) REFERENCES `goods_received_note_material_stock_items` (`item_id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_vouchers`
--
ALTER TABLE `imprest_vouchers`
  ADD CONSTRAINT `imprest_vouchers_ibfk_1` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_vouchers_ibfk_2` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_vouchers_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_vouchers_ibfk_4` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_vouchers_ibfk_5` FOREIGN KEY (`handler_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_asset_items`
--
ALTER TABLE `imprest_voucher_asset_items`
  ADD CONSTRAINT `imprest_voucher_asset_items_ibfk_1` FOREIGN KEY (`imprest_voucher_id`) REFERENCES `imprest_vouchers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_asset_items_ibfk_2` FOREIGN KEY (`requisition_approval_asset_item_id`) REFERENCES `requisition_approval_asset_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_cash_items`
--
ALTER TABLE `imprest_voucher_cash_items`
  ADD CONSTRAINT `imprest_voucher_cash_items_ibfk_1` FOREIGN KEY (`imprest_voucher_id`) REFERENCES `imprest_vouchers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_cash_items_ibfk_2` FOREIGN KEY (`requisition_approval_cash_item_id`) REFERENCES `requisition_approval_cash_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_contras`
--
ALTER TABLE `imprest_voucher_contras`
  ADD CONSTRAINT `imprest_voucher_contras_ibfk_1` FOREIGN KEY (`imprest_voucher_id`) REFERENCES `imprest_vouchers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_contras_ibfk_2` FOREIGN KEY (`contra_id`) REFERENCES `contras` (`contra_id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_material_items`
--
ALTER TABLE `imprest_voucher_material_items`
  ADD CONSTRAINT `imprest_voucher_material_items_ibfk_1` FOREIGN KEY (`imprest_voucher_id`) REFERENCES `imprest_vouchers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_material_items_ibfk_2` FOREIGN KEY (`requisition_approval_material_item_id`) REFERENCES `requisition_approval_material_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_retired_cash`
--
ALTER TABLE `imprest_voucher_retired_cash`
  ADD CONSTRAINT `imprest_voucher_retired_cash_ibfk_1` FOREIGN KEY (`imprest_voucher_retirement_id`) REFERENCES `imprest_voucher_retirements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retired_cash_ibfk_2` FOREIGN KEY (`imprest_voucher_cash_item_id`) REFERENCES `imprest_voucher_cash_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_retired_services`
--
ALTER TABLE `imprest_voucher_retired_services`
  ADD CONSTRAINT `imprest_voucher_retired_services_ibfk_1` FOREIGN KEY (`imprest_voucher_retirement_id`) REFERENCES `imprest_voucher_retirements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retired_services_ibfk_2` FOREIGN KEY (`imprest_voucher_service_item_id`) REFERENCES `imprest_voucher_service_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_retirements`
--
ALTER TABLE `imprest_voucher_retirements`
  ADD CONSTRAINT `imprest_voucher_retirements_ibfk_1` FOREIGN KEY (`imprest_voucher_id`) REFERENCES `imprest_vouchers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retirements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retirements_ibfk_3` FOREIGN KEY (`examined_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retirements_ibfk_4` FOREIGN KEY (`sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retirements_ibfk_5` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retirements_ibfk_6` FOREIGN KEY (`retirement_to`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_retirement_asset_items`
--
ALTER TABLE `imprest_voucher_retirement_asset_items`
  ADD CONSTRAINT `imprest_voucher_retirement_asset_items_ibfk_1` FOREIGN KEY (`imprest_voucher_retirement_id`) REFERENCES `imprest_voucher_retirements` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retirement_asset_items_ibfk_2` FOREIGN KEY (`asset_item_id`) REFERENCES `asset_items` (`id`);

--
-- Constraints for table `imprest_voucher_retirement_grns`
--
ALTER TABLE `imprest_voucher_retirement_grns`
  ADD CONSTRAINT `imprest_voucher_retirement_grns_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retirement_grns_ibfk_2` FOREIGN KEY (`imprest_voucher_retirement_id`) REFERENCES `imprest_voucher_retirements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_retirement_material_items`
--
ALTER TABLE `imprest_voucher_retirement_material_items`
  ADD CONSTRAINT `imprest_voucher_retirement_material_items_ibfk_1` FOREIGN KEY (`imprest_voucher_retirement_id`) REFERENCES `imprest_voucher_retirements` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_retirement_material_items_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE;

--
-- Constraints for table `imprest_voucher_service_items`
--
ALTER TABLE `imprest_voucher_service_items`
  ADD CONSTRAINT `imprest_voucher_service_items_ibfk_1` FOREIGN KEY (`imprest_voucher_id`) REFERENCES `imprest_vouchers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imprest_voucher_service_items_ibfk_2` FOREIGN KEY (`requisition_approval_service_item_id`) REFERENCES `requisition_approval_service_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `incidents_ibfk_2` FOREIGN KEY (`site_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `incident_job_cards`
--
ALTER TABLE `incident_job_cards`
  ADD CONSTRAINT `incident_job_cards_ibfk_1` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `incident_job_cards_ibfk_2` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inspections`
--
ALTER TABLE `inspections`
  ADD CONSTRAINT `inspections_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inspections_ibfk_2` FOREIGN KEY (`inspector_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inspections_ibfk_3` FOREIGN KEY (`site_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `inspection_categories`
--
ALTER TABLE `inspection_categories`
  ADD CONSTRAINT `inspection_categories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inspection_categories_ibfk_2` FOREIGN KEY (`inspection_id`) REFERENCES `inspections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inspection_category_parameters`
--
ALTER TABLE `inspection_category_parameters`
  ADD CONSTRAINT `inspection_category_parameters_ibfk_1` FOREIGN KEY (`inspection_category_id`) REFERENCES `inspection_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inspection_category_parameters_ibfk_2` FOREIGN KEY (`category_parameter_id`) REFERENCES `category_parameters` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inspection_category_parameter_types`
--
ALTER TABLE `inspection_category_parameter_types`
  ADD CONSTRAINT `inspection_category_parameter_types_ibfk_1` FOREIGN KEY (`inspection_category_parameter_id`) REFERENCES `inspection_category_parameters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inspection_category_parameter_types_ibfk_2` FOREIGN KEY (`parameter_type_id`) REFERENCES `parameter_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inspection_job_cards`
--
ALTER TABLE `inspection_job_cards`
  ADD CONSTRAINT `inspection_job_cards_ibfk_1` FOREIGN KEY (`inspection_id`) REFERENCES `inspections` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `inspection_job_cards_ibfk_2` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `internal_material_transfers`
--
ALTER TABLE `internal_material_transfers`
  ADD CONSTRAINT `internal_material_transfers_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `internal_material_transfers_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `internal_material_transfers_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `internal_material_transfer_items`
--
ALTER TABLE `internal_material_transfer_items`
  ADD CONSTRAINT `internal_material_transfer_items_ibfk_1` FOREIGN KEY (`transfer_id`) REFERENCES `internal_material_transfers` (`transfer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `internal_material_transfer_items_ibfk_2` FOREIGN KEY (`source_sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `internal_material_transfer_items_ibfk_3` FOREIGN KEY (`stock_id`) REFERENCES `material_stocks` (`stock_id`) ON UPDATE CASCADE;

--
-- Constraints for table `internal_transfer_asset_items`
--
ALTER TABLE `internal_transfer_asset_items`
  ADD CONSTRAINT `internal_transfer_asset_items_ibfk_1` FOREIGN KEY (`source_sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `internal_transfer_asset_items_ibfk_2` FOREIGN KEY (`asset_sub_location_history_id`) REFERENCES `asset_sub_location_histories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `internal_transfer_asset_items_ibfk_3` FOREIGN KEY (`transfer_id`) REFERENCES `internal_material_transfers` (`transfer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventory_locations`
--
ALTER TABLE `inventory_locations`
  ADD CONSTRAINT `inventory_locations_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `invoice_journal_voucher_items`
--
ALTER TABLE `invoice_journal_voucher_items`
  ADD CONSTRAINT `invoice_journal_voucher_items_ibfk_1` FOREIGN KEY (`journal_voucher_item_id`) REFERENCES `journal_voucher_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_journal_voucher_items_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `invoice_payment_vouchers`
--
ALTER TABLE `invoice_payment_vouchers`
  ADD CONSTRAINT `invoice_payment_vouchers_ibfk_1` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_payment_vouchers_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD CONSTRAINT `job_cards_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `job_card_labours`
--
ALTER TABLE `job_card_labours`
  ADD CONSTRAINT `job_card_labours_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `job_card_labours_ibfk_2` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `job_card_services`
--
ALTER TABLE `job_card_services`
  ADD CONSTRAINT `job_card_services_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `job_card_services_ibfk_2` FOREIGN KEY (`job_card_labour_id`) REFERENCES `job_card_labours` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `journal_contras`
--
ALTER TABLE `journal_contras`
  ADD CONSTRAINT `journal_contra_ibfk_1` FOREIGN KEY (`contra_id`) REFERENCES `contras` (`contra_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_contra_ibfk_2` FOREIGN KEY (`journal_id`) REFERENCES `journal_vouchers` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `journal_payment_vouchers`
--
ALTER TABLE `journal_payment_vouchers`
  ADD CONSTRAINT `journal_payment_vouchers_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journal_vouchers` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_payment_vouchers_ibfk_2` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `journal_receipts`
--
ALTER TABLE `journal_receipts`
  ADD CONSTRAINT `journal_receipts_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_receipts_ibfk_2` FOREIGN KEY (`journal_id`) REFERENCES `journal_vouchers` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `journal_vouchers`
--
ALTER TABLE `journal_vouchers`
  ADD CONSTRAINT `journal_vouchers_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `journal_voucher_attachments`
--
ALTER TABLE `journal_voucher_attachments`
  ADD CONSTRAINT `journal_voucher_attachments_ibfk_1` FOREIGN KEY (`journal_voucher_id`) REFERENCES `journal_vouchers` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_attachments_ibfk_2` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `journal_voucher_credit_accounts`
--
ALTER TABLE `journal_voucher_credit_accounts`
  ADD CONSTRAINT `journal_voucher_credit_accounts_ibfk_1` FOREIGN KEY (`journal_voucher_id`) REFERENCES `journal_vouchers` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_credit_accounts_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_credit_accounts_ibfk_3` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `journal_voucher_items`
--
ALTER TABLE `journal_voucher_items`
  ADD CONSTRAINT `journal_voucher_items_ibfk_1` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_items_ibfk_2` FOREIGN KEY (`journal_voucher_id`) REFERENCES `journal_vouchers` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_items_ibfk_3` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `journal_voucher_item_approved_cash_request_items`
--
ALTER TABLE `journal_voucher_item_approved_cash_request_items`
  ADD CONSTRAINT `journal_voucher_item_approved_cash_request_items_ibfk_1` FOREIGN KEY (`journal_voucher_item_id`) REFERENCES `journal_voucher_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_item_approved_cash_request_items_ibfk_2` FOREIGN KEY (`requisition_approval_asset_item_id`) REFERENCES `requisition_approval_asset_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_item_approved_cash_request_items_ibfk_3` FOREIGN KEY (`requisition_approval_cash_item_id`) REFERENCES `requisition_approval_cash_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_item_approved_cash_request_items_ibfk_4` FOREIGN KEY (`requisition_approval_material_item_id`) REFERENCES `requisition_approval_material_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_item_approved_cash_request_items_ibfk_5` FOREIGN KEY (`requisition_approval_service_item_id`) REFERENCES `requisition_approval_service_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `journal_voucher_item_approved_invoice_items`
--
ALTER TABLE `journal_voucher_item_approved_invoice_items`
  ADD CONSTRAINT `journal_voucher_item_approved_invoice_items_ibfk_1` FOREIGN KEY (`journal_voucher_item_id`) REFERENCES `journal_voucher_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_voucher_item_approved_invoice_items_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_approval_invoice_item_id`) REFERENCES `purchase_order_payment_request_approval_invoice_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `journal_voucher_item_approved_sub_contract_requisition_items`
--
ALTER TABLE `journal_voucher_item_approved_sub_contract_requisition_items`
  ADD CONSTRAINT `jv_item_approved_sc_req_items_ibfk_1` FOREIGN KEY (`journal_voucher_item_id`) REFERENCES `journal_voucher_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `jv_item_approved_sc_req_items_ibfk_2` FOREIGN KEY (`sub_contract_payment_requisition_approval_item_id`) REFERENCES `sub_contract_payment_requisition_approval_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `employee_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `maintenance_invoices`
--
ALTER TABLE `maintenance_invoices`
  ADD CONSTRAINT `out_fk` FOREIGN KEY (`outgoing_invoice_id`) REFERENCES `outgoing_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `services_fk` FOREIGN KEY (`service_id`) REFERENCES `maintenance_services` (`service_id`) ON UPDATE CASCADE;

--
-- Constraints for table `maintenance_services`
--
ALTER TABLE `maintenance_services`
  ADD CONSTRAINT `client_fk` FOREIGN KEY (`client_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `created_by_employee_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `currency_fk_id` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `maintenance_service_items`
--
ALTER TABLE `maintenance_service_items`
  ADD CONSTRAINT `service_id_fk` FOREIGN KEY (`service_id`) REFERENCES `maintenance_services` (`service_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `unity_id_fk` FOREIGN KEY (`measurement_unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE;

--
-- Constraints for table `maintenance_service_receipts`
--
ALTER TABLE `maintenance_service_receipts`
  ADD CONSTRAINT `maintenance_service_receipts_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `maintenance_service_receipts_ibfk_2` FOREIGN KEY (`maintenance_service_id`) REFERENCES `maintenance_services` (`service_id`) ON UPDATE CASCADE;

--
-- Constraints for table `material_average_prices`
--
ALTER TABLE `material_average_prices`
  ADD CONSTRAINT `material_average_prices_ibfk_1` FOREIGN KEY (`sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_average_prices_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_average_prices_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_average_prices_ibfk_4` FOREIGN KEY (`material_stock_id`) REFERENCES `material_stocks` (`stock_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `material_budgets`
--
ALTER TABLE `material_budgets`
  ADD CONSTRAINT `material_budgets_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_budgets_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_budgets_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `material_budgets_ibfk_4` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `material_costs`
--
ALTER TABLE `material_costs`
  ADD CONSTRAINT `material_costs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_costs_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_costs_ibfk_3` FOREIGN KEY (`source_sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_costs_ibfk_4` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_costs_ibfk_5` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON UPDATE CASCADE;

--
-- Constraints for table `material_cost_center_assignments`
--
ALTER TABLE `material_cost_center_assignments`
  ADD CONSTRAINT `material_cost_center_assignments_ibfk_1` FOREIGN KEY (`source_project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `material_cost_center_assignments_ibfk_2` FOREIGN KEY (`destination_project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `material_cost_center_assignments_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `material_cost_center_assignments_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `material_cost_center_assignment_items`
--
ALTER TABLE `material_cost_center_assignment_items`
  ADD CONSTRAINT `material_cost_center_assignment_items_ibfk_1` FOREIGN KEY (`material_cost_center_assignment_id`) REFERENCES `material_cost_center_assignments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `material_cost_center_assignment_items_ibfk_2` FOREIGN KEY (`stock_id`) REFERENCES `material_stocks` (`stock_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `material_disposals`
--
ALTER TABLE `material_disposals`
  ADD CONSTRAINT `material_disposals_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_disposals_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_disposals_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `material_disposal_items`
--
ALTER TABLE `material_disposal_items`
  ADD CONSTRAINT `material_disposal_items_ibfk_1` FOREIGN KEY (`disposal_id`) REFERENCES `material_disposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `material_disposal_items_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_disposal_items_ibfk_3` FOREIGN KEY (`sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_disposal_items_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `material_items`
--
ALTER TABLE `material_items`
  ADD CONSTRAINT `material_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `material_item_categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `material_items_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE;

--
-- Constraints for table `material_item_categories`
--
ALTER TABLE `material_item_categories`
  ADD CONSTRAINT `material_item_categories_ibfk_1` FOREIGN KEY (`parent_category_id`) REFERENCES `material_item_categories` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_item_categories_ibfk_2` FOREIGN KEY (`project_nature_id`) REFERENCES `project_categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `material_opening_stocks`
--
ALTER TABLE `material_opening_stocks`
  ADD CONSTRAINT `material_opening_stocks_ibfk_1` FOREIGN KEY (`stock_id`) REFERENCES `material_stocks` (`stock_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_opening_stocks_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_opening_stocks_ibfk_3` FOREIGN KEY (`sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_opening_stocks_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `material_stocks`
--
ALTER TABLE `material_stocks`
  ADD CONSTRAINT `material_stocks_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_stocks_ibfk_2` FOREIGN KEY (`sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `material_stocks_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `material_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `material_stocks_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `miscellaneous_budgets`
--
ALTER TABLE `miscellaneous_budgets`
  ADD CONSTRAINT `miscellaneous_budgets_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `miscellaneous_budgets_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `miscellaneous_budgets_ibfk_3` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `miscellaneous_budgets_ibfk_4` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ordered_pre_orders`
--
ALTER TABLE `ordered_pre_orders`
  ADD CONSTRAINT `ordered_pre_orders_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ordered_pre_orders_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `outgoing_invoices`
--
ALTER TABLE `outgoing_invoices`
  ADD CONSTRAINT `curency_as_fk` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_to_fk` FOREIGN KEY (`invoice_to`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `outgoing_invoice_items`
--
ALTER TABLE `outgoing_invoice_items`
  ADD CONSTRAINT `measurement_fk` FOREIGN KEY (`measurement_unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `outgoing_invoice_fk` FOREIGN KEY (`outgoing_invoice_id`) REFERENCES `outgoing_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `outgoing_invoice_items_ibfk_1` FOREIGN KEY (`maintenance_service_item_id`) REFERENCES `maintenance_service_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `outgoing_invoice_items_ibfk_2` FOREIGN KEY (`project_certificate_id`) REFERENCES `project_certificates` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `outgoing_invoice_items_ibfk_3` FOREIGN KEY (`stock_sale_asset_item_id`) REFERENCES `stock_sales_asset_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `outgoing_invoice_items_ibfk_4` FOREIGN KEY (`stock_sale_material_item_id`) REFERENCES `stock_sales_material_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `owned_equipment_costs`
--
ALTER TABLE `owned_equipment_costs`
  ADD CONSTRAINT `owned_equipment_costs_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `owned_equipment_costs_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`),
  ADD CONSTRAINT `owned_equipment_costs_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `owned_equipment_costs_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`);

--
-- Constraints for table `parameter_types`
--
ALTER TABLE `parameter_types`
  ADD CONSTRAINT `parameter_types_ibfk_1` FOREIGN KEY (`category_parameter_id`) REFERENCES `category_parameters` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment_request_approval_journal_vouchers`
--
ALTER TABLE `payment_request_approval_journal_vouchers`
  ADD CONSTRAINT `junction_journal_voucher_fk` FOREIGN KEY (`journal_voucher_id`) REFERENCES `journal_vouchers` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_request_approval_journal_vouchers_ibfk_1` FOREIGN KEY (`purchase_order_payment_request_approval_id`) REFERENCES `purchase_order_payment_request_approvals` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  ADD CONSTRAINT `payment_vouchers_ibfk_1` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_vouchers_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_vouchers_ibfk_3` FOREIGN KEY (`is_printed`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `payment_voucher_credit_accounts`
--
ALTER TABLE `payment_voucher_credit_accounts`
  ADD CONSTRAINT `payment_voucher_credit_accounts_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_credit_accounts_ibfk_2` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_credit_accounts_ibfk_3` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `payment_voucher_grns`
--
ALTER TABLE `payment_voucher_grns`
  ADD CONSTRAINT `payment_voucher_grns_ibfk_1` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_grns_ibfk_2` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment_voucher_items`
--
ALTER TABLE `payment_voucher_items`
  ADD CONSTRAINT `payment_voucher_items_ibfk_1` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_items_ibfk_2` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_items_ibfk_3` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_items_ibfk_4` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `payment_voucher_item_approved_cash_request_items`
--
ALTER TABLE `payment_voucher_item_approved_cash_request_items`
  ADD CONSTRAINT `payment_voucher_item_approved_cash_request_items_ibfk_1` FOREIGN KEY (`payment_voucher_item_id`) REFERENCES `payment_voucher_items` (`payment_voucher_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_item_approved_cash_request_items_ibfk_2` FOREIGN KEY (`requisition_approval_asset_item_id`) REFERENCES `requisition_approval_asset_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_item_approved_cash_request_items_ibfk_3` FOREIGN KEY (`requisition_approval_cash_item_id`) REFERENCES `requisition_approval_cash_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_item_approved_cash_request_items_ibfk_4` FOREIGN KEY (`requisition_approval_material_item_id`) REFERENCES `requisition_approval_material_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_item_approved_cash_request_items_ibfk_5` FOREIGN KEY (`requisition_approval_service_item_id`) REFERENCES `requisition_approval_service_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment_voucher_item_approved_invoice_items`
--
ALTER TABLE `payment_voucher_item_approved_invoice_items`
  ADD CONSTRAINT `payment_voucher_item_approved_invoice_items_ibfk_1` FOREIGN KEY (`payment_voucher_item_id`) REFERENCES `payment_voucher_items` (`payment_voucher_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_item_approved_invoice_items_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_approval_invoice_item_id`) REFERENCES `purchase_order_payment_request_approval_invoice_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment_voucher_item_approved_sub_contract_requisition_items`
--
ALTER TABLE `payment_voucher_item_approved_sub_contract_requisition_items`
  ADD CONSTRAINT `payment_voucher_item_approved_items_ibfk_1` FOREIGN KEY (`payment_voucher_item_id`) REFERENCES `payment_voucher_items` (`payment_voucher_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_voucher_item_approved_items_ibfk_2` FOREIGN KEY (`sub_contract_payment_requisition_approval_item_id`) REFERENCES `sub_contract_payment_requisition_approval_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `approval_module_fk` FOREIGN KEY (`approval_module_id`) REFERENCES `approval_modules` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `approved_by` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `department_id_fk` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll_approvals`
--
ALTER TABLE `payroll_approvals`
  ADD CONSTRAINT `approval_chain_fk` FOREIGN KEY (`approval_chain_level_id`) REFERENCES `approval_chain_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `approval_creator_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `approval_payroll_fk` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll_employee_allowances`
--
ALTER TABLE `payroll_employee_allowances`
  ADD CONSTRAINT `employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll_employee_basic_info`
--
ALTER TABLE `payroll_employee_basic_info`
  ADD CONSTRAINT `basic_info_payroll_id_fk` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_employee_id_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll_employer_deductions`
--
ALTER TABLE `payroll_employer_deductions`
  ADD CONSTRAINT `employee_ded_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_deduct_fk` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll_journal_vouchers`
--
ALTER TABLE `payroll_journal_vouchers`
  ADD CONSTRAINT `payroll_journal_vouchers_ibfk_1` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payroll_journal_vouchers_ibfk_2` FOREIGN KEY (`journal_voucher_id`) REFERENCES `journal_vouchers` (`journal_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll_payments`
--
ALTER TABLE `payroll_payments`
  ADD CONSTRAINT `payroll_fk_id` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `payroll_payment_vouchers`
--
ALTER TABLE `payroll_payment_vouchers`
  ADD CONSTRAINT `aliyetengeneza_id_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_payment_voucher_fk` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `payment_payroll_fk` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `permanent_labour_budgets`
--
ALTER TABLE `permanent_labour_budgets`
  ADD CONSTRAINT `permanent_labour_budgets_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `permanent_labour_budgets_ibfk_2` FOREIGN KEY (`job_position_id`) REFERENCES `job_positions` (`job_position_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `permanent_labour_budgets_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permanent_labour_budgets_ibfk_4` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `permanent_labour_costs`
--
ALTER TABLE `permanent_labour_costs`
  ADD CONSTRAINT `permanent_labour_costs_ibfk_1` FOREIGN KEY (`project_team_member_id`) REFERENCES `project_team_members` (`member_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `permanent_labour_costs_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `permanent_labour_costs_ibfk_4` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `permission_privileges`
--
ALTER TABLE `permission_privileges`
  ADD CONSTRAINT `permission_privileges_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `permissions` (`permission_id`) ON UPDATE CASCADE;

--
-- Constraints for table `procurement_attachments`
--
ALTER TABLE `procurement_attachments`
  ADD CONSTRAINT `procurement_attachments_ibfk_1` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `project_categories` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `projects_ibfk_6` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_accounts`
--
ALTER TABLE `project_accounts`
  ADD CONSTRAINT `project_accounts_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_accounts_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_attachments`
--
ALTER TABLE `project_attachments`
  ADD CONSTRAINT `project_attachments_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_attachments_ibfk_2` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_certificates`
--
ALTER TABLE `project_certificates`
  ADD CONSTRAINT `project_certificates_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_certificates_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_certificates_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_certificate_invoices`
--
ALTER TABLE `project_certificate_invoices`
  ADD CONSTRAINT `project_certificate_invoices_ibfk_1` FOREIGN KEY (`outgoing_invoice_id`) REFERENCES `outgoing_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_certificate_invoices_ibfk_2` FOREIGN KEY (`project_certificate_id`) REFERENCES `project_certificates` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_certificate_receipts`
--
ALTER TABLE `project_certificate_receipts`
  ADD CONSTRAINT `project_certificate_receipts_ibfk_1` FOREIGN KEY (`certificate_id`) REFERENCES `project_certificates` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_certificate_receipts_ibfk_2` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_closures`
--
ALTER TABLE `project_closures`
  ADD CONSTRAINT `project_closures_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_closures_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_contract_reviews`
--
ALTER TABLE `project_contract_reviews`
  ADD CONSTRAINT `project_contract_reviews_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_contract_reviews_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_imprest_voucher_items`
--
ALTER TABLE `project_imprest_voucher_items`
  ADD CONSTRAINT `impest_cash_fk` FOREIGN KEY (`imprest_voucher_cash_item_id`) REFERENCES `imprest_voucher_cash_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `p_imprest_service_fk` FOREIGN KEY (`imprest_voucher_service_item_id`) REFERENCES `imprest_voucher_service_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_payment_voucher_items`
--
ALTER TABLE `project_payment_voucher_items`
  ADD CONSTRAINT `project_payment_voucher_items_ibfk_1` FOREIGN KEY (`payment_voucher_item_id`) REFERENCES `payment_voucher_items` (`payment_voucher_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_payment_voucher_items_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_plans`
--
ALTER TABLE `project_plans`
  ADD CONSTRAINT `project_plans_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plans_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plans_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `project_plan_tasks`
--
ALTER TABLE `project_plan_tasks`
  ADD CONSTRAINT `project_plan_tasks_ibfk_1` FOREIGN KEY (`project_plan_id`) REFERENCES `project_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_tasks_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_tasks_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_plan_task_casual_labour_budgets`
--
ALTER TABLE `project_plan_task_casual_labour_budgets`
  ADD CONSTRAINT `project_plan_task_casual_labour_budgets_ibfk_1` FOREIGN KEY (`casual_labour_type_id`) REFERENCES `casual_labour_types` (`type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_casual_labour_budgets_ibfk_2` FOREIGN KEY (`project_plan_task_id`) REFERENCES `project_plan_tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_casual_labour_budgets_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_plan_task_equipment_budgets`
--
ALTER TABLE `project_plan_task_equipment_budgets`
  ADD CONSTRAINT `project_plan_task_equipment_budgets_ibfk_1` FOREIGN KEY (`project_plan_task_id`) REFERENCES `project_plan_tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_equipment_budgets_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_equipment_budgets_ibfk_3` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_plan_task_executions`
--
ALTER TABLE `project_plan_task_executions`
  ADD CONSTRAINT `project_plan_task_executions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_executions_ibfk_2` FOREIGN KEY (`project_plan_id`) REFERENCES `project_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_executions_ibfk_3` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_plan_task_execution_casual_labour`
--
ALTER TABLE `project_plan_task_execution_casual_labour`
  ADD CONSTRAINT `project_plan_task_execution_casual_labour_ibfk_1` FOREIGN KEY (`casual_labour_type_id`) REFERENCES `casual_labour_types` (`type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_execution_casual_labour_ibfk_2` FOREIGN KEY (`plan_task_execution_id`) REFERENCES `project_plan_task_executions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_plan_task_execution_equipments`
--
ALTER TABLE `project_plan_task_execution_equipments`
  ADD CONSTRAINT `project_plan_task_execution_equipments_ibfk_1` FOREIGN KEY (`plan_task_execution_id`) REFERENCES `project_plan_task_executions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_execution_equipments_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_plan_task_execution_material_costs`
--
ALTER TABLE `project_plan_task_execution_material_costs`
  ADD CONSTRAINT `project_plan_task_execution_material_costs_ibfk_2` FOREIGN KEY (`material_cost_id`) REFERENCES `material_costs` (`material_cost_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_execution_material_costs_ibfk_3` FOREIGN KEY (`plan_task_execution_id`) REFERENCES `project_plan_task_executions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_plan_task_material_budgets`
--
ALTER TABLE `project_plan_task_material_budgets`
  ADD CONSTRAINT `project_plan_task_material_budgets_ibfk_1` FOREIGN KEY (`project_plan_task_id`) REFERENCES `project_plan_tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_plan_task_material_budgets_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE;

--
-- Constraints for table `project_purchase_orders`
--
ALTER TABLE `project_purchase_orders`
  ADD CONSTRAINT `project_purchase_orders_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_purchase_orders_ibfk_2` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_requisitions`
--
ALTER TABLE `project_requisitions`
  ADD CONSTRAINT `project_requisitions_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_requisitions_ibfk_2` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_special_budgets`
--
ALTER TABLE `project_special_budgets`
  ADD CONSTRAINT `project_special_budgets_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_special_budgets_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `project_team_members`
--
ALTER TABLE `project_team_members`
  ADD CONSTRAINT `project_team_members_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `project_team_members_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_team_members_ibfk_3` FOREIGN KEY (`assignor_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `project_team_members_ibfk_4` FOREIGN KEY (`job_position_id`) REFERENCES `job_positions` (`job_position_id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_orders_ibfk_3` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_orders_ibfk_6` FOREIGN KEY (`handler_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_orders_ibfk_7` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_orders_ibfk_8` FOREIGN KEY (`is_printed`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_asset_items`
--
ALTER TABLE `purchase_order_asset_items`
  ADD CONSTRAINT `purchase_order_asset_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_asset_items_ibfk_2` FOREIGN KEY (`asset_item_id`) REFERENCES `asset_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_grns`
--
ALTER TABLE `purchase_order_grns`
  ADD CONSTRAINT `purchase_order_grns_ibfk_1` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`grn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_grns_ibfk_2` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_invoices`
--
ALTER TABLE `purchase_order_invoices`
  ADD CONSTRAINT `purchase_order_invoices_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_invoices_ibfk_2` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_material_items`
--
ALTER TABLE `purchase_order_material_items`
  ADD CONSTRAINT `purchase_order_material_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_material_items_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_material_item_grn_items`
--
ALTER TABLE `purchase_order_material_item_grn_items`
  ADD CONSTRAINT `purchase_order_material_item_grn_items_ibfk_1` FOREIGN KEY (`goods_received_note_item_id`) REFERENCES `goods_received_note_material_stock_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_material_item_grn_items_ibfk_2` FOREIGN KEY (`purchase_order_material_item_id`) REFERENCES `purchase_order_material_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_payment_requests`
--
ALTER TABLE `purchase_order_payment_requests`
  ADD CONSTRAINT `purchase_order_payment_requests_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_requests_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_requests_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_requests_ibfk_4` FOREIGN KEY (`approval_module_id`) REFERENCES `approval_modules` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_requests_ibfk_5` FOREIGN KEY (`forward_to`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_payment_request_approvals`
--
ALTER TABLE `purchase_order_payment_request_approvals`
  ADD CONSTRAINT `purchase_order_payment_request_approvals_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_approvals_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_id`) REFERENCES `purchase_order_payment_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_approvals_ibfk_3` FOREIGN KEY (`approval_chain_level_id`) REFERENCES `approval_chain_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_approvals_ibfk_4` FOREIGN KEY (`forward_to`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_approvals_ibfk_5` FOREIGN KEY (`is_printed`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_payment_request_approval_cash_items`
--
ALTER TABLE `purchase_order_payment_request_approval_cash_items`
  ADD CONSTRAINT `purchase_order_payment_request_approval_cash_items_ibfk_1` FOREIGN KEY (`purchase_order_payment_request_approval_id`) REFERENCES `purchase_order_payment_request_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_approval_cash_items_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_cash_item_id`) REFERENCES `purchase_order_payment_request_cash_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_payment_request_approval_invoice_items`
--
ALTER TABLE `purchase_order_payment_request_approval_invoice_items`
  ADD CONSTRAINT `purchase_order_payment_request_approval_invoice_items_ibfk_1` FOREIGN KEY (`purchase_order_payment_request_approval_id`) REFERENCES `purchase_order_payment_request_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_approval_invoice_items_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_invoice_item_id`) REFERENCES `purchase_order_payment_request_invoice_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_payment_request_approval_payment_vouchers`
--
ALTER TABLE `purchase_order_payment_request_approval_payment_vouchers`
  ADD CONSTRAINT `purchase_order_payment_request_approval_payment_vouchers_ibfk_1` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_approval_payment_vouchers_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_approval_id`) REFERENCES `purchase_order_payment_request_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_payment_request_attachments`
--
ALTER TABLE `purchase_order_payment_request_attachments`
  ADD CONSTRAINT `purchase_order_payment_request_attachments_ibfk_1` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_attachments_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_id`) REFERENCES `purchase_order_payment_requests` (`id`);

--
-- Constraints for table `purchase_order_payment_request_cash_items`
--
ALTER TABLE `purchase_order_payment_request_cash_items`
  ADD CONSTRAINT `purchase_order_payment_request_cash_items_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_id`) REFERENCES `purchase_order_payment_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_payment_request_invoice_items`
--
ALTER TABLE `purchase_order_payment_request_invoice_items`
  ADD CONSTRAINT `purchase_order_payment_request_invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_payment_request_invoice_items_ibfk_2` FOREIGN KEY (`purchase_order_payment_request_id`) REFERENCES `purchase_order_payment_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_order_service_items`
--
ALTER TABLE `purchase_order_service_items`
  ADD CONSTRAINT `purchase_order_service_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `purchase_order_service_items_ibfk_2` FOREIGN KEY (`measurement_unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE;

--
-- Constraints for table `receipts`
--
ALTER TABLE `receipts`
  ADD CONSTRAINT `receipts_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `receipts_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `receipts_ibfk_3` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `receipts_ibfk_4` FOREIGN KEY (`credit_account_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `receipts_ibfk_5` FOREIGN KEY (`invoice_id`) REFERENCES `outgoing_invoices` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `receipt_items`
--
ALTER TABLE `receipt_items`
  ADD CONSTRAINT `receipt_items_ibfk_2` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `registered_certificates`
--
ALTER TABLE `registered_certificates`
  ADD CONSTRAINT `registered_certificates_ibfk_1` FOREIGN KEY (`hse_certificate_id`) REFERENCES `hse_certificates` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rejected_payrolls`
--
ALTER TABLE `rejected_payrolls`
  ADD CONSTRAINT `current_level_id` FOREIGN KEY (`current_level`) REFERENCES `approval_chain_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `reject_payroll_id` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rejector_fk` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisitions`
--
ALTER TABLE `requisitions`
  ADD CONSTRAINT `requisitions_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisitions_ibfk_3` FOREIGN KEY (`approval_module_id`) REFERENCES `approval_modules` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisitions_ibfk_4` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisitions_ibfk_5` FOREIGN KEY (`finalizer_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisitions_ibfk_6` FOREIGN KEY (`foward_to`) REFERENCES `employees` (`employee_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `requisition_approvals`
--
ALTER TABLE `requisition_approvals`
  ADD CONSTRAINT `requisition_approvals_ibfk_1` FOREIGN KEY (`approval_chain_level_id`) REFERENCES `approval_chain_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approvals_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approvals_ibfk_3` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approvals_ibfk_4` FOREIGN KEY (`returned_chain_level_id`) REFERENCES `approval_chain_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approvals_ibfk_5` FOREIGN KEY (`is_printed`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approvals_ibfk_6` FOREIGN KEY (`forward_to`) REFERENCES `employees` (`employee_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `requisition_approval_asset_items`
--
ALTER TABLE `requisition_approval_asset_items`
  ADD CONSTRAINT `requisition_approval_asset_items_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_asset_items_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_asset_items_ibfk_4` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_asset_items_ibfk_5` FOREIGN KEY (`vendor_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_asset_items_ibfk_6` FOREIGN KEY (`requisition_asset_item_id`) REFERENCES `requisition_asset_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_asset_items_ibfk_7` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`);

--
-- Constraints for table `requisition_approval_cash_items`
--
ALTER TABLE `requisition_approval_cash_items`
  ADD CONSTRAINT `requisition_approval_cash_items_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_cash_items_ibfk_2` FOREIGN KEY (`requisition_cash_item_id`) REFERENCES `requisition_cash_items` (`id`),
  ADD CONSTRAINT `requisition_approval_cash_items_ibfk_3` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `requisition_approval_cash_item_expense_accounts`
--
ALTER TABLE `requisition_approval_cash_item_expense_accounts`
  ADD CONSTRAINT `requisition_approval_cash_item_expense_accounts_ibfk_1` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_cash_item_expense_accounts_ibfk_2` FOREIGN KEY (`requisition_cash_item_id`) REFERENCES `requisition_cash_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_cash_item_expense_accounts_ibfk_3` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisition_approval_imprest_vouchers`
--
ALTER TABLE `requisition_approval_imprest_vouchers`
  ADD CONSTRAINT `requisition_approval_imprest_vouchers_ibfk_1` FOREIGN KEY (`imprest_voucher_id`) REFERENCES `imprest_vouchers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `requisition_approval_imprest_vouchers_ibfk_2` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`);

--
-- Constraints for table `requisition_approval_material_items`
--
ALTER TABLE `requisition_approval_material_items`
  ADD CONSTRAINT `requisition_approval_material_items_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_material_items_ibfk_2` FOREIGN KEY (`requisition_material_item_id`) REFERENCES `requisition_material_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_material_items_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_material_items_ibfk_4` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_material_items_ibfk_5` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisition_approval_material_item_expense_accounts`
--
ALTER TABLE `requisition_approval_material_item_expense_accounts`
  ADD CONSTRAINT `requisition_approval_material_item_expense_accounts_ibfk_1` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_material_item_expense_accounts_ibfk_2` FOREIGN KEY (`requisition_material_item_id`) REFERENCES `requisition_material_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_material_item_expense_accounts_ibfk_3` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisition_approval_payment_vouchers`
--
ALTER TABLE `requisition_approval_payment_vouchers`
  ADD CONSTRAINT `requisition_approval_payment_vouchers_ibfk_1` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_payment_vouchers_ibfk_2` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `requisition_approval_service_items`
--
ALTER TABLE `requisition_approval_service_items`
  ADD CONSTRAINT `requisition_approval_service_items_ibfk_1` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_service_items_ibfk_2` FOREIGN KEY (`requisition_service_item_id`) REFERENCES `requisition_service_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_service_items_ibfk_3` FOREIGN KEY (`vendor_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_approval_service_items_ibfk_4` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisition_asset_items`
--
ALTER TABLE `requisition_asset_items`
  ADD CONSTRAINT `requisition_asset_items_ibfk_1` FOREIGN KEY (`asset_item_id`) REFERENCES `asset_items` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_asset_items_ibfk_2` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_asset_items_ibfk_3` FOREIGN KEY (`requested_currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_asset_items_ibfk_4` FOREIGN KEY (`requested_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisition_attachments`
--
ALTER TABLE `requisition_attachments`
  ADD CONSTRAINT `requisition_attachments_ibfk_1` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_attachments_ibfk_2` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `requisition_cash_items`
--
ALTER TABLE `requisition_cash_items`
  ADD CONSTRAINT `requisition_cash_items_ibfk_1` FOREIGN KEY (`measurement_unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_cash_items_ibfk_2` FOREIGN KEY (`requested_currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_cash_items_ibfk_3` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_cash_items_ibfk_4` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_cash_items_ibfk_5` FOREIGN KEY (`requested_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisition_cash_item_tasks`
--
ALTER TABLE `requisition_cash_item_tasks`
  ADD CONSTRAINT `requisition_cash_item_tasks_ibfk_1` FOREIGN KEY (`requisition_item_id`) REFERENCES `requisition_cash_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_cash_item_tasks_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `requisition_equipment_items`
--
ALTER TABLE `requisition_equipment_items`
  ADD CONSTRAINT `requisition_equipment_items_ibfk_1` FOREIGN KEY (`asset_group_id`) REFERENCES `asset_groups` (`id`);

--
-- Constraints for table `requisition_equipment_item_tasks`
--
ALTER TABLE `requisition_equipment_item_tasks`
  ADD CONSTRAINT `requisition_equipment_item_tasks_ibfk_1` FOREIGN KEY (`requisition_item_id`) REFERENCES `requisition_equipment_items` (`id`),
  ADD CONSTRAINT `requisition_equipment_item_tasks_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`);

--
-- Constraints for table `requisition_material_items`
--
ALTER TABLE `requisition_material_items`
  ADD CONSTRAINT `requisition_material_items_ibfk_1` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_material_items_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_material_items_ibfk_4` FOREIGN KEY (`requested_vendor_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_material_items_ibfk_6` FOREIGN KEY (`requested_currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_material_items_ibfk_7` FOREIGN KEY (`expense_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_material_items_ibfk_8` FOREIGN KEY (`requested_location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_material_items_ibfk_9` FOREIGN KEY (`requested_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisition_material_item_tasks`
--
ALTER TABLE `requisition_material_item_tasks`
  ADD CONSTRAINT `requisition_material_item_tasks_ibfk_1` FOREIGN KEY (`requisition_item_id`) REFERENCES `requisition_material_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_material_item_tasks_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `requisition_purchase_orders`
--
ALTER TABLE `requisition_purchase_orders`
  ADD CONSTRAINT `requisition_purchase_orders_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_purchase_orders_ibfk_2` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON UPDATE CASCADE;

--
-- Constraints for table `requisition_service_items`
--
ALTER TABLE `requisition_service_items`
  ADD CONSTRAINT `requisition_service_items_ibfk_1` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_service_items_ibfk_2` FOREIGN KEY (`measurement_unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_service_items_ibfk_3` FOREIGN KEY (`requested_vendor_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `requisition_service_items_ibfk_4` FOREIGN KEY (`requested_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE;

--
-- Constraints for table `revised_tasks`
--
ALTER TABLE `revised_tasks`
  ADD CONSTRAINT `revised_tasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`),
  ADD CONSTRAINT `revised_tasks_ibfk_2` FOREIGN KEY (`revision_id`) REFERENCES `revision` (`id`);

--
-- Constraints for table `revision`
--
ALTER TABLE `revision`
  ADD CONSTRAINT `revision_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`),
  ADD CONSTRAINT `revision_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `site_diary_compliances`
--
ALTER TABLE `site_diary_compliances`
  ADD CONSTRAINT `site_diary_compliances_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `site_diary_compliances_ibfk_2` FOREIGN KEY (`site_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `site_diary_compliances_ibfk_3` FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `site_diary_compliance_statuses`
--
ALTER TABLE `site_diary_compliance_statuses`
  ADD CONSTRAINT `site_diary_compliance_statuses_ibfk_1` FOREIGN KEY (`site_diary_id`) REFERENCES `site_diary_compliances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stakeholders`
--
ALTER TABLE `stakeholders`
  ADD CONSTRAINT `stakeholders_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stakeholders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `stakeholder_evaluation_scores`
--
ALTER TABLE `stakeholder_evaluation_scores`
  ADD CONSTRAINT `stakeholder_evaluation_scores_ibfk_1` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stakeholder_evaluation_scores_ibfk_2` FOREIGN KEY (`stakeholder_evaluation_factor_id`) REFERENCES `stakeholder_evaluation_factors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stakeholder_invoices`
--
ALTER TABLE `stakeholder_invoices`
  ADD CONSTRAINT `stakeholder_invoices_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stakeholder_invoices_ibfk_2` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_disposal_asset_items`
--
ALTER TABLE `stock_disposal_asset_items`
  ADD CONSTRAINT `stock_disposal_asset_items_ibfk_1` FOREIGN KEY (`disposal_id`) REFERENCES `material_disposals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_disposal_asset_items_ibfk_2` FOREIGN KEY (`asset_sub_location_history_id`) REFERENCES `asset_sub_location_histories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_sales`
--
ALTER TABLE `stock_sales`
  ADD CONSTRAINT `stock_sales_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sales_ibfk_2` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sales_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sales_ibfk_4` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sales_ibfk_5` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_sales_asset_items`
--
ALTER TABLE `stock_sales_asset_items`
  ADD CONSTRAINT `stock_sales_asset_items_ibfk_1` FOREIGN KEY (`asset_sub_location_history_id`) REFERENCES `asset_sub_location_histories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sales_asset_items_ibfk_2` FOREIGN KEY (`stock_sale_id`) REFERENCES `stock_sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stock_sales_material_items`
--
ALTER TABLE `stock_sales_material_items`
  ADD CONSTRAINT `stock_sales_material_items_ibfk_1` FOREIGN KEY (`stock_sale_id`) REFERENCES `stock_sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sales_material_items_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sales_material_items_ibfk_3` FOREIGN KEY (`source_sub_location_id`) REFERENCES `sub_locations` (`sub_location_id`);

--
-- Constraints for table `stock_sale_invoices`
--
ALTER TABLE `stock_sale_invoices`
  ADD CONSTRAINT `stock_sale_invoices_ibfk_1` FOREIGN KEY (`stock_sale_id`) REFERENCES `stock_sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sale_invoices_ibfk_2` FOREIGN KEY (`outgoing_invoice_id`) REFERENCES `outgoing_invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stock_sale_receipts`
--
ALTER TABLE `stock_sale_receipts`
  ADD CONSTRAINT `stock_sale_receipts_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `receipts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_sale_receipts_ibfk_2` FOREIGN KEY (`stock_sale_id`) REFERENCES `stock_sales` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD CONSTRAINT `subtasks_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `subtasks_ibfk_2` FOREIGN KEY (`measurement_unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contracts`
--
ALTER TABLE `sub_contracts`
  ADD CONSTRAINT `sub_contracts_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contracts_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contracts_ibfk_3` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `sub_contracts_items`
--
ALTER TABLE `sub_contracts_items`
  ADD CONSTRAINT `sub_contracts_items_ibfk_1` FOREIGN KEY (`sub_contract_id`) REFERENCES `sub_contracts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contracts_items_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_budgets`
--
ALTER TABLE `sub_contract_budgets`
  ADD CONSTRAINT `sub_contract_budgets_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_budgets_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_budgets_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_certificates`
--
ALTER TABLE `sub_contract_certificates`
  ADD CONSTRAINT `sub_contract_certificates_ibfk_1` FOREIGN KEY (`sub_contract_id`) REFERENCES `sub_contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_certificates_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_certificate_payment_vouchers`
--
ALTER TABLE `sub_contract_certificate_payment_vouchers`
  ADD CONSTRAINT `sub_contract_certificate_payment_vouchers_ibfk_1` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_certificate_payment_vouchers_ibfk_2` FOREIGN KEY (`sub_contract_certificate_id`) REFERENCES `sub_contract_certificates` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_certificate_tasks`
--
ALTER TABLE `sub_contract_certificate_tasks`
  ADD CONSTRAINT `sub_contract_certificate_tasks_ibfk_1` FOREIGN KEY (`sub_contract_certificate_id`) REFERENCES `sub_contract_certificates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_certificate_tasks_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_payment_requisitions`
--
ALTER TABLE `sub_contract_payment_requisitions`
  ADD CONSTRAINT `sub_contract_payment_requisitions_ibfk_1` FOREIGN KEY (`approval_module_id`) REFERENCES `approval_modules` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisitions_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisitions_ibfk_3` FOREIGN KEY (`finalizer_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisitions_ibfk_4` FOREIGN KEY (`foward_to`) REFERENCES `employees` (`employee_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisitions_ibfk_5` FOREIGN KEY (`requester_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_payment_requisition_approvals`
--
ALTER TABLE `sub_contract_payment_requisition_approvals`
  ADD CONSTRAINT `sub_contract_payment_requisition_approvals_ibfk_1` FOREIGN KEY (`approval_chain_level_id`) REFERENCES `approval_chain_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisition_approvals_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisition_approvals_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisition_approvals_ibfk_4` FOREIGN KEY (`returned_chain_level_id`) REFERENCES `approval_chain_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisition_approvals_ibfk_5` FOREIGN KEY (`sub_contract_requisition_id`) REFERENCES `sub_contract_payment_requisitions` (`sub_contract_requisition_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisition_approvals_ibfk_6` FOREIGN KEY (`forward_to`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_payment_requisition_approval_items`
--
ALTER TABLE `sub_contract_payment_requisition_approval_items`
  ADD CONSTRAINT `sub_contract_payment_requisition_approval_items_ibfk_1` FOREIGN KEY (`sub_contract_payment_requisition_approval_id`) REFERENCES `sub_contract_payment_requisition_approvals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisition_approval_items_ibfk_2` FOREIGN KEY (`sub_contract_payment_requisition_item_id`) REFERENCES `sub_contract_payment_requisition_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_payment_requisition_approval_journal_vouchers`
--
ALTER TABLE `sub_contract_payment_requisition_approval_journal_vouchers`
  ADD CONSTRAINT `sc_payment_req_approval_journal_vouchers_ibfk_1` FOREIGN KEY (`journal_voucher_id`) REFERENCES `journal_vouchers` (`journal_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sc_payment_req_approval_journal_vouchers_ibfk_2` FOREIGN KEY (`sub_contract_payment_requisition_approval_id`) REFERENCES `sub_contract_payment_requisition_approvals` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_payment_requisition_approval_payment_vouchers`
--
ALTER TABLE `sub_contract_payment_requisition_approval_payment_vouchers`
  ADD CONSTRAINT `sc_payment_requisition_approval_pvs_ibfk_1` FOREIGN KEY (`payment_voucher_id`) REFERENCES `payment_vouchers` (`payment_voucher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sc_payment_requisition_approval_pvs_ibfk_2` FOREIGN KEY (`sub_contract_payment_requisition_approval_id`) REFERENCES `sub_contract_payment_requisition_approvals` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_payment_requisition_attachments`
--
ALTER TABLE `sub_contract_payment_requisition_attachments`
  ADD CONSTRAINT `sub_contract_payment_requisition_attachments_ibfk_1` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisition_attachments_ibfk_2` FOREIGN KEY (`sub_contract_payment_requisition_id`) REFERENCES `sub_contract_payment_requisitions` (`sub_contract_requisition_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_contract_payment_requisition_items`
--
ALTER TABLE `sub_contract_payment_requisition_items`
  ADD CONSTRAINT `sub_contract_payment_requisition_items_ibfk_1` FOREIGN KEY (`sub_contract_requisition_id`) REFERENCES `sub_contract_payment_requisitions` (`sub_contract_requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_contract_payment_requisition_items_ibfk_2` FOREIGN KEY (`certificate_id`) REFERENCES `sub_contract_certificates` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_locations`
--
ALTER TABLE `sub_locations`
  ADD CONSTRAINT `sub_locations_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `system_logs_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `system_logs_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`measurement_unit_id`) REFERENCES `measurement_units` (`unit_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`predecessor`) REFERENCES `tasks` (`task_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `task_payment_voucher_items`
--
ALTER TABLE `task_payment_voucher_items`
  ADD CONSTRAINT `task_payment_voucher_items_ibfk_1` FOREIGN KEY (`payment_voucher_item_id`) REFERENCES `payment_voucher_items` (`payment_voucher_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_payment_voucher_items_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON UPDATE CASCADE;

--
-- Constraints for table `task_progress_updates`
--
ALTER TABLE `task_progress_updates`
  ADD CONSTRAINT `task_progress_updates_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`task_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tax_table_items`
--
ALTER TABLE `tax_table_items`
  ADD CONSTRAINT `tax_table_id` FOREIGN KEY (`tax_table_id`) REFERENCES `tax_tables` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tenders`
--
ALTER TABLE `tenders`
  ADD CONSTRAINT `tenders_ibfk_1` FOREIGN KEY (`procurement_currency_id`) REFERENCES `currencies` (`currency_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tenders_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tenders_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tenders_ibfk_4` FOREIGN KEY (`project_category_id`) REFERENCES `project_categories` (`category_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tenders_ibfk_5` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tender_attachments`
--
ALTER TABLE `tender_attachments`
  ADD CONSTRAINT `tender_attachments_ibfk_1` FOREIGN KEY (`attachment_id`) REFERENCES `attachments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_attachments_ibfk_2` FOREIGN KEY (`tender_id`) REFERENCES `tenders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tender_awards`
--
ALTER TABLE `tender_awards`
  ADD CONSTRAINT `tender_awards_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_awards_ibfk_2` FOREIGN KEY (`tender_id`) REFERENCES `tenders` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_awards_ibfk_3` FOREIGN KEY (`awarded_contractor_id`) REFERENCES `contractors` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tender_components`
--
ALTER TABLE `tender_components`
  ADD CONSTRAINT `tender_components_ibfk_1` FOREIGN KEY (`tender_id`) REFERENCES `tenders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_components_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tender_component_lumpsum_prices`
--
ALTER TABLE `tender_component_lumpsum_prices`
  ADD CONSTRAINT `tender_component_lumpsum_prices_ibfk_1` FOREIGN KEY (`tender_component_id`) REFERENCES `tender_components` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_component_lumpsum_prices_ibfk_2` FOREIGN KEY (`tender_lumpsum_price_id`) REFERENCES `tender_lumpsum_prices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tender_component_material_prices`
--
ALTER TABLE `tender_component_material_prices`
  ADD CONSTRAINT `tender_component_material_prices_ibfk_1` FOREIGN KEY (`tender_component_id`) REFERENCES `tender_components` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_component_material_prices_ibfk_2` FOREIGN KEY (`tender_material_price_id`) REFERENCES `tender_material_prices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tender_lumpsum_prices`
--
ALTER TABLE `tender_lumpsum_prices`
  ADD CONSTRAINT `tender_lumpsum_prices_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tender_material_prices`
--
ALTER TABLE `tender_material_prices`
  ADD CONSTRAINT `tender_material_prices_ibfk_1` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_material_prices_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tender_requirements`
--
ALTER TABLE `tender_requirements`
  ADD CONSTRAINT `tender_requirements_ibfk_1` FOREIGN KEY (`tender_requirement_type_id`) REFERENCES `tender_requirement_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_requirements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_requirements_ibfk_3` FOREIGN KEY (`tender_id`) REFERENCES `tenders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tender_requirement_types`
--
ALTER TABLE `tender_requirement_types`
  ADD CONSTRAINT `tender_requirement_types_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tender_sub_components`
--
ALTER TABLE `tender_sub_components`
  ADD CONSTRAINT `tender_sub_components_ibfk_1` FOREIGN KEY (`tender_component_id`) REFERENCES `tender_components` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_sub_components_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tender_sub_component_material_prices`
--
ALTER TABLE `tender_sub_component_material_prices`
  ADD CONSTRAINT `tender_sub_component_material_prices_ibfk_1` FOREIGN KEY (`tender_sub_component_id`) REFERENCES `tender_sub_component_material_prices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tender_sub_component_material_prices_ibfk_2` FOREIGN KEY (`tender_material_price_id`) REFERENCES `tender_material_prices` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `toolbox_talk_registers`
--
ALTER TABLE `toolbox_talk_registers`
  ADD CONSTRAINT `toolbox_talk_registers_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `toolbox_talk_registers_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `toolbox_talk_registers_ibfk_3` FOREIGN KEY (`site_id`) REFERENCES `projects` (`project_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `toolbox_talk_registers_ibfk_4` FOREIGN KEY (`supervisor_id`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `toolbox_talk_register_participants`
--
ALTER TABLE `toolbox_talk_register_participants`
  ADD CONSTRAINT `toolbox_talk_register_participants_ibfk_1` FOREIGN KEY (`toolbox_talk_register_id`) REFERENCES `toolbox_talk_registers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `toolbox_talk_register_topics`
--
ALTER TABLE `toolbox_talk_register_topics`
  ADD CONSTRAINT `toolbox_talk_register_topics_ibfk_1` FOREIGN KEY (`toolbox_talk_register_id`) REFERENCES `toolbox_talk_registers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `toolbox_talk_register_topics_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `site_topics` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `topics_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `topic_carbon_copies`
--
ALTER TABLE `topic_carbon_copies`
  ADD CONSTRAINT `topic_carbon_copies_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `topic_conversations`
--
ALTER TABLE `topic_conversations`
  ADD CONSTRAINT `topic_conversations_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `topic_conversations_ibfk_2` FOREIGN KEY (`recipient`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `topic_conversations_ibfk_3` FOREIGN KEY (`sender`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;

--
-- Constraints for table `topic_conversation_logs`
--
ALTER TABLE `topic_conversation_logs`
  ADD CONSTRAINT `topic_conversation_logs_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `topic_subjects`
--
ALTER TABLE `topic_subjects`
  ADD CONSTRAINT `topic_subjects_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transferred_transfer_orders`
--
ALTER TABLE `transferred_transfer_orders`
  ADD CONSTRAINT `transferred_transfer_orders_ibfk_1` FOREIGN KEY (`transfer_id`) REFERENCES `external_material_transfers` (`transfer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transferred_transfer_orders_ibfk_2` FOREIGN KEY (`requisition_approval_id`) REFERENCES `requisition_approvals` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `transfer_requisitions`
--
ALTER TABLE `transfer_requisitions`
  ADD CONSTRAINT `transfer_requisitions_ibfk_1` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transfer_requisitions_ibfk_2` FOREIGN KEY (`destination_location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE;

--
-- Constraints for table `transfer_requisition_assets`
--
ALTER TABLE `transfer_requisition_assets`
  ADD CONSTRAINT `transfer_requisition_assets_ibfk_1` FOREIGN KEY (`requisition_id`) REFERENCES `requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transfer_requisition_assets_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `unprocured_deliveries`
--
ALTER TABLE `unprocured_deliveries`
  ADD CONSTRAINT `unprocured_delivery_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `unprocured_delivery_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `clients` (`client_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `unprocured_delivery_ibfk_3` FOREIGN KEY (`delivery_for`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `unprocured_delivery_ibfk_4` FOREIGN KEY (`location_id`) REFERENCES `inventory_locations` (`location_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `unprocured_delivery_ibfk_5` FOREIGN KEY (`receiver_id`) REFERENCES `employees` (`employee_id`);

--
-- Constraints for table `unprocured_delivery_asset_items`
--
ALTER TABLE `unprocured_delivery_asset_items`
  ADD CONSTRAINT `unprocured_delivery_asset_items_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `unprocured_deliveries` (`delivery_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `unprocured_delivery_asset_items_ibfk_2` FOREIGN KEY (`asset_item_id`) REFERENCES `asset_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `unprocured_delivery_grns`
--
ALTER TABLE `unprocured_delivery_grns`
  ADD CONSTRAINT `unprocured_delivery_grns_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `unprocured_deliveries` (`delivery_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `unprocured_delivery_grns_ibfk_2` FOREIGN KEY (`grn_id`) REFERENCES `goods_received_notes` (`grn_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `unprocured_delivery_material_items`
--
ALTER TABLE `unprocured_delivery_material_items`
  ADD CONSTRAINT `unprocured_delivery_material_items_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `unprocured_deliveries` (`delivery_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `unprocured_delivery_material_items_ibfk_2` FOREIGN KEY (`material_item_id`) REFERENCES `material_items` (`item_id`) ON UPDATE CASCADE;

--
-- Constraints for table `unprocured_delivery_material_item_grn_items`
--
ALTER TABLE `unprocured_delivery_material_item_grn_items`
  ADD CONSTRAINT `unprocured_delivery_material_item_grn_items_ibfk_1` FOREIGN KEY (`grn_item_id`) REFERENCES `goods_received_note_material_stock_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `unprocured_delivery_material_item_grn_items_ibfk_2` FOREIGN KEY (`unprocured_delivery_material_item_id`) REFERENCES `unprocured_delivery_material_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`confidentiality_level_id`) REFERENCES `employee_confidentiality_levels` (`level_id`) ON UPDATE CASCADE;

--
-- Constraints for table `users_permissions`
--
ALTER TABLE `users_permissions`
  ADD CONSTRAINT `users_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_permissions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_permission_privileges`
--
ALTER TABLE `user_permission_privileges`
  ADD CONSTRAINT `user_permission_privileges_ibfk_1` FOREIGN KEY (`user_permission_id`) REFERENCES `users_permissions` (`user_permission_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_permission_privileges_ibfk_2` FOREIGN KEY (`permission_privilege_id`) REFERENCES `permission_privileges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `withholding_taxes`
--
ALTER TABLE `withholding_taxes`
  ADD CONSTRAINT `withholding_taxes_ibfk_1` FOREIGN KEY (`credit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `withholding_taxes_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `withholding_taxes_ibfk_3` FOREIGN KEY (`debit_account_id`) REFERENCES `accounts` (`account_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `withholding_taxes_ibfk_4` FOREIGN KEY (`payment_voucher_item_id`) REFERENCES `payment_voucher_items` (`payment_voucher_item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `withholding_taxes_ibfk_5` FOREIGN KEY (`receipt_item_id`) REFERENCES `receipt_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `withholding_taxes_ibfk_6` FOREIGN KEY (`stakeholder_id`) REFERENCES `stakeholders` (`stakeholder_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `withholding_taxes_payments`
--
ALTER TABLE `withholding_taxes_payments`
  ADD CONSTRAINT `withholding_taxes_payments_ibfk_1` FOREIGN KEY (`withholding_tax_id`) REFERENCES `withholding_taxes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `withholding_taxes_payments_ibfk_2` FOREIGN KEY (`paid_by`) REFERENCES `employees` (`employee_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
