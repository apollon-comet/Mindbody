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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'nexusbond_wp_bc24t' );

/** Database username */
define( 'DB_USER', 'nexusbond_wp_pxcrx' );

/** Database password */
define( 'DB_PASSWORD', '*g#0f%gd0OAhH22^' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define('AUTH_KEY', 'O4m3[+jWhH2[4yPQkU(#A8+ucKmkTv#87d4T7P]vCZ]~ax;0*J|s8ZcYZe9h-8%%');
define('SECURE_AUTH_KEY', ';E0J+U!w898hBx9_9E2nw6tnzA;66Z:(Lvp;fqc%1(9Huf0e[!k2d)ee]Fzsr*(S');
define('LOGGED_IN_KEY', 'MSc6L(+P2pL289W3I4;9DsUQ3+93(&G*R-)pstGeY:%DYwv4w|qda||V6qVEr7S&');
define('NONCE_KEY', '6F2;d#[4-8n%(b(5l!lMv1x8jcGR%8!A(CLaO-%xVqc+@2;o6DliC6)q4&0K95~)');
define('AUTH_SALT', 'H6CvA(4*&M~@DoqU371y2f_2HZ:-Xu4noHsw3&s3N5j9yCc(pi~x1CbPq5(U@jGL');
define('SECURE_AUTH_SALT', 'q*JTP)|vT&S)0|]ZS68#)tys3mQQ0G2TD|W%y@9VY%P[]75X06!*s7Rr*JgGE!+*');
define('LOGGED_IN_SALT', '85#+b6CB!Z+n|3@Ni52]%ZUp6/yT3*~h-zS4&R/L948g5;&M/5g*qJ8OWd*C~g7F');
define('NONCE_SALT', 'gmD%0S0%*%]0@-7*&~7~*~Mz4M]_E7@s1UP:!Te*pq22bn!v6aIyQ)U5%L~v|6w:');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'nb_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
