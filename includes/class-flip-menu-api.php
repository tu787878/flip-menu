<?php

/**
 * REST API functionality for the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/includes
 */

/**
 * REST API functionality.
 *
 * Handles all REST API endpoints for external access to shops and menus.
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/includes
 * @author     Your Name <email@example.com>
 */
class Flip_Menu_API {

	/**
	 * The plugin name.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The plugin name.
	 */
	private $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name    The name of this plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * Register REST API routes.
	 *
	 * @since    1.0.0
	 */
	public function register_routes() {
		$namespace = 'flip-menu/v1';

		// Get all shops
		register_rest_route( $namespace, '/shops', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_shops' ),
			'permission_callback' => array( $this, 'check_api_permission' ),
		) );

		// Get single shop
		register_rest_route( $namespace, '/shops/(?P<id>\d+)', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_shop' ),
			'permission_callback' => array( $this, 'check_api_permission' ),
			'args'                => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
			),
		) );

		// Get shop menu items
		register_rest_route( $namespace, '/shops/(?P<id>\d+)/menu', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_shop_menu' ),
			'permission_callback' => array( $this, 'check_api_permission' ),
			'args'                => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
			),
		) );

		// Get complete shop data (shop + menu items)
		register_rest_route( $namespace, '/shops/(?P<id>\d+)/complete', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'get_shop_complete' ),
			'permission_callback' => array( $this, 'check_api_permission' ),
			'args'                => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
			),
		) );

		// Verify API key
		register_rest_route( $namespace, '/verify', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'verify_api_key' ),
			'permission_callback' => array( $this, 'check_api_permission' ),
		) );
	}

	/**
	 * Check API permission (API key validation).
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request    $request    The request object.
	 * @return   bool|WP_Error
	 */
	public function check_api_permission( $request ) {
		// Check if API is enabled
		$api_enabled = get_option( 'flip_menu_api_enabled', false );

		if ( ! $api_enabled ) {
			return new WP_Error(
				'api_disabled',
				__( 'API access is disabled', 'flip-menu' ),
				array( 'status' => 403 )
			);
		}

		// Get API key from header or query parameter
		$api_key = $request->get_header( 'X-API-Key' );

		if ( ! $api_key ) {
			$api_key = $request->get_param( 'api_key' );
		}

		// Get stored API key
		$stored_key = get_option( 'flip_menu_api_key', '' );

		// If no API key is set, allow public access
		if ( empty( $stored_key ) ) {
			return true;
		}

		// Validate API key
		if ( $api_key !== $stored_key ) {
			return new WP_Error(
				'invalid_api_key',
				__( 'Invalid API key', 'flip-menu' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Get all shops.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request    $request    The request object.
	 * @return   WP_REST_Response
	 */
	public function get_shops( $request ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'flip_menu_shops';

		$shops = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC" );

		if ( empty( $shops ) ) {
			return new WP_REST_Response( array(
				'success' => true,
				'data'    => array(),
				'message' => __( 'No shops found', 'flip-menu' )
			), 200 );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data'    => $shops,
			'count'   => count( $shops )
		), 200 );
	}

	/**
	 * Get single shop.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request    $request    The request object.
	 * @return   WP_REST_Response
	 */
	public function get_shop( $request ) {
		global $wpdb;
		$shop_id = intval( $request['id'] );
		$table_name = $wpdb->prefix . 'flip_menu_shops';

		$shop = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table_name WHERE id = %d",
			$shop_id
		) );

		if ( ! $shop ) {
			return new WP_REST_Response( array(
				'success' => false,
				'message' => __( 'Shop not found', 'flip-menu' )
			), 404 );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data'    => $shop
		), 200 );
	}

	/**
	 * Get shop menu items.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request    $request    The request object.
	 * @return   WP_REST_Response
	 */
	public function get_shop_menu( $request ) {
		global $wpdb;
		$shop_id = intval( $request['id'] );
		$items_table = $wpdb->prefix . 'flip_menu_items';

		$items = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $items_table WHERE shop_id = %d ORDER BY page_order ASC",
			$shop_id
		) );

		if ( empty( $items ) ) {
			return new WP_REST_Response( array(
				'success' => true,
				'data'    => array(),
				'message' => __( 'No menu items found', 'flip-menu' )
			), 200 );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data'    => $items,
			'count'   => count( $items )
		), 200 );
	}

	/**
	 * Get complete shop data (shop + menu items).
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request    $request    The request object.
	 * @return   WP_REST_Response
	 */
	public function get_shop_complete( $request ) {
		global $wpdb;
		$shop_id = intval( $request['id'] );
		$shops_table = $wpdb->prefix . 'flip_menu_shops';
		$items_table = $wpdb->prefix . 'flip_menu_items';

		// Get shop
		$shop = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $shops_table WHERE id = %d",
			$shop_id
		) );

		if ( ! $shop ) {
			return new WP_REST_Response( array(
				'success' => false,
				'message' => __( 'Shop not found', 'flip-menu' )
			), 404 );
		}

		// Get menu items
		$items = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $items_table WHERE shop_id = %d ORDER BY page_order ASC",
			$shop_id
		) );

		return new WP_REST_Response( array(
			'success' => true,
			'data'    => array(
				'shop'  => $shop,
				'items' => $items,
				'count' => count( $items )
			)
		), 200 );
	}

	/**
	 * Verify API key.
	 *
	 * @since    1.0.0
	 * @param    WP_REST_Request    $request    The request object.
	 * @return   WP_REST_Response
	 */
	public function verify_api_key( $request ) {
		return new WP_REST_Response( array(
			'success' => true,
			'message' => __( 'API key is valid', 'flip-menu' )
		), 200 );
	}

	/**
	 * Add CORS headers for external access.
	 *
	 * @since    1.0.0
	 */
	public function add_cors_headers() {
		$allow_cors = get_option( 'flip_menu_api_cors_enabled', true );

		if ( ! $allow_cors ) {
			return;
		}

		$allowed_origins = get_option( 'flip_menu_api_allowed_origins', '*' );

		header( 'Access-Control-Allow-Origin: ' . $allowed_origins );
		header( 'Access-Control-Allow-Methods: GET, OPTIONS' );
		header( 'Access-Control-Allow-Headers: X-API-Key, Content-Type' );
		header( 'Access-Control-Allow-Credentials: true' );
	}

}
