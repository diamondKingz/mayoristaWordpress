<?php
/**
 * Init extended admin features of the plugin
 *
 * @author YITH
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Admin_Extended' ) ) {
	/**
	 * WooCommerce Wishlist admin Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Admin_Extended extends YITH_WCWL_Admin {

		/**
		 * Various links
		 *
		 * @var string
		 * @access public
		 * @since 1.0.0
		 */
		public $showcase_images = array();

		/**
		 * Constructor of the class
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			parent::__construct();

			// add premium settings.
			add_filter( 'yith_wcwl_wishlist_page_options', array( $this, 'add_wishlist_options' ) );
		}

		/* === INITIALIZATION SECTION === */

		/**
		 * Initiator method. Initiate properties.
		 *
		 * @return void
		 * @access private
		 * @since 1.0.0
		 */
		public function init() {
			parent::init();

			// add new tabs.
			/**
			 * APPLY_FILTERS: yith_wcwl_available_admin_tabs_extended
			 *
			 * Filter the available tabs in the plugin panel.
			 *
			 * @param array $tabs Admin tabs
			 *
			 * @return array
			 */
			$this->available_tabs = apply_filters(
				'yith_wcwl_available_admin_tabs_extended',
				yith_wcwl_merge_in_array(
					$this->available_tabs,
					array(
						'popular'         => __( 'Popular', 'yith-woocommerce-wishlist' ),
						'promotion_email' => __( 'Promotional', 'yith-woocommerce-wishlist' ),
					),
					'wishlist_page'
				)
			);
		}

		/**
		 * Add new options to wishlist settings tab
		 *
		 * @param array $options Array of available options.
		 * @return array Filtered array of options
		 */
		public function add_wishlist_options( $options ) {
			$settings = $options['wishlist_page'];

			$settings = yith_wcwl_merge_in_array(
				$settings,
				array(
					'show_quantity' => array(
						'name'          => __( 'In wishlist table show', 'yith-woocommerce-wishlist' ),
						'desc'          => __( 'Product quantity (so users can manage the quantity of each product from the wishlist)', 'yith-woocommerce-wishlist' ),
						'id'            => 'yith_wcwl_quantity_show',
						'type'          => 'checkbox',
						'default'       => '',
						'checkboxgroup' => 'wishlist_info',
					),
				),
				'show_unit_price'
			);

			$options['wishlist_page'] = $settings;

			return $options;
		}

		/* === PANEL HANDLING === */

		/**
		 * Adds params to use in admin template files
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function print_popular_table() {
			if ( isset( $_GET['action'] ) && 'send_promotional_email' === $_GET['action'] && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'send_promotional_email' ) ) {
				$emails          = WC_Emails::instance()->get_emails();
				$promotion_email = $emails['YITH_WCWL_Promotion_Email'];

				$additional_info['current_tab'] = 'popular';
				$additional_info['product_id']  = isset( $_REQUEST['product_id'] ) ? intval( $_REQUEST['product_id'] ) : false;

				$additional_info['promotional_email_html_content'] = $promotion_email->get_option( 'content_html' );
				$additional_info['promotional_email_text_content'] = $promotion_email->get_option( 'content_text' );

				$additional_info['coupons'] = get_posts(
					array(
						'post_type'      => 'shop_coupon',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
					)
				);

				yith_wcwl_get_template( 'admin/wishlist-panel-send-promotional-email.php', $additional_info );
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Admin_Extended class
 *
 * @return \YITH_WCWL_Admin
 * @since 2.0.0
 */
function YITH_WCWL_Admin_Extended() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WCWL_Admin_Extended::get_instance();
}
