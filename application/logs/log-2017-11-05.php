<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2017-11-05 11:04:51 --> Query error: Unknown column 'noteType' in 'field list' - Invalid query: INSERT INTO `tblinvoices` (`clientid`, `project_id`, `billing_street`, `billing_city`, `billing_state`, `billing_zip`, `show_shipping_on_invoice`, `shipping_street`, `shipping_city`, `shipping_state`, `shipping_zip`, `number`, `date`, `duedate`, `allowed_payment_modes`, `currency`, `sale_agent`, `recurring`, `discount_type`, `adminnote`, `show_quantity_as`, `subtotal`, `discount_percent`, `discount_total`, `adjustment`, `total`, `clientnote`, `noteType`, `terms`, `prefix`, `number_format`, `hash`, `cancel_overdue_reminders`, `datecreated`, `addedfrom`, `include_shipping`) VALUES ('1354', 0, '', '', '', '', 1, NULL, NULL, NULL, NULL, '001564', '2017-11-05', '2017-12-05', 'a:1:{i:0;s:1:\"1\";}', '1', '22', '0', '', '', '1', '100.00', '0', '0', '0.00', '100.00', '', '', '', 'INV-', '1', '7a1c06b557df2af7201dfcc1ecc33cd0', 0, '2017-11-05 11:04:51', '22', 0)
ERROR - 2017-11-05 11:15:35 --> Could not find the language line "Renewable"
ERROR - 2017-11-05 11:15:35 --> Could not find the language line "From"
ERROR - 2017-11-05 11:15:35 --> Could not find the language line "To"
ERROR - 2017-11-05 11:15:50 --> Query error: Unknown column 'email_signature_image' in 'field list' - Invalid query: SELECT `staffid`, `email_signature`, `email_signature_image`
FROM `tblstaff`
WHERE `staffid` = '22'
