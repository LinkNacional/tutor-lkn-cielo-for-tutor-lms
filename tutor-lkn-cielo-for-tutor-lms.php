<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://linknacional.com.br
 * @since             1.0.0
 * @package           Lkn_Cielo_For_Tutor_Lms
 *
 * @wordpress-plugin
 * Plugin Name:       Cielo For Tutor Lms
 * Plugin URI:        https://linknacional.com.br/wordpress
 * Description:       This is a description of the plugin.
 * Version:           1.0.1
 * Author:            Link Nacional
 * Author URI:        https://linknacional.com.br/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cielo-for-tutor-lms
 * Requires Plugins:  tutor
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
require_once 'tutor-lkn-cielo-for-tutor-lms-file.php';