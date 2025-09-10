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
define( 'DB_NAME', 'bitnami_wordpress' );

/** Database username */
define( 'DB_USER', 'bn_wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'V4tgVN7BP8tzrVaIVlZ6GTYMKknRoFnL' );

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
define( 'AUTH_KEY',          '=ui[RRq9.Go4F5U+7&J1@OY[6qM|w;YKeu7`Ud6C,/Jz3n]k0e+ffU@Y-v(^TDSI' );
define( 'SECURE_AUTH_KEY',   '_J|-j#k(3Tj8c}w@i2%%-!N$?DCM#c]pm?iXy(}86<f)&Bw,a:WKHJ#xv=>v0%@J' );
define( 'LOGGED_IN_KEY',     '@/t:=hjC],ddm`g#O6G!:[fdn8oLfgm%>7QrKFC%NOJ~v;lMA2qU8(URYfy//#F%' );
define( 'NONCE_KEY',         '2[qp5N%5;,oLG/D[Y{CcNzKQM&#qUy< P>2?uWQ70&yRvaeqNnK7$,Fcw)R.#yas' );
define( 'AUTH_SALT',         'HxGO[d.BY) GR2BPKnPXkoj6x^5Gwr8QOFq3TsH;i>)CSZZ|c*q}Yu8N@4Pl:Vh<' );
define( 'SECURE_AUTH_SALT',  'lT#;Q=u4+=A-h2~B8HPPv@kk^WoH9#WV0(S59c/%_2HrYZkv&%/wK,S,n412v<s?' );
define( 'LOGGED_IN_SALT',    'JU<v+M9qPwoU_|nlr/lJtRAVlGd;$jwZ:ZU+p%G.XRx.Gblh.6{%io:=m9flC fB' );
define( 'NONCE_SALT',        '#WL6EU]?ekIw-hbfJK8oDpYd6X%C%B,m*pcH}t1/k*bG$kJ:]KM:nd?va3Ntc{A(' );
define( 'WP_CACHE_KEY_SALT', '6Ewt%AZ;Po,6Gsgum?,fYJbb)#xZWoOmGZ4N$W{qB!XXrgR*@hJ~2:FS:#aW%$1)' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


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

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
