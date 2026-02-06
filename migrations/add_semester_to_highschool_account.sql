-- Add semester column to highschool_account table
ALTER TABLE `highschool_account` ADD COLUMN `semester` VARCHAR(50) DEFAULT '1st semester' AFTER `password`;
