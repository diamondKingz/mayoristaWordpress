<?php
/**
 * Init features of the extended version
 *
 * @author YITH
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Extended' ) ) {
	/**
	 * WooCommerce Wishlist Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Extended extends YITH_WCWL {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWL_Extended
		 * @since 2.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCWL_Extended
		 * @since 2.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct();

			// emails handling.
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );
			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );
			add_filter( 'woocommerce_locate_core_template', array( $this, 'filter_woocommerce_template' ), 10, 3 );
			add_filter( 'woocommerce_locate_template', array( $this, 'filter_woocommerce_template' ), 10, 3 );

			// back in stock handling.
			add_action( 'woocommerce_product_set_stock_status', array( $this, 'schedule_back_in_stock_emails' ), 10, 3 );
			add_action( 'woocommerce_variation_set_stock_status', array( $this, 'schedule_back_in_stock_emails' ), 10, 3 );
		}

		/* === WOOCOMMERCE EMAIL METHODS === */

		/**
		 * Locate default templates of woocommerce in plugin, if exists
		 *
		 * @param string $core_file     Location of core files.
		 * @param string $template      Template to search.
		 * @param string $template_base Template base path.
		 *
		 * @return string
		 * @since  2.0.0
		 */
		public function filter_woocommerce_template( $core_file, $template, $template_base ) {
			$located = yith_wcwl_locate_template( $template );

			if ( $located ) {
				return $located;
			} else {
				return $core_file;
			}
		}

		/**
		 * Filters woocommerce available mails, to add wishlist related ones
		 *
		 * @param array $emails Array of available emails.
		 * @return array
		 * @since 2.0.0
		 */
		public function add_woocommerce_emails( $emails ) {
			$emails['YITH_WCWL_Back_In_Stock_Email'] = include YITH_WCWL_INC . 'emails/class-yith-wcwl-back-in-stock-email.php';

			return $emails;
		}

		/**
		 * Loads WC Mailer when needed
		 *
		 * @return void
		 * @since 1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function load_wc_mailer() {
			add_action( 'send_back_in_stock_mail', array( 'WC_Emails', 'send_transactional_email' ), 10, 2 );
		}

		/**
		 * Return url to unsubscribe from wishlist mailing lists
		 *
		 * @param int $user_id User id.
		 * @return string Unsubscribe url
		 * @see \YITH_WCWL_Form_Handler_Premium::unsubscribe
		 */
		public function get_unsubscribe_link( $user_id ) {
			// retrieve unique unsubscribe token.
			$unsubscribe_token            = get_user_meta( $user_id, 'yith_wcwl_unsubscribe_token', true );
			$unsubscribe_token_expiration = get_user_meta( $user_id, 'yith_wcwl_unsubscribe_token_expiration', true );

			// if user has no token, or previous token has expired, generate new unsubscribe token.
			if ( ! $unsubscribe_token || $unsubscribe_token_expiration < time() ) {
				$unsubscribe_token = wp_generate_password( 24, false, false );

				/**
				 * APPLY_FILTERS: yith_wcwl_unsubscribe_token_expiration
				 *
				 * Filter the expiration for the unsubscribe token.
				 *
				 * @param int    $token_expiration  Token expiration
				 * @param string $unsubscribe_token Unsubscribe token
				 *
				 * @return int
				 */
				$unsubscribe_token_expiration = apply_filters( 'yith_wcwl_unsubscribe_token_expiration', time() + 30 * DAY_IN_SECONDS, $unsubscribe_token );

				update_user_meta( $user_id, 'yith_wcwl_unsubscribe_token', $unsubscribe_token );
				update_user_meta( $user_id, 'yith_wcwl_unsubscribe_token_expiration', $unsubscribe_token_expiration );
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_unsubscribe_url
			 *
			 * Filter the URL to unsubscribe for the plugin emails.
			 *
			 * @param string $url                          Unsubscribe URL
			 * @param int    $user_id                      User ID
			 * @param string $unsubscribe_token            Unsubscribe token
			 * @param int    $unsubscribe_token_expiration Unsubscribe token expiration
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcwl_unsubscribe_url', add_query_arg( 'yith_wcwl_unsubscribe', $unsubscribe_token, get_home_url() ), $user_id, $unsubscribe_token, $unsubscribe_token_expiration );
		}

		/* === BACK IN STOCK HANDLING === */

		/**
		 * Schedule email sending, when an item is back in stock
		 *
		 * @param int         $product_id Product or variation id.
		 * @param string      $stock_status Product stock status.
		 * @param \WC_Product $product Current product.
		 *
		 * @return void
		 */
		public function schedule_back_in_stock_emails( $product_id, $stock_status, $product ) {
			if ( 'instock' !== $stock_status ) {
				return;
			}

			// skip if email ain't active.
			$email_options = get_option( 'woocommerce_yith_wcwl_back_in_stock_settings', array() );

			if ( ! isset( $email_options['enabled'] ) || 'yes' !== $email_options['enabled'] ) {
				return;
			}

			// skip if product is on exclusion list.
			$product_exclusions = ! empty( $email_options['product_exclusions'] ) ? array_map( 'absint', $email_options['product_exclusions'] ) : false;

			if ( $product_exclusions && in_array( $product_id, $product_exclusions, true ) ) {
				return;
			}

			// skip if product category is on exclusion list.
			$product_categories = $product->get_category_ids();

			if ( ! empty( $email_options['category_exclusions'] ) && array_intersect( $product_categories, $email_options['category_exclusions'] ) ) {
				return;
			}

			// retrieve items.
			$items = $this->get_products(
				array(
					'user_id'     => false,
					'session_id'  => false,
					'wishlist_id' => 'all',
					'product_id'  => $product_id,
				)
			);

			if ( empty( $items ) ) {
				return;
			}

			// queue handling.
			$queue        = get_option( 'yith_wcwl_back_in_stock_queue', array() );
			$unsubscribed = get_option( 'yith_wcwl_unsubscribed_users', array() );

			foreach ( $items as $item ) {
				$user    = $item->get_user();
				$user_id = $item->get_user_id();

				if ( ! $user ) {
					continue;
				}

				// skip if user unsubscribed.
				if ( in_array( $user->user_email, $unsubscribed, true ) ) {
					continue;
				}

				if ( ! isset( $queue[ $user_id ] ) ) {
					$queue[ $user_id ] = array(
						$item->get_product_id() => $item->get_id(),
					);
				} else {
					$queue[ $user_id ][ $item->get_product_id() ] = $item->get_id();
				}
			}

			update_option( 'yith_wcwl_back_in_stock_queue', $queue );
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Premium class
 *
 * @return \YITH_WCWL_Extended
 * @since 2.0.0
 */
function YITH_WCWL_Extended() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WCWL_Extended::get_instance();
}
