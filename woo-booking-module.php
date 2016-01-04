<?php
/*Plugin Name:Woocommerce Booking Module
 Plugin URI: http://wp-theme.eu/de
 Description: Create Multi-step Booking Module with WooCommerceVersion: 1.0.1
 Author: WP-THEME.EU
 Author URI:http://wp-theme.eu/de*/
if (!defined('ABSPATH')) exit;
if (!defined('WBM_PLUGIN_DIR')) define('WBM_PLUGIN_DIR', dirname(__FILE__));
if (!defined('WBM_PLUGIN_ROOT_PHP')) define('WBM_PLUGIN_ROOT_PHP', dirname(__FILE__) . '/' . basename(__FILE__));
if (!defined('WBM_PLUGIN_ABSOLUTE_PATH')) define('WBM_PLUGIN_ABSOLUTE_PATH', plugin_dir_url(__FILE__));
if (!defined('WBM_PLUGIN_ADMIN_DIR')) define('WBM_PLUGIN_ADMIN_DIR', dirname(__FILE__) . '/admin');
if (!class_exists('WBM_Booking_Module')) {
    class WBM_Booking_Module
    {
        const CAPABILITY = "edit_wbm";

        public function __construct()
        {
            require_once(WBM_PLUGIN_ADMIN_DIR . '/class-admin.php');
            require_once(WBM_PLUGIN_DIR . '/inc/class-scripts-styles.php');
            add_action('init', array(&$this, 'init'));
        }

        public function init()
        {
            require_once(WBM_PLUGIN_DIR . '/inc/class-frontend-product.php');
            require_once(WBM_PLUGIN_DIR . '/inc/class-frontend-ajax.php');
            require_once(WBM_PLUGIN_DIR . '/inc/class-product-cart.php');
            load_plugin_textdomain('wbm', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
        }
    }

    new WBM_Booking_Module();
}