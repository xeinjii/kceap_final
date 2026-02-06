-- Add semester column to college_account table
ALTER TABLE `college_account` ADD COLUMN `semester` VARCHAR(50) DEFAULT '1st semester' AFTER `password`;
