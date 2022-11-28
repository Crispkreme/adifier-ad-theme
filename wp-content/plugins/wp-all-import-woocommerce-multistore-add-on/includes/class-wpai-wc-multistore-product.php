<?php
/**
 * Product functionality.
 *
 * This handles product related functionality.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WPAI_WC_Multistore_Product
 */
class WPAI_WC_Multistore_Product{

	public function __construct(){
		$this->hooks();
	}

	public function hooks(){
		add_action( 'pmwi_tab_header', array( $this, 'tab_header' ) );
		add_action( 'pmwi_tab_content', array( $this, 'tab_content' ) );
		add_filter( 'pmxi_options_options', array( $this, 'pmxi_options_options' ) );

		if( ! $this->is_importing() ){
			return;
		}

		$this->remove_wc_multistore_update_product();
		add_action( 'pmxi_saved_post', array( $this, 'pmxi_saved_post' ), PHP_INT_MAX );
		add_action( 'wp_all_import_make_product_simple', array( $this, 'wp_all_import_make_product_simple' ), PHP_INT_MAX );

		if ( is_multisite() ) {
			add_filter( 'woocommerce_product_type_query', array( $this, 'woocommerce_product_type_query' ), 10, 2 );
			add_filter( 'WOO_MSTORE_admin_product/master_product_meta_to_update', array( $this, 'master_product_meta_to_update' ) );
			add_filter( 'WOO_MSTORE_admin_product/slave_product_meta_to_update', array( $this, 'slave_product_meta_to_update' ), 20, 2 );
			add_filter( 'WOO_MSTORE_admin_product/is_product_inherit_updates', array( $this, 'is_product_inherit_updates' ), 20, 2 );
			add_filter( 'WOO_MSTORE_admin_product/is_product_stock_synchronize', array( $this, 'is_product_stock_synchronize' ), 20, 2 );
		}
	}


	/**
	 * Remove WC Multistore woocommerce_update_product
	 */
	public function remove_wc_multistore_update_product(){
		global $wp_filter;
		if( ! empty($wp_filter['woocommerce_update_product']) ){
			if( ! empty($wp_filter['woocommerce_update_product']->callbacks) ){
				foreach ( $wp_filter['woocommerce_update_product']->callbacks as $priority => $value ){
					foreach ( $value as $key => $nvalue ){
						if( $nvalue['function'][0] instanceof WC_Multistore_Product ){
							unset( $wp_filter['woocommerce_update_product']->callbacks[$priority][$key] );
						}
					}
				}
			}
		}
	}

	/**
	 * @param $meta_data
	 *
	 * @return mixed
	 */
	public function master_product_meta_to_update( $meta_data ) {
		if ( doing_filter( 'WOO_MSTORE_admin_product/process_product' ) ) {
			$options = $this->get_options(); // for the admin interface.

			foreach ( $options as $key => $value ) {
				if ( preg_match( '/^_woonet_publish_to_\d+$/', $key ) ) {
					if( !empty( $value ) ){
						$meta_data[ $key ] = $value;
					}
				}
			}
		}
		return $meta_data;
	}

	/**
	 * @param $meta
	 *
	 * @return mixed
	 */
	public function slave_product_meta_to_update($meta, $data){
		if( empty($meta['_woonet_child_inherit_updates']) ){
			unset($meta['_woonet_child_inherit_updates']);
		}

		if( empty($meta['_woonet_child_stock_synchronize']) ){
			unset($meta['_woonet_child_stock_synchronize']);
		}

		switch_to_blog($data['master_product_blog_id']);
		$options = $this->get_options();
		restore_current_blog();

		// remove lingering meta keys if options are disabled for site. Otherwise, custom meta is deleted from child products
		if( $options['_woonet_publish_to_'.get_current_blog_id()] == 'no' || $options['_woonet_publish_to_'.get_current_blog_id().'_child_inheir'] == 'no' ){
			foreach ( $meta as $key => $value ){
				if( false === strpos($key,'_woonet') ){
					unset($meta[$key]);
				}
			}
		}

		return $meta;
	}

