
-- ADD new roles
INSERT INTO `vpsdb`.`user_roles` (`id`, `role_name`) VALUES ('4', 'sales_agent');
INSERT INTO `vpsdb`.`user_roles` (`id`, `role_name`) VALUES ('5', 'manager');

-- Add new column in visa_applications table
ALTER TABLE visa_applications ADD COLUMN applicant_surname VARCHAR(255) NULL after applicant_name;
ALTER TABLE visa_applications ADD COLUMN deadline_date DATE NULL after applicant_limit;

-- Add new column in notices table
ALTER TABLE notices ADD COLUMN user_id integer NULL after id;