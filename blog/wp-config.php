<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'magento');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         ',i|+r?$+{@ aT?(8%8J>|tx_m|ZX%U+!/%9&F%+yp<q6]a*5wh%#} p|_5*<[Gn@');
define('SECURE_AUTH_KEY',  'p~N-7g/DLv0lg}X{X(<65)~>Afk%1b3<-?|.-,jlIlOrWR(n?|DveBv^}y<$gbtE');
define('LOGGED_IN_KEY',    'iKi0z>s@pL-y<R`RZ}VJHZ3b4dy{(NU5|_)MVtK|O1=^a+J~<q5],Bz)82OC4Qj+');
define('NONCE_KEY',        ')HG_5^>dgZf;Rr+0?eFEGmFOQm0[M6-mx^IYg>=otgE#|,ok#F{Du-nE]3(c*UUS');
define('AUTH_SALT',        '!|p 8+1$h5laQ>lG}pFI.yedBG-iA~6GgvFtuX7Phfq/X!L74B|Kj[3QRq_<sgI`');
define('SECURE_AUTH_SALT', 'z4yk|;TylpxLc *(j|o%TKLvrYfgO<}t.|)-U&j}CRf&q(J/ko+acP>jK|Hc,;mC');
define('LOGGED_IN_SALT',   ':IQt.+1nCtA+2XLcaI&J=>fYH(q&@*80h{<!cYc/*sLHIw/Z+,v,^z2|z6_>{Qy|');
define('NONCE_SALT',       'ox)] |8H}!1A,kD]LSP|y(X*3ZJruv`^6 ~e$b6AJ&~0LgYp80|il(&L0/gA#:2}');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
