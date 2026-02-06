-- Add semester column to highschool_record table
ALTER TABLE `highschool_record` ADD COLUMN `semester` VARCHAR(50) DEFAULT NULL AFTER `year_level`;
