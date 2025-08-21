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
 * @package    LkncftlCieloForTutorLms
 * @subpackage LkncftlCieloForTutorLms/includes
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
 * @package    LkncftlCieloForTutorLms
 * @subpackage LkncftlCieloForTutorLms/includes
 * @author     Link Nacional <contato@linknacional.com>
 */
namespace Lkncftl\lknCieloForTutorLms\Includes;

use Lkncftl\lknCieloForTutorLms\Admin\LkncftlCieloForTutorLmsAdmin;
use Lkncftl\lknCieloForTutorLms\PublicView\LkncftlCieloForTutorLmsPublic;
use Lkn_Puc_Plugin_UpdateChecker;
use Payments\Custom\LkncftlCieloForTutorLmsGateway;

class LkncftlCieloForTutorLms {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      LkncftlCieloForTutorLmsLoader    $loader    Maintains and registers all hooks for the plugin.
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

	public $LkncftlCieloForTutorLmsHelperClass;

	public function __construct() {
		if ( defined( 'LKNCFTLCIELO_FOR_TUTOR_LMS_VERSION' ) ) {
			$this->version = LKNCFTLCIELO_FOR_TUTOR_LMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'lkn-cielo-for-tutor-lms';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - LkncftlCieloForTutorLmsLoader. Orchestrates the hooks of the plugin.
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

		$this->loader = new LkncftlCieloForTutorLmsLoader();
		$this->LkncftlCieloForTutorLmsHelperClass = new LkncftlCieloForTutorLmsHelper();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new LkncftlCieloForTutorLmsAdmin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_filter( 'plugin_action_links_' . LKNCFTLCIELO_FOR_TUTOR_LMS_BASENAME, $this, 'addSettings', 10, 2);
        
		
		
	}
	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		
		$plugin_public = new LkncftlCieloForTutorLmsPublic( $this->get_plugin_name(), $this->get_version() );
		
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_filter( 'tutor_gateways_with_class', $this->LkncftlCieloForTutorLmsHelperClass, 'addWebhook', 10, 2);
		$this->loader->add_filter( 'tutor_payment_gateways_with_class', $this->LkncftlCieloForTutorLmsHelperClass, 'addGateway');
		$this->loader->add_filter( 'tutor_payment_gateways', $this->LkncftlCieloForTutorLmsHelperClass, 'setConfigs');
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
	 * @return    LkncftlCieloForTutorLmsLoader    Orchestrates the hooks of the plugin.
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

	public static function addSettings($plugin_meta, $plugin_file) {
        $new_meta_links['setting'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            admin_url('admin.php?page=tutor_settings&tab_page=ecommerce_payment'),
            __('Configurações', 'cielo-for-tutor-lms')
        );

        return array_merge($plugin_meta, $new_meta_links);
    }
}
