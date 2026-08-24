<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2024-10-02 16:21:44 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '.`tblcontacts`.`lastname` as `user_lastname`, `tblstaff`.`firstname` as `staff_f' at line 1 - Invalid query: SELECT *, `tbltickets`.`userid`, `tbltickets`.`name` as `from_name`, `tbltickets`.`email` as `ticket_email`, `tbldepartments`.`name` as `department_name`, `tblpriorities`.`name` as `priority_name`, `statuscolor`, `tbltickets`.`admin`, `tblservices`.`name` as `service_name`, `service`, `tblticketstatus`.`name` as `status_name`, `tbltickets`.`ticketid`, `subject`, `tblcontacts`.`firstname` as `user_firstname`, .`tblcontacts`.`lastname` as `user_lastname`, `tblstaff`.`firstname` as `staff_firstname`, `tblstaff`.`lastname` as `staff_lastname`, `lastreply`, `message`, `tbltickets`.`status`, `subject`, `department`, `priority`, `tblcontacts`.`email`, `adminread`, `clientread`, `date`
FROM `tbltickets`
LEFT JOIN `tbldepartments` ON `tbldepartments`.`departmentid` = `tbltickets`.`department`
LEFT JOIN `tblticketstatus` ON `tblticketstatus`.`ticketstatusid` = `tbltickets`.`status`
LEFT JOIN `tblservices` ON `tblservices`.`serviceid` = `tbltickets`.`service`
LEFT JOIN `tblclients` ON `tblclients`.`userid` = `tbltickets`.`userid`
LEFT JOIN `tblcontacts` ON `tblcontacts`.`id` = `tbltickets`.`contactid`
LEFT JOIN `tblstaff` ON `tblstaff`.`staffid` = `tbltickets`.`admin`
LEFT JOIN `tblpriorities` ON `tblpriorities`.`priorityid` = `tbltickets`.`priority`
WHERE `tbltickets`.`ticketid` = '9'
