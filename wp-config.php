<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'rfgd_19684073_abdo');

/** MySQL database username */
define('DB_USER', 'rfgd_19684073');

/** MySQL database password */
define('DB_PASSWORD', 'abdo111999');

/** MySQL hostname */
define('DB_HOST', 'sql210.rf.gd');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '[1W/B#I)MJ8sBa,@%W%Ht#+-vPTKSm_h$1)N<`!D1r+?FQ56<O!zVoZy`:S#Jq2J');
define('SECURE_AUTH_KEY',  'P}-sRC?{y*{87Yo!w}Mcn9f.e:UQRI^;Ra K,,6y=r$gFkj!3#qql, #fqL(sIcI');
define('LOGGED_IN_KEY',    'sInY.Q1^l8r_sAFIB1gZmuGtJ2(j:]!_@,(q:K neV8P8]^b(AM#%tqZ|.pUI)P?');
define('NONCE_KEY',        '= `{fUz{B!t%*41Y/>Jkek25Hi7W!|X.M`ic;z>e;w0_dD&~iXQp$T=ea%t3U2q(');
define('AUTH_SALT',        '-dv{dHr^Q/_~y=%`%xP{;Df%tgA->;J9/2_cVri~-oZXiFG{9@_qXlL=T<Xv|+06');
define('SECURE_AUTH_SALT', 'KYW>DXo[4qe>bI(#7$6sIxg?O|ZA?DBv]ybS|R;fpj$n.KW_i5WfkG;iYA`fc^aj');
define('LOGGED_IN_SALT',   'NV3_MtnBU|-m%u:S&GwEkOy1]*2|YF9y*llX6e)0;k81ZYBJ6[jK5OA`-/2m[Sx`');
define('NONCE_SALT',       'I1+fM#F-K!?l[!HJ7pf=>Lq&n72Tt5I$9juJ0QZ.%AgWaAdA6chejU0u@L>$GXF@');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
