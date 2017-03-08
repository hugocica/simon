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
define('DB_NAME', 'interamerican_simon');

/** MySQL database username */
define('DB_USER', 'grupoi9');

/** MySQL database password */
define('DB_PASSWORD', 'i9mysql#2305');

/** MySQL hostname */
define('DB_HOST', 'grupoi9-mysql.cpt71cfci1vq.us-east-1.rds.amazonaws.com');

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
define('AUTH_KEY',         'X/:[j+N4H~+byyMq[QVKVtsN2a]3DG$<E_EB[w-Vo[:zp]<@jvBmlpPs[r~I#;Lu');
define('SECURE_AUTH_KEY',  '?~],*>#aDduBVmlRaN&-}7%2rPt9,m>1Bw5%TZ;)D}u}+KQHO8s@<$LQehJt[(?J');
define('LOGGED_IN_KEY',    'EKf;s Qr8Y(qj<O#5]|RVgQV73Y;t){xQd!,VTS_2+K!O;M3UhVXN+nfZs>29p2H');
define('NONCE_KEY',        '20RYREKzDj+Ghr:&J?LZ2j2byh48*]Ql@ufgFtmLrv9F/GIi$nGrp,Mi3.W!Mv=[');
define('AUTH_SALT',        '//K@PFXMg2@:YZPEN{ZiWQYWo#j{NdQ@VfMmFClcYpMfH,bo^O9,d4 0qPJOtN}c');
define('SECURE_AUTH_SALT', '.VJE/ mar=<K}!q+3r1I>KYVCmhkCH%OG1KLGd*Ap<;]uW;B$oJ>E}Dc0f|[e8(z');
define('LOGGED_IN_SALT',   'OG76!V<J?@s)fZaD7yIh?nI3dl!#bIHTS`7yJ~ bPR&F,vCFW]Bit#b4dT:deD6D');
define('NONCE_SALT',       '}2!3>ZjUem0vG,;?F>Ld(q!}$i1Nx9z/BKeh^#Rb$Wy_W*!DZnb!NZBElJccRbAY');

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

define('FS_METHOD','direct');
