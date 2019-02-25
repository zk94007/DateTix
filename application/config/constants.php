<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 |--------------------------------------------------------------------------
 | File and Directory Modes
 |--------------------------------------------------------------------------
 |
 | These prefs are used when checking and setting modes when working
 | with the file system.  The defaults are fine on servers with proper
 | security, but you may wish (or even need) to change the values in
 | certain environments (Apache running a separate process for each
 | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
 | always be used to set the mode correctly.
 |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
 |--------------------------------------------------------------------------
 | File Stream Modes
 |--------------------------------------------------------------------------
 |
 | These modes are used when working with fopen()/popen()
 |
 */

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');
define('SQL_DATETIME',		date("Y-m-d H:i:s"));
define('SQL_DATE1',		date("Y-m-d"));
define('DATE_FORMATE',"F j, Y");


define('LOGO_NOT_AVAILABLE','images/logo_not_found.png');
define('INFO_EMAIL','info@datetix.com');
define('CEO_EMAIL','micheal.ye@datetix.com');


//record limit on displaying on page.
define('PER_PAGE',20);
define('PER_PAGE_ADMIN',100);

define('TIMEOUT_PAGE_SECONDS',3600);
define('FB_PHOTO_LIMIT',50);


define('FB_RESTRICTED_COUNTRY','7');

// Pass membership id in contant
define('PERMISSION_UNLIMITED_DATES',1);
define('PERMISSION_PRIMIUM_FILTER_ACCESS',2);
define('PERMISSION_INSTANT_INTRO',3);
define('PERMISSION_RE_INTRO',4);
define('PERMISSION_PAST_FEEDBACK',5);
define('PERMISSION_MORE_INTRODUCTIONS',6);

define('MAX_FREE_UPCOMING_INTROS',50);
define('MAX_PAID_UPCOMING_INTROS',200);
define('DEFAULT_IDEAL_FILTERS_ID',"1,2,5,7,20,23");

define('SMALL_EVENT_USER_LIMIT', 20);
define('REQUIRED_DATE_COMPLETED_STEP', 4);
define('CRON_USER_LIMIT',"1000");
define('RECENT_CHAT_CHAR_LIMIT',"80");
define('LAST_SIGN_UP_STEP',5);
define('LAST_MINUTE_DATE',10);
define('REFUND_EMAIL','refund@datetix.com');
/* End of file constants.php */
/* Location: ./application/config/constants.php */
