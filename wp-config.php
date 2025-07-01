<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u962520559_may' );

/** Database username */
define( 'DB_USER', 'u962520559_may' );

/** Database password */
define( 'DB_PASSWORD', 'O9]iR?Kez' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'T|x:x|3ZoXKN{U@=_y[:5`^PBnFXtLR!Mkv0JTQP6hX<b[RN^2w^?KB*aZX!S,_~' );
define( 'SECURE_AUTH_KEY',  'Oy_#|Zc?E9jzurOqs$F2/XmGMx28{|OsJ_U`+3])zeo1G!#uKIX[Dht*+US]F?u{' );
define( 'LOGGED_IN_KEY',    'S2~$^E8.8``Qt1 wt;&,t+_ZHKE8YyA!Vu8DSkv^6)&hX@Bi&0>/pE2CBpu34vVL' );
define( 'NONCE_KEY',        'wWOMIJMq.R}Rsr&E>dy*u./7#bv|haqh80h;_fD?vv 9~9LVA1F(%Ygu]mpt`y&H' );
define( 'AUTH_SALT',        'f^w-u.f;s8?x<35?s&I5@|KidsT(h+AwPU@5:.^6DMi{t`dO5zBGC?FO5~:mwiq9' );
define( 'SECURE_AUTH_SALT', 'c^{8xU)87cSlQ=iWDH7`!18rN9WBH##|E_z|0})}Dt~(qp@HnUr?yk?qJH$#wD%p' );
define( 'LOGGED_IN_SALT',   'h)CcaH)Rc#dfz,TKaBfK>w7l3Gg_>c;p-O}Z;q0X6Qojkf|>t]<;FOde7K3lkd(B' );
define( 'NONCE_SALT',       '~fqm2U=a4eAD0I:JNG{KS_DHNRh=B4j!|/33(66|g]lSD#~2H3;EH:N;;`qhb-lr' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );

define( 'WP_DEBUG_LOG', true );

define( 'WP_DEBUG_DISPLAY', false );
/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
