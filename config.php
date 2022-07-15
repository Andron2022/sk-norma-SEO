<?php
  /************************
  *
  * Configuration file
  * for current website
  * 
  *************************/

    // AUTHKEY - for password encryption
    if (!defined('AUTHKEY')) { define('AUTHKEY', 'simpla.es'); }

    // Admin folder
    if (!defined('ADMIN_FOLDER')) { define('ADMIN_FOLDER', 'admin'); }

    // Database settings
    
    /*
    if (!defined('HOSTNAME')) { define('HOSTNAME', 'localhost'); }
    if (!defined('DATABASE')) { define('DATABASE', 'sknorma'); }
    if (!defined('DBUSER')) { define('DBUSER', 'root'); }
    if (!defined('DBPASS')) { define('DBPASS', ''); }
    */

    if (!defined('HOSTNAME')) { define('HOSTNAME', 'localhost'); }
    if (!defined('DATABASE')) { define('DATABASE', 'u1126524_sk'); }
    if (!defined('DBUSER')) { define('DBUSER', 'u1126524_sk'); }
    if (!defined('DBPASS')) { define('DBPASS', 'F2y5U3d4'); }

    // Prefix for database tables
    if (!defined('PREFIX')) { define('PREFIX', ''); }

    // Prefix for database tables
    if (!defined('MODE')) { define('MODE', 'web'); }
        
    // days in basket for items
    if (!defined('INBASKET')) { define('INBASKET', 30); }
    
    // ONPAGE
    if (!defined('ONPAGE')) { define('ONPAGE', 20); }
    
    // URL_END end for url: / or .html etc.
    if (!defined('URL_END')) { define('URL_END', ''); }
    
    // BGCOLOR
    if (!defined('BGCOLOR')) { define('BGCOLOR', '#cccccc'); }
    
    // EMAIL_CHARSET utf-8 or koi8-r or cp1251
    if (!defined('EMAIL_CHARSET')) { define('EMAIL_CHARSET', 'utf-8'); }
    
    // UNKNOWN_SITE - if site url not found in database
    // true - will show default site, false - error 503 
    if (!defined('UNKNOWN_SITE')) { define('UNKNOWN_SITE', true); }
    
    
?>