	/**
	 * @param $result
	 *
	 * @return mixed
	 */
	public function is_product_inherit_updates( $result, $data ) {
		if ( doing_filter( 'WOO_MSTORE_admin_product/process_product' ) ) {
			switch_to_blog( get_post_meta($data->get_id(), '_woonet_network_is_child_site_id', true ) );
			$options = $this->get_options();
			restore_current_blog();

			if ( isset( $options[ '_woonet_publish_to_' . get_current_blog_id() . '_child_inheir' ] ) ) {
				$result = $options[ '_woonet_publish_to_' . get_current_blog_id() . '_child_inheir' ];
			} elseif ( isset( $options['_woonet_child_inherit_updates'] ) ) {
				$result = $options['_woonet_child_inherit_updates'];
			}
		}

		return $result;
	}

	/**
	 * @param $result
	 *
	 * @return mixed
	 */
	public function is_product_stock_synchronize( $result, $data ) {
		if ( doing_filter( 'WOO_MSTORE_admin_product/process_product' ) ) {
			switch_to_blog( get_post_meta($data->get_id(), '_woonet_network_is_child_site_id', true ) );
			$options = $this->get_options();
			restore_current_blog();

			if ( isset( $options[ '_woonet_' . get_current_blog_id() . '_child_stock_synchronize' ] ) ) {
				$result = $options[ '_woonet_' . get_current_blog_id() . '_child_stock_synchronize' ];
			} elseif ( isset( $options['_woonet_child_stock_synchronize'] ) ) {
				$result = $options['_woonet_child_stock_synchronize'];
			}
		}

		return $result;
	}

	/**
	 *
	 */
	public function tab_header() {
		printf(
			'<li class="woonet_tab"><a href="#woonet_data" rel="woonet_data"><span>%s</span></a></li>',
			__( 'MultiStore', 'woonet' )
		);
	}

	/**
	 *
	 */
	public function tab_content() {
		include( WPAI_WC_MULTISTORE_ABSPATH. '/views/admin/import/product/_tabs/_wc-multistore.php' );
	}


	/**
	 * @param $options
	 *
	 * @return mixed
	 */
	public function pmxi_options_options( $options ) {
		if ( ! isset( $_POST['is_submitted'] ) ) {
			return $options;
		}

		$options['_woonet_global_publish_to'] = $_POST['_woonet_global_publish_to'];
		$options['_woonet_global_inherit'] = $_POST['_woonet_global_inherit'];
		$options['_woonet_global_stock'] = $_POST['_woonet_global_stock'];

		if( is_multisite() ){
			if( class_exists('WOO_MSTORE_functions') ){
				$sites = WOO_MSTORE_functions::get_active_woocommerce_blog_ids();
			}else{
				$sites = WC_Multistore_Functions::get_active_woocommerce_blog_ids();
			}
		}else{
			$sites = get_option('woonet_child_sites');
		}

		foreach ($sites as $site) {
			if ( is_multisite() ) {
				if ( $site == get_current_blog_id() ) {
					continue;
				}
				$site_id = $site;
			} else {
				$site_id   = $site['uuid'];
			}

			$options['_woonet_publish_to_'.$site_id] = $_POST['_woonet_publish_to_'.$site_id];
			$options['_woonet_publish_to_'.$site_id.'_child_inheir'] = $_POST['_woonet_publish_to_'.$site_id.'_child_inheir'];
			$options['_woonet_'.$site_id.'_child_stock_synchronize'] = $_POST['_woonet_'.$site_id.'_child_stock_synchronize'];
		}


		return $options;
	}

	/**
	 * @param $product_id
	 */
	public function wp_all_import_make_product_simple( $product_id ) {
		if ( is_multisite() ) {
			do_action( 'WOO_MSTORE_admin_product/process_product', $product_id );
		} else {
			// Single site product sync.
			$_REQUEST['post_ID'] = $product_id;
			WOO_MULTISTORE()->product->quick_sync();
		}
	}

