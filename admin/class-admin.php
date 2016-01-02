<?php
if ( !defined( 'ABSPATH' ) ) exit;
if( !class_exists('WBM_Admin') ) {
    class WBM_Admin{
        public function __construct() {
            add_action('admin_init',array(&$this,'plugin_admin_init'));
            add_action( 'admin_menu', array(&$this,'customize_menu'), 99, 0 );
            add_action('admin_enqueue_scripts',array(&$this,'admin_scripts'));
        }
        public static function plugin_admin_init(){
              //  remove_role('wbm_employee');
                $role = get_role( 'wbm_employee' );
                if(!$role) $role = add_role('wbm_employee', __('Employee/Cleaner','wbm'));
                $role->add_cap( 'manage_woocommerce_orders' );
                $role->add_cap( 'manage_woocommerce' );
                $role->add_cap( 'edit_shop_order' );
                $role->add_cap( 'edit_published_shop_orders' );
                $role->add_cap( 'edit_shop_orders' );
                $role->add_cap( 'read_shop_order' );
                $role->add_cap( 'edit_shop_order_terms' );
                $role->add_cap( 'edit_others_shop_orders' );
                $role->add_cap( 'read' );
                require_once(WBM_PLUGIN_ADMIN_DIR.'/class-admin-attributes.php');
                require_once(WBM_PLUGIN_ADMIN_DIR . '/class-admin-product.php' );
                require_once(WBM_PLUGIN_ADMIN_DIR . '/class-admin-order.php' );
        }
        public static function admin_scripts(){
            wp_enqueue_media();
            wp_register_script('wbm_sheepit_script',WBM_PLUGIN_ABSOLUTE_PATH.'admin/assets/js/jquery.sheepItPlugin-1.1.1.min.js','',false,true);
            wp_register_script('wbm_admin_script',WBM_PLUGIN_ABSOLUTE_PATH.'admin/assets/js/wbm_admin_script.js','',false,true);
            wp_enqueue_script('wbm_sheepit_script');
            wp_enqueue_script('wbm_admin_script');
            wp_enqueue_style('wpc_admin_styles',WBM_PLUGIN_ABSOLUTE_PATH.'admin/assets/css/wbm_admin_styles.css');
        }
        public  function customize_menu(){
            $remove = array( 'wc-settings', 'wc-status', 'wc-addons', );
            foreach ( $remove as $slug ) {
                if ( ! current_user_can( 'update_core' ) ) {
                    remove_submenu_page( 'woocommerce', $slug );
                }
            }
            add_submenu_page(
                'options.php'
                , __('Booking Options','wbm')
                , __('Booking Options','wbm')
                , 'manage_options'
                , 'wbm_booking_options'
                , array(&$this,'my_custom_submenu_page_callback')
            );
            add_submenu_page(
                'options.php'
                , __('Booking Dates','wbm')
                , __('Booking Dates','wbm')
                , 'manage_options'
                , 'wbm_booking_dates'
                , array(&$this,'my_custom_submenu_date_callback')
            );
        }
        public function my_custom_submenu_page_callback(){
            require_once(WBM_PLUGIN_ADMIN_DIR.'/admin_booking_options.php');
        }
        public function my_custom_submenu_date_callback(){
            require_once(WBM_PLUGIN_ADMIN_DIR.'/admin_booking_dates.php');
        }
    }
    new WBM_Admin();
}