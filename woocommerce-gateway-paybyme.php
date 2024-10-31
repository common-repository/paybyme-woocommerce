<?php
/*
Plugin Name: PaybyMe WooCommerce
Plugin URI:  https://wordpress.org/plugins/paybyme-woocommerce
Description: PaybyMe payment gateway for WooCommerce
Version:     1.1.7
Author:      PaybyMe
Author URI:  https://www.payby.me/
Text Domain: woocommerce-paybyme
Domain Path: /i18n/languages/
License:     GPL2
WC requires at least: 3.0
WC tested up to: 4.0
 
{Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
*/
if (!defined('ABSPATH')) {
    exit;
}
if ( ! class_exists( 'PaybyMe_CheckoutForm_For_WooCommerce' ) ) {

	class PaybyMe_CheckoutForm_For_WooCommerce {

		protected static $instance;
       
        public static function get_instance() {

            if ( null === self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
		}
		
		protected function __construct() {
            add_action('plugins_loaded', array($this,'init'));
		}
		
		public function init() {
            $this->InitPaybyMePaymentGateway();
		}

		public static function installLanguage() {
			load_plugin_textdomain('woocommerce-paybyme',false,plugin_basename(dirname(__FILE__)) . '/i18n/languages/');
		}
		

		public function InitPaybyMePaymentGateway() {

            if ( ! class_exists('WC_Payment_Gateway')) {
                return;
            }

			include_once untrailingslashit( plugin_dir_path( __FILE__ )).'/includes/wc-paybyme-woocommerce-gateway.php';
			include_once untrailingslashit( plugin_dir_path( __FILE__ )).'/includes/wc-paybyme-form-fields.php';
			include_once untrailingslashit( plugin_dir_path( __FILE__ )).'/includes/wc-paybyme-generate-form.php';
			include_once untrailingslashit( plugin_dir_path( __FILE__ )).'/includes/wc-paybyme-get-payment-link.php';
           
            add_filter('woocommerce_payment_gateways',array($this,'woocommerce_add_paybyme_gateway'));
		}
		
		function woocommerce_add_paybyme_gateway($methods) {
			$methods[] = 'WC_PaybyMe';	
			return $methods;
		}

		function CreatePbmTable() {			
			global $wpdb;
			$tableName = $wpdb->prefix . 'pbm_preorder';
		
			$charset_collate = $wpdb->get_charset_collate();
		
			$sql = "CREATE TABLE IF NOT EXISTS $tableName (
				orderId INT(11) NOT NULL AUTO_INCREMENT,
				woocommerceOrderId  INT(11) NOT NULL,
				pbmLongSyncId VARCHAR(36) NOT NULL,                
				createDate  TIMESTAMP DEFAULT current_timestamp,
			  PRIMARY KEY (orderId)
			) $charset_collate;";
		
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}

		function DropPbmTable() {			
			global $wpdb;
			$tableName = $wpdb->prefix . 'pbm_preorder';
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "DROP TABLE IF EXISTS $tableName;";		
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta($sql);
		}
	}
PaybyMe_CheckoutForm_For_WooCommerce::get_instance();
add_action('plugins_loaded',array('PaybyMe_CheckoutForm_For_WooCommerce','installLanguage'));
register_activation_hook(__FILE__, array('PaybyMe_CheckoutForm_For_WooCommerce','CreatePbmTable'));
register_deactivation_hook( __FILE__,array('PaybyMe_CheckoutForm_For_WooCommerce','DropPbmTable'));
}
?>