-- Migration: Enhance highschool_account table to match college_account structure
-- This migration adds additional fields to highschool_account table

ALTER TABLE `highschool_account` ADD COLUMN `applicant_id` int(11) NOT NULL AFTER `id`;
ALTER TABLE `highschool_account` ADD COLUMN `first_name` varchar(100) NOT NULL AFTER `applicant_id`;
ALTER TABLE `highschool_account` ADD COLUMN `middle_name` varchar(100) DEFAULT NULL AFTER `first_name`;
ALTER TABLE `highschool_account` ADD COLUMN `last_name` varchar(100) DEFAULT NULL AFTER `middle_name`;
ALTER TABLE `highschool_account` ADD COLUMN `school` varchar(150) DEFAULT NULL AFTER `last_name`;
ALTER TABLE `highschool_account` ADD COLUMN `year_level` varchar(50) DEFAULT NULL AFTER `school`;
ALTER TABLE `highschool_account` ADD COLUMN `address` varchar(255) DEFAULT NULL AFTER `year_level`;
ALTER TABLE `highschool_account` ADD COLUMN `phone_number` varchar(50) DEFAULT NULL AFTER `address`;
ALTER TABLE `highschool_account` ADD COLUMN `status` varchar(155) DEFAULT NULL AFTER `phone_number`;
ALTER TABLE `highschool_account` ADD COLUMN `schedule_date` date DEFAULT NULL AFTER `status`;
ALTER TABLE `highschool_account` ADD COLUMN `schedule_time` time DEFAULT NULL AFTER `schedule_date`;
