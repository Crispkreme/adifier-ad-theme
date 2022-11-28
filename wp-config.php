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
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         'y{HYp)Zkva+9Z$&a=EY8;WG8d.O<W>hUS:2g?LIV%uSksk9xywANy]A^5`Q|$x?.' );
define( 'SECURE_AUTH_KEY',  '2^Pb@#jiaDWx/mB|e:E,tf>T0xUFk:5V)-y)HP*Djkf7sbl[he{T34dxWju7V=Mt' );
define( 'LOGGED_IN_KEY',    'wXnZ_s5>h1-$|!4=<RxB:v`aJK%2h6WpA%p>:CarK7E;H_IP]WA]Nr*k<}>MU:Wa' );
define( 'NONCE_KEY',        'h htyS/xcumTY_M~Dflh)5gixKmy_*bI_3d[cj@_-*cz{Ir2mDy6`Yl(>)[X,%X{' );
define( 'AUTH_SALT',        'Q/-4-U~Dlbb*)U%o51G{1JNw/yLuHD&MjMbw@V%|))vv?E5<2keg;q#G!`GgI2Ck' );
define( 'SECURE_AUTH_SALT', 'V$+EH#6hb<1&nH(0(qT_]t?Qdszce.k~AwnP|>AtJ1S|=*DFKqzT$jO80UIqg6Bc' );
define( 'LOGGED_IN_SALT',   'G_Uv4HZvYb`(Tzq:eOt+-vy3[0,aVT4:JS[5M4v0kd0ukhGKQk2 ZAzK{5*d~lGQ' );
define( 'NONCE_SALT',       'Tua4>o4SYGXT+)-13c%W3)1eQ46mF5|M+$g{(1vQg2k,X.Gr[@z;qGWAQG2YO.,b' );

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
