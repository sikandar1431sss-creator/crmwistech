<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2020-09-02 19:04:10 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 22:04:11 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 19:12:24 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 22:12:25 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 19:12:48 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 22:12:48 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 19:13:02 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 22:13:03 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 19:14:04 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 22:14:05 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 19:14:30 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 22:14:31 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 19:15:01 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 22:15:01 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 19:15:11 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 22:15:11 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 22:48:26 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 20:02:24 --> 404 Page Not Found: Assets/plugins
ERROR - 2020-09-02 23:02:25 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near 'company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) E' at line 8 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS ANY_VALUE(staff_id) as staff_id, ANY_VALUE(name) as name, ANY_VALUE((SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tbltaskstimers.id and rel_type="timesheet" ORDER by tag_order ASC)) as tags, ANY_VALUE(start_time) as start_time, ANY_VALUE(end_time) as end_time, ANY_VALUE(note) as note, ANY_VALUE((CASE rel_type
        WHEN "contract" THEN (SELECT subject FROM tblcontracts WHERE tblcontracts.id = tblstafftasks.rel_id)
        WHEN "estimate" THEN (SELECT id FROM tblestimates WHERE tblestimates.id = tblstafftasks.rel_id)
        WHEN "proposal" THEN (SELECT id FROM tblproposals WHERE tblproposals.id = tblstafftasks.rel_id)
        WHEN "invoice" THEN (SELECT id FROM tblinvoices WHERE tblinvoices.id = tblstafftasks.rel_id)
        WHEN "ticket" THEN (SELECT CONCAT(CONCAT("#",tbltickets.ticketid), " - ", tbltickets.subject) FROM tbltickets WHERE tbltickets.ticketid=tblstafftasks.rel_id)
        WHEN "lead" THEN (SELECT CASE tblleads.email WHEN "" THEN tblleads.name ELSE CONCAT(tblleads.name, " - ", tblleads.email) END FROM tblleads WHERE tblleads.id=tblstafftasks.rel_id)
        WHEN "customer" THEN (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname  "-" company) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE tblclients.userid=tblstafftasks.rel_id)
        WHEN "project" THEN (SELECT CONCAT(CONCAT(CONCAT("#",tblprojects.id)," - ",tblprojects.name), " - ", (SELECT CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END FROM tblclients WHERE userid=tblprojects.clientid)) FROM tblprojects WHERE tblprojects.id=tblstafftasks.rel_id)
        WHEN "expense" THEN (SELECT CASE expense_name WHEN "" THEN tblexpensescategories.name ELSE
         CONCAT(tblexpensescategories.name, ' (',tblexpenses.expense_name,')') END FROM tblexpenses JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category WHERE tblexpenses.id=tblstafftasks.rel_id)
        ELSE NULL
        END)) as rel_name, ANY_VALUE(end_time - start_time) as time_h, ANY_VALUE(end_time - start_time) as time_d ,ANY_VALUE(tbltaskstimers.id) as id,ANY_VALUE(task_id) as task_id,ANY_VALUE(rel_type) as rel_type,ANY_VALUE(rel_id) as rel_id,ANY_VALUE(status) as status
    FROM tbltaskstimers
    LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id
    
    WHERE  task_id != 0  AND start_time BETWEEN 1598990400 AND 1599076799
    
    ORDER BY ANY_VALUE(start_time) DESC
    LIMIT 0, 25
    
ERROR - 2020-09-02 23:09:52 --> Severity: Notice --> unserialize(): Error at offset 0 of 1 bytes /home/wiscrm/public_html/application/models/Projects_model.php 220
ERROR - 2020-09-02 23:09:52 --> Severity: Notice --> unserialize(): Error at offset 0 of 1 bytes /home/wiscrm/public_html/application/models/Projects_model.php 220
ERROR - 2020-09-02 23:10:26 --> Severity: Notice --> unserialize(): Error at offset 0 of 1 bytes /home/wiscrm/public_html/application/models/Projects_model.php 220
ERROR - 2020-09-02 23:10:26 --> Severity: Notice --> unserialize(): Error at offset 0 of 1 bytes /home/wiscrm/public_html/application/models/Projects_model.php 220
