<?php if(!defined('ABSPATH')) exit; // Exit if accessed directly
if (!class_exists('WBM_Product_Cart')) {
    class WBM_Product_Cart
    {
        public function __construct()
        {
            add_filter('woocommerce_add_cart_item_data', array(&$this, 'add_cart_item_data'), 10, 2);
            add_action('woocommerce_before_calculate_totals', array(&$this, 'calculate_product_price'), 1, 1);
            add_filter('woocommerce_get_cart_item_from_session', array(&$this, 'get_cart_item_from_session'), 10, 2);
            add_filter('woocommerce_get_item_data', array(&$this, 'get_item_data'), 10, 2);
            add_filter('add_to_cart_redirect', array(&$this, 'redirect_to_checkout'));
            add_action('woocommerce_add_order_item_meta', array(&$this, 'order_item_meta'), 10, 2);
        }
        function add_cart_item_data($cart_item_meta, $product_id)
        {
            if (isset($cart_item_meta['wbm_product_cart'])) {
                return $cart_item_meta;
            }
            if (WBM_Frontend_Product::wbm_enabled($product_id) && isset($_POST["wbm_product_cart"]) && $_POST["wbm_product_cart"] == 1 && check_admin_referer('wbm_product_cart_' . $product_id)) {
                $cart_item_meta['wbm_product_cart'] = $_POST["wbm_product_cart"];
                $cart_item_meta['wbm_product_price'] = $_POST["wbm_product_price"];
                $cart_item_meta['wbm_product_attributes'] = $_POST["wbm_attributes"];
            }
            return $cart_item_meta;
        }
        function get_cart_item_from_session($cart_item, $values)
        {
            if (isset($values['wbm_product_cart']) && isset($values['wbm_product_price']) && isset($values['wbm_product_attributes'])) {
                $cart_item['wbm_product_cart'] = $values['wbm_product_cart'];
                $cart_item['wbm_product_price'] = $values['wbm_product_price'];
                $cart_item['wbm_product_attributes'] = $values['wbm_product_attributes'];
            }
            return $cart_item;
        }
        function get_item_data($other_data, $cart_item){
            if (isset($cart_item['wbm_product_cart']) && isset($cart_item['wbm_product_price']) && isset($cart_item['wbm_product_attributes'])) {
                $wbm_cart_array=$cart_item['wbm_product_attributes']["main"];
                $mainAttr=$wbm_cart_array["mainattr"];
                unset($wbm_cart_array["mainattr"]);
                $html='';
                foreach($wbm_cart_array as $key=>$value){
                    $html.='&nbsp;&nbsp;<strong>'.$value[0].'</strong><br/>';
                    if(isset($value["selectbox"]) && !empty($value["selectbox"])){
                        foreach($value["selectbox"] as $k=>$v){
                            $html.='&nbsp;&nbsp;&nbsp;&nbsp;'.$v.'<br/>';
                        }
                    }
                    if(isset($value["dates"]) && !empty($value["dates"])){
                        foreach($value["dates"] as $m=>$n){
                            $html.='&nbsp;&nbsp;&nbsp;&nbsp;'.$n.'<br/>';
                        }
                    }
                }
	            $other_data[] = array('name' =>$mainAttr, 'display' => $html, 'value' => '','hidden'=>false);
            }
	        return $other_data;
        }
        public function order_item_meta($item_id, $cart_item){
            if(isset($cart_item['wbm_product_cart']) && isset($cart_item['wbm_product_price']) && isset($cart_item['wbm_product_attributes'])){
                $wbm_cart_array=$cart_item['wbm_product_attributes']["main"];
                $mainAttr=$wbm_cart_array["mainattr"];
                unset($wbm_cart_array["mainattr"]);
                $html='';
                foreach($wbm_cart_array as $key=>$value){
                    $html.='&nbsp;&nbsp;<strong>'.$value[0].'</strong><br/>';
                    if(isset($value["selectbox"]) && !empty($value["selectbox"])){
                        foreach($value["selectbox"] as $k=>$v){
                            $html.='&nbsp;&nbsp;&nbsp;&nbsp;'.$v.'<br/>';
                        }
                    }
                    if(isset($value["dates"]) && !empty($value["dates"])){
                        foreach($value["dates"] as $m=>$n){
                            $html.='&nbsp;&nbsp;&nbsp;&nbsp;'.$n.'<br/>';
                        }
                    }
                }
                wc_add_order_item_meta($item_id, $mainAttr, $html);
            }
        }
        function redirect_to_checkout() {
            return WC()->cart->get_checkout_url();
        }
        function calculate_product_price($cart_object){
            foreach ( $cart_object->cart_contents as $key => $value ) {
                if(isset($value['wbm_product_cart']) && isset($value['wbm_product_price'])){
                    $value['data']->price=floatval($value['wbm_product_price']);
                }
            }
        }
    }
    new WBM_Product_Cart();
}