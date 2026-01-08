<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

// By Speed Optimizer by SiteGround

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
define( 'DB_NAME', 'Bandieredelmondo' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          'C7OPe$GW#5_/GNr=v*8U=bVoPCOVIftG+Ek7Lh=o$<=^$56baTADQW^eStGX,~MU' );
define( 'SECURE_AUTH_KEY',   'k_EaN[/pdIc2iQ)}(TB?C Kvsn$CPxo420.w4h5|2hPC%lNiQ<2!E4mc9V8(9Rt_' );
define( 'LOGGED_IN_KEY',     'O<QQ-`]Mi`K.y0NsLhC ^6%5`eKEPj2TPC!^cZEE|93VG)?/U7CztIk=xG>7X;@{' );
define( 'NONCE_KEY',         'iKXaNiaRfQT#H2z7Stl]{S5.l[%2ax0M$t]P:ST?{f3C],aI97Hz) 7>r5JG)< Y' );
define( 'AUTH_SALT',         '9DN`p(/%]JindCrPDMj/n&fa[B@51I_6I>Mz)~Nrv#u5N:5aRCE3wHQ2;wmxctt}' );
define( 'SECURE_AUTH_SALT',  '+/=TrIZpILwf=}TkO4~>R@_h$~&Ej/YcAVbvZ=!9~asH&UmZ,3m[lkKSTt}~/#Rl' );
define( 'LOGGED_IN_SALT',    'Le_*D3P{|7z/9JEd.A|Ej;eB0UGgQvXH.??vSyRI[[WP1QCKl86IBvF}Y4}(L.|H' );
define( 'NONCE_SALT',        'N6}h1<h<uJRO5*|%.7ZBo1+I(nb*/K%SrR22@QH@|hK<`y6(iM]0QYU-9.N]9ZT~' );
define( 'WP_CACHE_KEY_SALT', 'JWlPa17mA(M]c60~;7sSOq>`X`&:Z/zabX?eadr-M.`L)[5>A<`xE706vI^s8bG-' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'ljs_';


/* Add any custom values between this line and the "stop editing" line. */



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
@include_once('/var/lib/sec/wp-settings-pre.php'); // Added by SiteGround WordPress management system
require_once ABSPATH . 'wp-settings.php';
@include_once('/var/lib/sec/wp-settings.php'); // Added by SiteGround WordPress management system
