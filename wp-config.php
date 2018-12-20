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
define('DB_NAME', 'conference');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'x-R{7t~p/K5-}Gl?!!3#b~a<P,o/{P[`$1dDiBu(>Q;GwhDK^Yl}vr+&= 3`1^Lg');
define('SECURE_AUTH_KEY',  '|k/r%B^e*IEuj+(b;h0i%N%HCTT!{zrFctoGXcwd$$SMusbEBs88U93gGUjRO?,,');
define('LOGGED_IN_KEY',    '^e1R#8kR$29#P;gqdadUN8!;Up52:lp?tCTDo_xT&}ga(]PE[^4dO1 pJd= =21k');
define('NONCE_KEY',        'tB(GM|-Ba@tV!xCzO<c RtR9c6I/5]0|/-ZL1k7?lw[%|FZGk{aROg~3D_REQOeA');
define('AUTH_SALT',        'mzTez8>Egziax<lJM)DtH&.+uhSRKa7sMLCfn@*%3W|T:*ukVmPd44Yp+Rt_TBr@');
define('SECURE_AUTH_SALT', '?|QftJ,vQaWI89YOGyc]:Gd_%$?>oB,`B)9[=[%rO{H~@qe`$XmnP|Vn,C:6E/Y=');
define('LOGGED_IN_SALT',   'Doq4W*N]$I-oEd@,d!jFE>wtQ*jb6MSL|[f<` I?O/et0j<pC|1#L6z=VVhW1*I(');
define('NONCE_SALT',       'SMEIj29}HCO#v*<j)eMU.xA[YFn$Sxhk(qO{S/FRBG?~V^n_=q2fd@sd5F{eips4');

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
