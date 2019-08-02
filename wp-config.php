<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache





/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
// if($_SERVER['HTTP_HOST'] == "localhost"){
// define('DB_NAME', 'customcabledemo');
// // define('DB_NAME', 'custom_cable');
// }
// else{
// define('DB_NAME', 'custom_cable');
// }

define( 'SMTP_USER',   'shop@megladonmfg.com' );    // Username to use for SMTP authentication
define( 'SMTP_PASS',   'megladonmfg#1' );       // Password to use for SMTP authentication
define( 'SMTP_HOST',   'secure.emailsrvr.com' );    // The hostname of the mail server
define( 'SMTP_FROM',   'shop@megladonmfg.com' ); // SMTP From email address
define( 'SMTP_NAME',   'Megladon Order Updates' );    // SMTP From name
define( 'SMTP_PORT',   '25' );                  // SMTP port number - likely to be 25, 465 or 587
define( 'SMTP_SECURE', 'tls' );                 // Encryption system to use - ssl or tls
define( 'SMTP_AUTH',    true );                 // Use SMTP authentication (true|false)


/** MySQL database username */
// define('DB_USER', 'custom_cable');

/** MySQL database password */
// define('DB_PASSWORD', 'custom_cable');

/** MySQL hostname */
// define('DB_HOST', '192.168.100.71');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');


/**
 * DB Settings for deployment
 * #1 itgladonmfg.com
 */
define('DB_NAME', 'custom_cable');
/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', ',2`W=jV{A0[6E]fL[HR}@i&}>x]Y) Ry^*:fxrC#uPvlt<$oQNilY}hMyT$wYxA;' );
define( 'SECURE_AUTH_KEY', 'J_G5A)Nw*`OA;3E*zTX5wTFL5NF[9OD>5<%^1ECmsBT,Yfg2R$$AOoKLaPV|q=_p' );
define( 'LOGGED_IN_KEY', 'RAcB!=y@EcC{t=Xym*c[~VD,Jv$?:?p1A+rCodU<Of$9KB#U6 ^@5_Z&CjLZiG*D' );
define( 'NONCE_KEY', 's52fsdF+=!;^`k+(Jl[i1b=N/VX.n^K+01r gj=/`7Xv3-of9Lp~!=Ejm6!r^/ws' );
define( 'AUTH_SALT', '5$8Upj@bF.#}!La7t0C;UuEBFb3MH0eC^7,|hRBv8d9Xl8N,4CTb: fz{3ai<{):' );
define( 'SECURE_AUTH_SALT', 'xuH9J9;OS*CAlTjxx45yM,2@?URR@$V Wj~!EfLka$f4q!1KLb`Jsj9=/LPa>_c9' );
define( 'LOGGED_IN_SALT', 'H tk^a+)/rAcAJ2-5VA,V}W7dvP8 =+h6RsrRcL]>lNgNCDkRKwC]<3S-,N5xbjM' );
define( 'NONCE_SALT', 'nf6:C}C[eP2Wzu={k![e{6h}7S*$g #28~Q&t9Vy{ts??E@O ulEf7}nX2Io)$/g' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'x2mnb_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', true);

define('DISABLE_WP_CRON', 'true');

/* That's all, stop editing! Happy blogging. */

define('WP_MEMORY_LIMIT', '96M');

/* Specify maximum number of Revisions. */
define( 'WP_POST_REVISIONS', '0' );
/*Auto save time*/
define( 'AUTOSAVE_INTERVAL', 120 ); // Seconds
/* Media Trash. */
define( 'MEDIA_TRASH', true );
/* Trash Days. */
define( 'EMPTY_TRASH_DAYS', '10' );


///* Deactivate if not working March 7 *//
///* Compression */
define( 'COMPRESS_CSS',        true );
define( 'COMPRESS_SCRIPTS',    true );
define( 'CONCATENATE_SCRIPTS', true );
define( 'ENFORCE_GZIP',        true );




define( 'FTP_USER', 'megladonmfg' );
define( 'FTP_PASS', 'zzBqci3SkKp]' );
define( 'FTP_HOST', 'localhost' );



/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');



/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');


