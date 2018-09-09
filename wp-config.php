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
define('DB_NAME', 'dev_wingparent_staging');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'G@marul3z1!');

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
define('AUTH_KEY',         '4)L}$G?qdje1L`@/j}6bx`ta~gM&:6!DQPUPWn9GU#Uym1=17B2FR.YKa|];:2=H');
define('SECURE_AUTH_KEY',  'NUFqs#6+foi2F9 ~8-5bq8nqgtBB>9e`G9<RpoN9RP1&9o3ayF$x_1fa{}Expxt=');
define('LOGGED_IN_KEY',    'zR)*KtaN(oq_ftcn|ev,N#h#dn.CFQwRdQL*pwMQ&]|n0]<v`wX$3&EoiguwWbVJ');
define('NONCE_KEY',        '-MeMMYuLTJ$]iYSb7i& qbq+#jg&Dm2OVkSs{]3u#tYhed:a3$r;+XN]N0-%>*;w');
define('AUTH_SALT',        '?Wwp4P|8Wn=<)7nLMS79cS7E 4{hn@1O}$?TKE(6<9A|d*@6BI.i`;E{N2Y.<t.C');
define('SECURE_AUTH_SALT', 's@V#$5Q1m)HI *ni+ODvW{c*RBW(bIh`!{^luEXXU)sbIM>+,tTihyB1>?1U>!h5');
define('LOGGED_IN_SALT',   'V HDBW!!u~_V;$m;0 Px.aATbtQvoRt0LgYC6Y]{GzJzGb^Z)H#nncXHq0YE}Us7');
define('NONCE_SALT',       'Kk;)OfIvB&Hu5Yj;i^j)oRF4DYK71JwWYAS? }xT!;&rPJ/L,.(]BcwX`)OqDK,;');

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
define('WP_DEBUG_DISPLAY', false);
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
