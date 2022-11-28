<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'project_wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '*%?%Nn=[w]CzOjjXONyp,EnN@aGiY%zOEv-tn,H@3R2q7aMWs!->~E9&77b=&~00' );
define( 'SECURE_AUTH_KEY',  ';*#q]a+:%Ey49-J/`*HkjLC]+~KH@adN3-6ZiyMv?vtaH:^,QWMF/20?;t[d#76F' );
define( 'LOGGED_IN_KEY',    'Qlo:Fkle8QwB~f61+Md7=K6dqW}6v2k7e|QUIA2_Y?Yd]uE?Sx:obg/DH,wP5Q^Z' );
define( 'NONCE_KEY',        ':9yI8gG&3eZO{%BEW@kC{VEFOe?$V;pF3B^$XjY$Yz^8R+LK?;8`,)Ud[WbAAK?$' );
define( 'AUTH_SALT',        '}3syuzj:_ci^c/qu],%-kte|SN:SNeZEqcodpOF(NJp`e2.y4w|`}JZIP Wq.1aE' );
define( 'SECURE_AUTH_SALT', 'Tjw!LGy=&%9*w1x!d7<cxf4DuR!M}mDfS6dJgO0f^y/8U:xt;I+5H8Bwg@*Wl]k~' );
define( 'LOGGED_IN_SALT',   '_P0U&.[)20,,yqKO*tjX/RDot8`5NKz. BSa(j<Z5kt`2v^vIUSGM{agus]*4Q~i' );
define( 'NONCE_SALT',       'a0Gp_I@x:/R:rRMb7G9|4NCa>=br,!z`x#b[fi[/2F,[n{%c|^)11rQ*)9#nQq7#' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
