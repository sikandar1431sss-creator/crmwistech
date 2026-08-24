<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|   http://example.com/
|
| If this is not set then CodeIgniter will try guess the protocol, domain
| and path to your installation. However, you should always configure this
| explicitly and never rely on auto-guessing, especially in production
| environments.
|
*/

define('APP_BASE_URL','http://localhost/24aug/');

/*
|--------------------------------------------------------------------------
| Local safety switch
|--------------------------------------------------------------------------
|
| Keep outbound communication disabled while working locally. When enabled,
| the app will not send emails, SMS messages, staff/client notifications, or
| realtime Pusher notification events.
|
*/
define('APP_DISABLE_OUTBOUND', true);

/*
|--------------------------------------------------------------------------
| Encryption Key
| IMPORTANT: Do not change this ever!
|--------------------------------------------------------------------------
|
| If you use the Encryption class, you must set an encryption key.
| See the user guide for more info.
|
| http://codeigniter.com/user_guide/libraries/encryption.html
|
| Auto updated added on install
*/

define('APP_ENC_KEY','e582b39271629d3b24ebd0a71414bf49');
/**
 * Database Credentials
 */
/* The hostname of your database server. */
define('APP_DB_HOSTNAME','localhost');
/* The username used to connect to the database */
define('APP_DB_USERNAME','root');
/* The password used to connect to the database */
define('APP_DB_PASSWORD','');
/* The name of the database you want to connect to */
define('APP_DB_NAME','crmwistech');


/**
 *
 * Session handler driver
 * By default the database driver will be used.
 *
 * For files session use this config:
 * define('SESS_DRIVER', 'files');
 * define('SESS_SAVE_PATH', NULL);
 * In case you are having problem with the SESS_SAVE_PATH consult with your hosting provider to set "session.save_path" value to php.ini
 *
 */

define('SESS_DRIVER','database');
define('SESS_SAVE_PATH','tblsessions');

/**
 * Enables CSRF Protection
 */
define('APP_CSRF_PROTECTION',true);
