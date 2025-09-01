<?php

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */

use Lkncftl\lknCieloForTutorLms\Includes\LkncftlCieloForTutorLms;
use Lkncftl\lknCieloForTutorLms\Includes\LkncftlCieloForTutorLmsActivator;
use Lkncftl\lknCieloForTutorLms\Includes\LkncftlCieloForTutorLmsDeactivator;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

require_once __DIR__ . '/vendor/autoload.php';


if ( ! defined('LKNCFTLCIELO_FOR_TUTOR_LMS_VERSION')) {
	define( 'LKNCFTLCIELO_FOR_TUTOR_LMS_VERSION', '1.0.3' );
}

if ( ! defined('LKNCFTLCIELO_FOR_TUTOR_LMS_FILE')) {
    define('LKNCFTLCIELO_FOR_TUTOR_LMS_FILE', __DIR__ . '/tutor-lkn-cielo-for-tutor-lms.php');
}

if ( ! defined('LKNCFTLCIELO_FOR_TUTOR_LMS_DIR')) {
    define('LKNCFTLCIELO_FOR_TUTOR_LMS_DIR', plugin_dir_path(LKNCFTLCIELO_FOR_TUTOR_LMS_FILE));
}

if ( ! defined('LKNCFTLCIELO_FOR_TUTOR_LMS_DIR_URL')) {
    define('LKNCFTLCIELO_FOR_TUTOR_LMS_DIR_URL', plugin_dir_url(LKNCFTLCIELO_FOR_TUTOR_LMS_FILE));
}

if ( ! defined('LKNCFTLCIELO_FOR_TUTOR_LMS_BASENAME')) {
    define('LKNCFTLCIELO_FOR_TUTOR_LMS_BASENAME', plugin_basename(LKNCFTLCIELO_FOR_TUTOR_LMS_FILE));
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lkn-cielo-for-tutor-lms-activator.php
 */
function activateLkncftlCieloForTutorLms() {
	LkncftlCieloForTutorLmsActivator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lkn-cielo-for-tutor-lms-deactivator.php
 */
function deactivateLkncftlCieloForTutorLms() {
	LkncftlCieloForTutorLmsDeactivator::deactivate();
}

register_activation_hook( __FILE__, 'activateLkncftlCieloForTutorLms' );
register_deactivation_hook( __FILE__, 'deactivateLkncftlCieloForTutorLms' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function runLkncftlCieloForTutorLms() {

	$plugin = new LkncftlCieloForTutorLms();
	$plugin->run();

}
runLkncftlCieloForTutorLms();