<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://linknacional.com.br
 * @since      1.0.0
 *
 * @package    LknCieloForTutorLms
 * @subpackage LknCieloForTutorLms/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    LknCieloForTutorLms
 * @subpackage LknCieloForTutorLms/includes
 * @author     Link Nacional <contato@linknacional.com>
 */
namespace Lkn\lknCieloForTutorLms\Includes;

use Lkn\lknCieloForTutorLms\Admin\LknCieloForTutorLmsAdmin;
use Lkn\lknCieloForTutorLms\PublicView\LknCieloForTutorLmsPublic;
use Lkn_Puc_Plugin_UpdateChecker;
use Payments\Custom\LknCieloForTutorLmsGateway;

class LknCieloForTutorLms {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      LknCieloForTutorLmsLoader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */

	public $LknCieloForTutorLmsHelperClass;

	public function __construct() {
		if ( defined( 'LKN_CIELO_FOR_TUTOR_LMS_VERSION' ) ) {
			$this->version = LKN_CIELO_FOR_TUTOR_LMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'lkn-cielo-for-tutor-lms';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->updater_init();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - LknCieloForTutorLmsLoader. Orchestrates the hooks of the plugin.
	 * - LknCieloForTutorLmsi18n. Defines internationalization functionality.
	 * - Lkn_Cielo_For_Tutor_Lms_Admin. Defines all hooks for the admin area.
	 * - Lkn_Cielo_For_Tutor_Lms_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$this->loader = new LknCieloForTutorLmsLoader();
		$this->LknCieloForTutorLmsHelperClass = new LknCieloForTutorLmsHelper();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the LknCieloForTutorLmsi18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new LknCieloForTutorLmsi18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new LknCieloForTutorLmsAdmin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_filter( 'plugin_action_links_' . LKN_CIELO_FOR_TUTOR_LMS_BASENAME, $this, 'addSettings', 10, 2);
        
		
		
	}
	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		
		$plugin_public = new LknCieloForTutorLmsPublic( $this->get_plugin_name(), $this->get_version() );
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_filter( 'tutor_gateways_with_class', $this->LknCieloForTutorLmsHelperClass, 'addWebhook', 10, 2);
		$this->loader->add_filter( 'tutor_payment_gateways_with_class', $this->LknCieloForTutorLmsHelperClass, 'addGateway');
		$this->loader->add_filter( 'tutor_payment_gateways', $this->LknCieloForTutorLmsHelperClass, 'setConfigs');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    LknCieloForTutorLmsLoader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	private function updater_init() {
        include_once __DIR__ . '/plugin-updater/plugin-update-checker.php';

        return new Lkn_Puc_Plugin_UpdateChecker(
            'https://api.linknacional.com/v2/u/?slug=tutor-lkn-cielo-for-tutor-lms',
            LKN_CIELO_FOR_TUTOR_LMS_FILE,
            'tutor-lkn-cielo-for-tutor-lms'
        );
    }

	public static function addSettings($plugin_meta, $plugin_file) {
        $new_meta_links['setting'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            admin_url('admin.php?page=tutor_settings&tab_page=ecommerce_payment'),
            __('Settings', 'woocommerce')
        );

        return array_merge($plugin_meta, $new_meta_links);
    }
}
