<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property CI_URI::$config is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/URI.php 101
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property CI_Router::$uri is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Router.php 127
ERROR - 2026-09-01 10:09:45 --> Severity: Warning --> Undefined array key "REQUEST_URI" /Applications/XAMPP/xamppfiles/htdocs/24aug/application/config/routes.php 110
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> strpos(): Passing null to parameter #1 ($haystack) of type string is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/application/config/routes.php 110
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$benchmark is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$hooks is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$config is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$log is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$utf8 is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$uri is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$exceptions is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$router is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$output is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$security is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$input is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$lang is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 75
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$load is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php 78
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$session is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Loader.php 1283
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property Clients::$db is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Loader.php 396
ERROR - 2026-09-01 10:09:45 --> Severity: 8192 --> Creation of dynamic property CI_DB_mysqli_driver::$failover is deprecated /Applications/XAMPP/xamppfiles/htdocs/24aug/system/database/DB_driver.php 371
ERROR - 2026-09-01 10:09:45 --> Severity: Error --> Uncaught mysqli_sql_exception: No such file or directory in /Applications/XAMPP/xamppfiles/htdocs/24aug/system/database/drivers/mysqli/mysqli_driver.php:201
Stack trace:
#0 /Applications/XAMPP/xamppfiles/htdocs/24aug/system/database/drivers/mysqli/mysqli_driver.php(201): mysqli->real_connect('localhost', 'root', Object(SensitiveParameterValue), 'crmwistech', NULL, NULL, 0)
#1 /Applications/XAMPP/xamppfiles/htdocs/24aug/system/database/DB_driver.php(401): CI_DB_mysqli_driver->db_connect(false)
#2 /Applications/XAMPP/xamppfiles/htdocs/24aug/system/database/DB.php(216): CI_DB_driver->initialize()
#3 /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Loader.php(399): DB(Array, NULL)
#4 /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Loader.php(1354): CI_Loader->database()
#5 /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Loader.php(157): CI_Loader->_ci_autoloader()
#6 /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/Controller.php(79): CI_Loader->initialize()
#7 /Applications/XAMPP/xamppfiles/htdocs/24aug/application/core/CRM_Controller.php(9): CI_Controller->__construct()
#8 /Applications/XAMPP/xamppfiles/htdocs/24aug/application/core/Clients_controller.php(25): CRM_Controller->__construct()
#9 /Applications/XAMPP/xamppfiles/htdocs/24aug/application/controllers/Clients.php(9): Clients_controller->__construct()
#10 /Applications/XAMPP/xamppfiles/htdocs/24aug/system/core/CodeIgniter.php(518): Clients->__construct()
#11 /Applications/XAMPP/xamppfiles/htdocs/24aug/index.php(321): require_once('/Applications/X...')
#12 Command line code(1): require('/Applications/X...')
#13 {main}
  thrown /Applications/XAMPP/xamppfiles/htdocs/24aug/system/database/drivers/mysqli/mysqli_driver.php 201
