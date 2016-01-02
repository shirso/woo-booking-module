<?php if (!defined('ABSPATH')) exit;
if (!class_exists('WBM_Admin_Order')) {
    class WBM_Admin_Order{
        public function __construct() {
            add_action( 'add_meta_boxes', array(&$this,'add_order_meta_box'));
        }


    }
    new WBM_Admin_Order();
}
