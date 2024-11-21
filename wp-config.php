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
define( 'DB_NAME', 'wp_6oj40' );

/** MySQL database username */
define( 'DB_USER', 'wp_qflu0' );

/** MySQL database password */
define( 'DB_PASSWORD', 'JAH7#g@$2!!95!zC' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '527]o57]5D/20:h[O%m~j|oqz]i3O30zn7YA!Q~-p*7a%]4]wJ3sR@c#@v22JL(o');
define('SECURE_AUTH_KEY', '!+2~hBp@K441P5(5/SQAc:~Jz6@e]G7])~s*8]*M@O]X!0vj/!-LiKR:e1;#*85g');
define('LOGGED_IN_KEY', 'l!NvH8U:[/N(116~6k1pX@(1N)2ho;i|k6;_06L711_nFI@2Yyxmt&N4u3b&4dZc');
define('NONCE_KEY', '2CIItI7l/1h/[3H54G6:_jT6_~qK29Qn)/EHK4pXTqS(])1RSn8k4278*03iDF/C');
define('AUTH_SALT', '8S2GB#@3ud[uLJK6etC]2@mqD)Nct2IAa(!5:Fh(aV6:|:J!8199!/!g-nd3_VS;');
define('SECURE_AUTH_SALT', '65A)5zm]QI2QB+(06B57Ld!LBx1!Dg;@6;KMn7b/H;ycZ!_1V868#G]yI+z5DNO0');
define('LOGGED_IN_SALT', 'jJ]7o|j5|C+ST988k18-n!S8vH86~GZ73nVG@Zhg!1Be2*Z#8k09aA!nOHx@)v3h');
define('NONCE_SALT', '00!96&y9bxTCESM[B4eWA!IW:U58sL&k5GeEZ3V70O(K#t5VSjD4f)189!tKtl:z');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'mABPXTUpP_';


define('WP_ALLOW_MULTISITE', true);
define('WP_AUTO_UPDATE_CORE', true);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
