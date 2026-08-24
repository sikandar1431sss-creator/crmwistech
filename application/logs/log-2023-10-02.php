<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2023-10-02 15:36:51 --> 404 Page Not Found: Assets/plugins
ERROR - 2023-10-02 18:36:51 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'groups, tblclients.datecreated as datecreated, ctable_0.value as date_picker_cva' at line 1 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tblclients.userid as userid, company, CONCAT(firstname, " ", lastname) as contact_fullname, email, tblclients.phonenumber as phonenumber, `tblclients`.`active` AS `tblclients.active`, (SELECT GROUP_CONCAT(name ORDER BY name ASC) FROM tblcustomersgroups JOIN tblcustomergroups_in ON tblcustomergroups_in.groupid = tblcustomersgroups.id WHERE customer_id = tblclients.userid LIMIT 1) as groups, tblclients.datecreated as datecreated, ctable_0.value as date_picker_cvalue_0 ,tblcontacts.id as contact_id,tblclients.zip as zip,registration_confirmed
    FROM tblclients
    LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=10
    
    WHERE  (tblclients.active = 1 OR tblclients.active=0 AND registration_confirmed = 0)
    
    ORDER BY company ASC
    LIMIT 0, 25
    
ERROR - 2023-10-02 15:37:04 --> 404 Page Not Found: Assets/plugins
ERROR - 2023-10-02 18:37:05 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'groups, tblclients.datecreated as datecreated, ctable_0.value as date_picker_cva' at line 1 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tblclients.userid as userid, company, CONCAT(firstname, " ", lastname) as contact_fullname, email, tblclients.phonenumber as phonenumber, `tblclients`.`active` AS `tblclients.active`, (SELECT GROUP_CONCAT(name ORDER BY name ASC) FROM tblcustomersgroups JOIN tblcustomergroups_in ON tblcustomergroups_in.groupid = tblcustomersgroups.id WHERE customer_id = tblclients.userid LIMIT 1) as groups, tblclients.datecreated as datecreated, ctable_0.value as date_picker_cvalue_0 ,tblcontacts.id as contact_id,tblclients.zip as zip,registration_confirmed
    FROM tblclients
    LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=10
    
    WHERE  (tblclients.active = 1 OR tblclients.active=0 AND registration_confirmed = 0)
    
    ORDER BY company ASC
    LIMIT 0, 25
    
ERROR - 2023-10-02 15:38:55 --> 404 Page Not Found: Assets/plugins
ERROR - 2023-10-02 18:38:55 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'groups, tblclients.datecreated as datecreated, ctable_0.value as date_picker_cva' at line 1 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, tblclients.userid as userid, company, CONCAT(firstname, " ", lastname) as contact_fullname, email, tblclients.phonenumber as phonenumber, `tblclients`.`active` AS `tblclients.active`, (SELECT GROUP_CONCAT(name ORDER BY name ASC) FROM tblcustomersgroups JOIN tblcustomergroups_in ON tblcustomergroups_in.groupid = tblcustomersgroups.id WHERE customer_id = tblclients.userid LIMIT 1) as groups, tblclients.datecreated as datecreated, ctable_0.value as date_picker_cvalue_0 ,tblcontacts.id as contact_id,tblclients.zip as zip,registration_confirmed
    FROM tblclients
    LEFT JOIN tblcontacts ON tblcontacts.userid=tblclients.userid AND tblcontacts.is_primary=1 LEFT JOIN tblcustomfieldsvalues as ctable_0 ON tblclients.userid = ctable_0.relid AND ctable_0.fieldto="customers" AND ctable_0.fieldid=10
    
    WHERE  (tblclients.active = 1 OR tblclients.active=0 AND registration_confirmed = 0)
    
    ORDER BY company ASC
    LIMIT 0, 25
    
