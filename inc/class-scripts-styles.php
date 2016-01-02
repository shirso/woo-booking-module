<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!class_exists('WBM_Scripts_Styles')) {
    class WBM_Scripts_Styles{
        public function __construct(){
            add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
            add_action('wp_footer',array(&$this,'wbm_footer'));
        }

        public function enqueue_styles(){
            global $post;
            if(WBM_Frontend_Product::wbm_enabled($post->ID)){
                wp_enqueue_style('wbm_style_grids', WBM_PLUGIN_ABSOLUTE_PATH . 'assets/css/grid.css', false);
                wp_enqueue_style('wbm_style_checkbox', WBM_PLUGIN_ABSOLUTE_PATH . 'assets/css/fm.checkator.css', false);
                wp_enqueue_style('wbm_style_switch', WBM_PLUGIN_ABSOLUTE_PATH . 'assets/css/lc_switch.css', false);
                wp_enqueue_style('wbm_style_datetimepicker', WBM_PLUGIN_ABSOLUTE_PATH . 'assets/css/jquery.datetimepicker.css', false);
                wp_enqueue_style('wbm_style_accordion', WBM_PLUGIN_ABSOLUTE_PATH . 'assets/css/smk-accordion.css', false);
                wp_enqueue_style('wbm_style_alert', WBM_PLUGIN_ABSOLUTE_PATH . 'assets/css/jquery.alerts.css', false);
                wp_enqueue_style('wbm_style_frontend', WBM_PLUGIN_ABSOLUTE_PATH . 'assets/css/style.css', false);
                wp_enqueue_script('wbm_script_underscore',WBM_PLUGIN_ABSOLUTE_PATH.'assets/js/underscore-min.js',array('jquery'),null,true);
                wp_enqueue_script('wbm_script_chekbox',WBM_PLUGIN_ABSOLUTE_PATH.'assets/js/fm.checkator.jquery.js',array('jquery'),null,true);
                wp_enqueue_script('wbm_script_switch',WBM_PLUGIN_ABSOLUTE_PATH.'assets/js/lc_switch.min.js',array('jquery'),null,true);
                wp_enqueue_script('wbm_script_datetimepicker',WBM_PLUGIN_ABSOLUTE_PATH.'assets/js/jquery.datetimepicker.full.js',array('jquery'),null,true);
                wp_enqueue_script('wbm_script_tabs',WBM_PLUGIN_ABSOLUTE_PATH.'assets/js/jquery.responsiveTabs.js',array('jquery'),null,true);
                wp_enqueue_script('wbm_script_accordion',WBM_PLUGIN_ABSOLUTE_PATH.'assets/js/smk-accordion.min.js',array('jquery'),null,true);
                wp_enqueue_script('wbm_script_alert',WBM_PLUGIN_ABSOLUTE_PATH.'assets/js/jquery.alerts.js',array('jquery'),null,true);
                wp_register_script('wbm_script_frontend',WBM_PLUGIN_ABSOLUTE_PATH.'assets/js/wbm.frontend.js',array('jquery'),null,true);
                wp_localize_script('wbm_script_frontend','wbm_params',array(
                    'ajaxURL'=>admin_url( 'admin-ajax.php' ),
                    'yes_string'=>__('Yes','wbm'),
                    'no_string'=>__('No','wbm'),
                    'no_want'=>__("I don't want this service","wbm"),
                    'yes_want'=>__("I want this service","wbm"),
                    'currency_symbol'=>get_woocommerce_currency_symbol(),
                    'alert_heading'=>'Error',
                    'alert_msg'=>__('Please fill up all fields','wbm'),
                    'confirm_heading'=>__('Confirmation','wbm'),
                    'confirm_msg'=>__('Are you sure to book?','wbm'),
	                'choose_first_option_msg'=>__('Please choose any option first','wbm')
                ));
                wp_enqueue_script('wbm_script_frontend');
            }
        }
        public function wbm_footer(){
            global $post;
            if(WBM_Frontend_Product::wbm_enabled($post->ID)){
                $productId=$post->ID;
                $secondary_attr_id=get_post_meta($productId,'_wbm_sub_attribute',true);
                $secondary_variations=get_terms($secondary_attr_id);
                ?>
                <div id="wbm_hidden_container" class="wbm_hidden">
                <div id="wbm_main_attr">
                    <input type="hidden" value="" class="wbm_title">
                    <input type="hidden" value="" class="wbm_term">
                </div>
                <?php if(isset($secondary_variations) && !empty($secondary_variations)){
                    foreach($secondary_variations as $variation){
                        if(has_term(absint($variation->term_id),$secondary_attr_id,$productId)){
                    ?>
                        <div id="wbm_attr_<?=$variation->taxonomy?>_<?=$variation->term_id?>" class="wbm_attr_container">
                            <div class="wbm_secondary_container">
                                <input type="hidden" value="" class="wbm_title">
                                <input type="hidden" value="" class="wbm_term">
                                <input type="hidden" value="no" class="wbm_check">
                                <input type="hidden" value="" class="wbm_price">
                            </div>
                            <div class="wbm_selectbox_container">

                            </div>
                            <div class="wbm_date_container">

                            </div>
                        </div>
                <?php }}} ?>
                </div>
            <?php
            }
        }

    }
    new WBM_Scripts_Styles();
}