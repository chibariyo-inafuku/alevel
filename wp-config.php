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
define( 'DB_NAME', 'alevel' );

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
define( 'AUTH_KEY',         'fo7sI<%te07yzn=d ;7l5Z3B%g3,Zx/I?s>G>^>%%SkZmYhl?Q0NS-XAa6Zx< H2' );
define( 'SECURE_AUTH_KEY',  'nsjD`=b~i#qSL9MZ Z)#Sod8A0owk_#3)8YHKm$1Ab^OA{a86>{9LerLGtdk1eeX' );
define( 'LOGGED_IN_KEY',    'Nuc0<c4XH3Rd;vW?_78&sPj!Sfr;@1w++,h 3Xt?OZ#%1)S7tl3;C87U5,+w$ml6' );
define( 'NONCE_KEY',        '|hfk imtc`dl0Ea%le%Aa*d}nPJkq=C=Vfq^=%NL1`+O<{9]-7Ih![[#cR7I8b@i' );
define( 'AUTH_SALT',        '4:CdMr3PEpvH924&*i,Ej.hnWr~2!w29<QiV,r1Hek{=h/.}rKs,S5.1fDN_M_d<' );
define( 'SECURE_AUTH_SALT', '<nW]_-H/4MLzWk!MIlMtqd#$w22~(9J8iTF{l99ewCY)lLhfltDDRF(8+qT(.a(&' );
define( 'LOGGED_IN_SALT',   'SkUz>p@,KZoyJyV/JpW?Er.qx~tB>w*rfw^2zoQj!rd350Y=:Zk&v,*w;F@$9[X(' );
define( 'NONCE_SALT',       'Y#bqv:`0fd+/>bg}7S4+-8QHHNA-.jdQG6{ |ZYp{N:y8l+Zu;CR3LSV5>ahxGWB' );

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
$table_prefix = 'wp_av';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
