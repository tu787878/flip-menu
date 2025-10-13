<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and handles the shortcode
 * and front-end display of flip menus.
 *
 * @package    Flip_Menu
 * @subpackage Flip_Menu/public
 * @author     Your Name <email@example.com>
 */
class Flip_Menu_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/flip-menu-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Enqueue Turn.js library
		wp_enqueue_script( 'turnjs', plugin_dir_url( __FILE__ ) . 'js/turn.min.js', array( 'jquery' ), '4.1.0', true );

		// Enqueue plugin script
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/flip-menu-public.js', array( 'jquery', 'turnjs' ), $this->version, true );

	}

	/**
	 * Register the shortcode.
	 *
	 * @since    1.0.0
	 */
	public function register_shortcode() {
		add_shortcode( 'flip_menu', array( $this, 'flip_menu_shortcode' ) );
	}

	/**
	 * Shortcode callback function.
	 *
	 * @since    1.0.0
	 * @param    array    $atts    Shortcode attributes.
	 * @return   string   HTML output for the flip menu.
	 */
	public function flip_menu_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'shop_id' => 0,
			'width' => '800',
			'height' => '600',
		), $atts );

		$shop_id = intval( $atts['shop_id'] );

		if ( ! $shop_id ) {
			return '<p>' . __( 'Please provide a valid shop_id', 'flip-menu' ) . '</p>';
		}

		global $wpdb;
		$items_table = $wpdb->prefix . 'flip_menu_items';
		$shops_table = $wpdb->prefix . 'flip_menu_shops';

		// Get shop details
		$shop = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $shops_table WHERE id = %d",
			$shop_id
		) );

		if ( ! $shop ) {
			return '<p>' . __( 'Shop not found', 'flip-menu' ) . '</p>';
		}

		// Get menu items
		$items = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $items_table WHERE shop_id = %d ORDER BY page_order ASC",
			$shop_id
		) );

		if ( empty( $items ) ) {
			return '<p>' . __( 'No menu items found for this shop', 'flip-menu' ) . '</p>';
		}

		// Generate unique ID for this instance
		$unique_id = 'flip-menu-' . uniqid();

		ob_start();
		?>
		<div class="flip-menu-container" style="max-width: <?php echo esc_attr( $atts['width'] ); ?>px; margin: 0 auto;">
			<h2 class="flip-menu-title"><?php echo esc_html( $shop->name ); ?></h2>
			<?php if ( $shop->description ) : ?>
				<p class="flip-menu-description"><?php echo esc_html( $shop->description ); ?></p>
			<?php endif; ?>

			<div id="<?php echo esc_attr( $unique_id ); ?>" class="flip-menu" style="width: <?php echo esc_attr( $atts['width'] ); ?>px; height: <?php echo esc_attr( $atts['height'] ); ?>px;">
				<?php foreach ( $items as $index => $item ) : ?>
					<div class="page" data-page="<?php echo $index + 1; ?>">
						<?php if ( $item->source_type === 'image' ) : ?>
							<img src="<?php echo esc_url( $item->source_url ); ?>" alt="<?php echo esc_attr( $item->title ); ?>" style="width: 100%; height: 100%; object-fit: contain;" />
						<?php elseif ( $item->source_type === 'pdf' ) : ?>
							<div class="pdf-notice">
								<p><?php _e( 'PDF Menu - ', 'flip-menu' ); ?><a href="<?php echo esc_url( $item->source_url ); ?>" target="_blank"><?php _e( 'View Full PDF', 'flip-menu' ); ?></a></p>
								<p class="description"><?php _e( 'Note: PDF pages need to be converted to images for flip display', 'flip-menu' ); ?></p>
							</div>
						<?php endif; ?>
						<div class="page-number"><?php echo $index + 1; ?></div>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="flip-menu-controls" style="text-align: center; margin-top: 20px;">
				<button class="flip-menu-btn flip-menu-prev">&larr; <?php _e( 'Previous', 'flip-menu' ); ?></button>
				<button class="flip-menu-btn flip-menu-next"><?php _e( 'Next', 'flip-menu' ); ?> &rarr;</button>
			</div>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			var flipbook = $('#<?php echo esc_js( $unique_id ); ?>');

			flipbook.turn({
				width: <?php echo intval( $atts['width'] ); ?>,
				height: <?php echo intval( $atts['height'] ); ?>,
				autoCenter: true,
				display: 'double',
				acceleration: true,
				gradients: true,
				elevation: 50,
				when: {
					turned: function(event, page, view) {
						console.log('Current view: ', view);
					}
				}
			});

			// Previous button
			$('.flip-menu-prev').on('click', function() {
				flipbook.turn('previous');
			});

			// Next button
			$('.flip-menu-next').on('click', function() {
				flipbook.turn('next');
			});

			// Keyboard navigation
			$(document).on('keydown', function(e) {
				if (e.keyCode === 37) { // Left arrow
					flipbook.turn('previous');
				} else if (e.keyCode === 39) { // Right arrow
					flipbook.turn('next');
				}
			});
		});
		</script>
		<?php
		return ob_get_clean();
	}

}