	/**
	 * @param $product_id
	 */
	public function pmxi_saved_post( $product_id ) {
		if ( $product = wc_get_product( $product_id ) ) {
			if ( $product_parent_id = $product->get_parent_id() ) {
				$product_id = $product->get_parent_id();
			}

			if ( is_multisite() ) {
				do_action( 'WOO_MSTORE_admin_product/process_product', $product_id );
			} else {
				// single site.
				$sites   = get_option( 'woonet_child_sites' );
				$options = $this->get_options();

				foreach ( $sites as $site ) {
					if ( !empty( $options[ '_woonet_publish_to_' . $site['uuid'] ] ) ) {
						$_REQUEST[ '_woonet_publish_to_' . $site['uuid'] ] = $options[ '_woonet_publish_to_' . $site['uuid'] ];
					}else{
						$_REQUEST[ '_woonet_publish_to_' . $site['uuid'] ] = get_post_meta($product_id,'_woonet_publish_to_' . $site['uuid'], true );
					}

					if ( !empty( $options[ '_woonet_publish_to_' . $site['uuid'] . '_child_inheir' ] ) ) {
						$_REQUEST[ '_woonet_publish_to_' . $site['uuid'] . '_child_inheir' ] = $options[ '_woonet_publish_to_' . $site['uuid'] . '_child_inheir' ];
					}else{
						$_REQUEST[ '_woonet_publish_to_' . $site['uuid'] . '_child_inheir' ] = get_post_meta($product_id,'_woonet_publish_to_' . $site['uuid'] . '_child_inheir', true );
					}

					if ( !empty( $options[ '_woonet_' . $site['uuid'] . '_child_stock_synchronize' ] )) {
						$_REQUEST[ '_woonet_' . $site['uuid'] . '_child_stock_synchronize' ] = $options[ '_woonet_' . $site['uuid'] . '_child_stock_synchronize' ];
					}else{
						$_REQUEST[ '_woonet_' . $site['uuid'] . '_child_stock_synchronize' ] = get_post_meta($product_id,'_woonet_' . $site['uuid'] . '_child_stock_synchronize', true );
					}
				}
				// Single site product sync.
				$_REQUEST['post_ID'] = $product_id;
				WOO_MULTISTORE()->product->quick_sync();
			}
		}
	}

	/**
	 * @param $product_type
	 * @param $product_id
	 *
	 * @return false|mixed|string
	 */
	public function woocommerce_product_type_query( $product_type, $product_id ) {
		if ( doing_action( 'WOO_MSTORE_admin_product/process_product' ) ) {
			global $wpdb;

			$query  = "SELECT p.post_type AS post_type, t.name AS product_type
			FROM {$wpdb->posts} AS p
			JOIN {$wpdb->term_relationships} AS tr ON tr.object_id = p.ID
			JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
			JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
			WHERE p.ID={$product_id} AND tt.taxonomy='product_type'";
			$result = $wpdb->get_row( $query );

			if ( empty( $result->post_type ) ) {
				$product_type = false;
			} elseif ( 'product_variation' == $result->post_type ) {
				$product_type = 'variation';
			} else {
				$product_type = $result->product_type;
			}
		}

		return $product_type;
	}

	/**
	 * Get import options from session
	 */
	private function get_options() {
		if ( ! empty( PMXI_Plugin::$session ) ) {
			$options = PMXI_Plugin::$session->options; // not available when running via cron.
		} else {
			$import  = new PMXI_Import_Record();
			$options = $import->getById( $_GET['import_id'] );
			$options = $options->options;
		}

		return $options;
	}

	/**
	 * @return bool
	 */
	public function is_importing(){
		// Importing from dashboard
		if( ! empty($_REQUEST['page'] ) && $_REQUEST['page'] == 'pmxi-admin-import' && ! empty($_REQUEST['action'] ) && $_REQUEST['action'] == 'process' ) {
			return true;
		}
		// Importing by scheduler
		if( ! empty($_REQUEST['import_key']) ){
			return true;
		}

		return false;
	}
}