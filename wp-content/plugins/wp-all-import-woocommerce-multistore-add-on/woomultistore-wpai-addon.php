<?php
/*
* Plugin Name: WP All Import - WooCommerce Multistore Add-On
* Plugin URI: http://www.lykkemedia.no
* Description: Import to WooCommerce. Adds integration with WooCommerce Multistore. Requires WP All Import.
* Version: 2.1.1
* Author: Lykke Media AS
* Requires at least: 5.3.0
* WC tested up to: 6.3.1
* Tested up to: 6.0.1
*/

defined( 'ABSPATH' ) || exit;

/**
 * Class WPAI_WM_Add_On
 */
final class WPAI_WM_Add_On {

	protected static $_instance = null;

	/**
	 * Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'woonet' ), '2.0.4' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woonet' ), '2.0.4' );
	}

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->hooks();
	}

	/**
	 * Defines constants
	 */
	public function define_constants() {
		$this->define( 'WPAI_WC_MULTISTORE_ABSPATH', dirname( __FILE__ ) . '/' );
		$this->define( 'WPAI_WC_MULTISTORE_URL', plugins_url( '', __FILE__ ) );
		$this->define( 'WPAI_WC_MULTISTORE_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'WPAI_WC_MULTISTORE_SLUG', plugin_basename( __DIR__ ) );
		$this->define( 'WPAI_WC_MULTISTORE_API_URL', 'https://woomultistore.com/index.php' );
		$this->define( 'WPAI_WC_MULTISTORE_CHANGELOG_URL', 'https://swphdev.com/sandbox/tonny/woomultistore/dev/multisite/wp-content/uploads/woocommerce_uploads/2022/06/readme-7zbbvy.txt' );
		$this->define( 'WPAI_WC_MULTISTORE_PRODUCT_UNIQUE_ID', 'WPAI-Add-On-1' );
		$this->define( 'WPAI_WC_MULTISTORE_VERSION', '2.1.1' );
		$this->define( 'WPAI_WC_MULTISTORE_DOMAIN', str_replace( array( 'https://', 'http://' ), '', network_site_url() ) );
	}

	/**
	 * Includes
	 */
	public function includes(){
		require_once( WPAI_WC_MULTISTORE_ABSPATH . 'includes/class-wpai-wc-multistore-product.php' );
	}

	/**
	 * Hooks
	 */
	public function hooks(){
		add_action( 'plugins_loaded', array( $this, 'dependencies' ), 20 );
		add_action( 'init', array( $this, 'init' ), 20 );
	}

	/**
	 * Init
	 */
	public function init(){
		$GLOBALS['WPAI_WC_Multistore_Product'] = new WPAI_WC_Multistore_Product();
	}

	public function dependencies(){
		if( ! class_exists('PMXI_Plugin' ) || ( ! class_exists('WOO_MSTORE_MULTI_INIT') && ! class_exists('WOO_MSTORE_SINGLE_MAIN') ) ){
			add_action( 'admin_notices', array( $this, 'admin_notice' ), 10, 0 );
			add_action( 'network_admin_notices', array( $this, 'admin_notice' ), 10, 0 );
			deactivate_plugins(WPAI_WC_MULTISTORE_BASENAME);
		}
	}

	public function admin_notice() {
		$class   = 'notice notice-error';
		$message = __( 'WP All Import Woocommerce Multistore Addon requires Woocommerce Multistore and WP All Import to be activated', 'woonet' );

		printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			esc_attr( $class ),
			esc_html( $message )
		);
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}

function WPAI_WM_Add_On(){
	return WPAI_WM_Add_On::instance();
}

$GLOBALS['WPAI_WM_Add_On'] = WPAI_WM_Add_On();