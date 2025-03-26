<?php

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

use Lkn\lknCieloForTutorLms\Includes\LknCieloForTutorLms;
use Lkn\lknCieloForTutorLms\Includes\LknCieloForTutorLmsActivator;
use Lkn\lknCieloForTutorLms\Includes\LknCieloForTutorLmsDeactivator;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

require_once __DIR__ . '/vendor/autoload.php';


if ( ! defined('LKN_CIELO_FOR_TUTOR_LMS_VERSION')) {
	define( 'LKN_CIELO_FOR_TUTOR_LMS_VERSION', '1.0.0' );
}

if ( ! defined('LKN_CIELO_FOR_TUTOR_LMS_FILE')) {
    define('LKN_CIELO_FOR_TUTOR_LMS_FILE', __DIR__ . '/rede-for-woocommerce-pro.php');
}

if ( ! defined('LKN_CIELO_FOR_TUTOR_LMS_DIR')) {
    define('LKN_CIELO_FOR_TUTOR_LMS_DIR', plugin_dir_path(LKN_CIELO_FOR_TUTOR_LMS_FILE));
}

if ( ! defined('LKN_CIELO_FOR_TUTOR_LMS_DIR_URL')) {
    define('LKN_CIELO_FOR_TUTOR_LMS_DIR_URL', plugin_dir_url(LKN_CIELO_FOR_TUTOR_LMS_FILE));
}

if ( ! defined('LKN_CIELO_FOR_TUTOR_LMS_BASENAME')) {
    define('LKN_CIELO_FOR_TUTOR_LMS_BASENAME', plugin_basename(LKN_CIELO_FOR_TUTOR_LMS_FILE));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lkn-cielo-for-tutor-lms-activator.php
 */
function activateLknCieloForTutorLms() {
	LknCieloForTutorLmsActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lkn-cielo-for-tutor-lms-deactivator.php
 */
function deactivateLknCieloForTutorLms() {
	LknCieloForTutorLmsDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activateLknCieloForTutorLms' );
register_deactivation_hook( __FILE__, 'deactivateLknCieloForTutorLms' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function runLknCieloForTutorLms() {

	$plugin = new LknCieloForTutorLms();
	$plugin->run();

}
runLknCieloForTutorLms();