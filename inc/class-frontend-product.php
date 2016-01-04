<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!class_exists('WBM_Frontend_Product')) {
    class WBM_Frontend_Product
    {
        public function __construct()
        {
            add_filter('body_class', array(&$this, 'add_class'));
            add_action('template_redirect', array(&$this, 'remove_main_image'));
            add_filter('woocommerce_is_purchasable', array(&$this, 'custom_woocommerce_is_purchasable'), 10, 2);
        }

        public function custom_woocommerce_is_purchasable($purchasable, $product)
        {
            if ($this->wbm_enabled($product->id)) {
                if ($product->get_price() == 0 || $product->get_price() == '') {
                    $purchasable = true;
                }
            }
            return $purchasable;
        }

        public function remove_main_image()
        {
            global $post;
            if ($this->wbm_enabled($post->ID)) {
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
                remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
                add_action('woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 10);
                remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
                add_action('woocommerce_before_single_product_summary', array(&$this, 'add_product_designer'), 20);
                remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
                add_action('woocommerce_after_shop_loop_item', array(&$this, 'replace_add_to_cart'));
            }
        }

        public function replace_add_to_cart()
        {
            global $product;
            $link = $product->get_permalink();
            $details_link = get_post_meta($product->id, '_wbm_details_link', true);
            echo do_shortcode('<a href="' . $details_link . '" class="button wbm_shop_loop_details_button">' . __('View Details', 'wbm') . '</a>');
            echo do_shortcode('<a href="' . $link . '" class="button wbm_shop_loop_book_button">' . __('Book Now', 'wbm') . '</a>');
        }

        public function add_product_designer()
        {
            global $post, $wpdb, $product, $woocommerce;
            $productId = $product->id;
            $primary_attr_id = get_post_meta($productId, '_wbm_base_attribute', true);
            $secondary_attr_id = get_post_meta($productId, '_wbm_sub_attribute', true);
            $secondary_variations = get_terms($secondary_attr_id);
            $primary_variation = get_terms($primary_attr_id);
            $price_data = get_post_meta($productId, '_regular_price', true);
            $initial_price = !empty($price_data) ? $price_data : 0;
            ?>
            <div id="wbm_container">
                <div id="wbm_horizontalTab" class="clearfix">
                    <ul id="wbm_secondary_list">
                        <li><a href="#wbm_first_tab"><?= __('Your Needs', 'wbm'); ?></a></li>
                        <?php if (isset($secondary_variations) && !empty($secondary_variations)) {
                            foreach ($secondary_variations as $variation) {
                                if (has_term(absint($variation->term_id), $secondary_attr_id, $post->ID)) {
                                    ?>
                                    <li class="wbm_hidden"
                                        id="li_<?= $variation->taxonomy; ?>_<?= $variation->term_id; ?>"><a
                                            href="#<?= $variation->taxonomy ?>_<?= $variation->term_id ?>"><span><?= $variation->name; ?></span></a>
                                    </li>
                                <?php }
                            }
                        } ?>
                        <li class="wbm_hidden"><a href="#wbm_last_tab"><?= __('Your Choices', 'wbm') ?></a></li>
                    </ul>
                    <div id="wbm_first_tab">
                        <h3><?= __('What do you need from us?', 'wbm') ?></h3>
						<div class="clearfix">
                        <?php if (isset($primary_variation) && !empty($primary_variation)) {
                            foreach ($primary_variation as $variation1) {
                                if (has_term(absint($variation1->term_id), $primary_attr_id, $post->ID)) {
                                    ?>
                                    <div class="col-sm-4 sps wbm_first_step_divs"
                                         data-term="<?= $variation1->term_id ?>" data-title="<?= $variation1->name; ?>"
                                         data-price="">
                                        <?php $thumbnail = get_option('_wbm_variation_attr_' . $variation1->term_id);
                                        $tooltip = get_option('_wbm_variation_tooltip_' . $variation1->term_id);
                                        ?>
                                        <?php if (!empty($thumbnail)) ?>
                                        <img src="<?= $thumbnail ?>" class="img-responsive"/>

                                        <h2><?= $variation1->name; ?></h2>

                                        <p>
                                            <?= $variation1->description ?>
                                            <?php if(!empty($tooltip)){?>
                                            <a href="#" class="toltp" title="<?=esc_html(stripslashes($tooltip))?>">(?)</a>
                                        <?php }?>
                                        </p>
                                    </div>
                                <?php }
                            }
                        } ?>
						</div>
                        <div class="blato"><div class="cntn lftbtn"><button data-type="next" class="wbm_next wbm_navigate_button_for_first"><?= __('Continue', 'wbm') ?> <i class="fa fa-angle-double-right"></i></button></div></div>
                    </div>
                    <?php if (isset($secondary_variations) && !empty($secondary_variations)) {
                        foreach ($secondary_variations as $variation) {
                            if (has_term(absint($variation->term_id), $secondary_attr_id, $post->ID)) {
                                $attr_price = get_post_meta($productId, '_wbm_main_attribute_' . $variation->taxonomy . '_' . $variation->term_id, true);
                                ?>
                                <div id="<?= $variation->taxonomy ?>_<?= $variation->term_id ?>"
                                     data-title="<?= $variation->name; ?>" data-price="<?= @$attr_price; ?>"
                                     data-taxonomy="<?= $variation->taxonomy ?>"
                                     data-term="<?= $variation->term_id ?>"></div>
                            <?php }
                        }
                    } ?>
                    <div id="wbm_last_tab">
                        <div id="wbm_choice_details"></div>
                        <form method="post" id="wbm_booking_form" class="wbm_hidden">
                            <input type="hidden" name="wbm_product_attributes" id="wbm_product_attributes" value="">
                            <input type="hidden" name="add-to-cart" value="<?= $post->ID; ?>">
                            <input type="submit" id="wbm_book_button" value="<?= __('Book Now', 'wbm'); ?>">
                            <?= wp_nonce_field('wbm_product_cart_' . $post->ID); ?>
                        </form>
                        <p id="wbm_error_choosing"><?= __('You haven\'t finish selection', 'wbm'); ?></p>
                    </div>
                </div>
                <div id="wbm_price_container">
                    <h6><i class="fa fa-database"></i><?= __('Total Price', 'wbm') ?>
                        <span><?= get_woocommerce_currency_symbol() ?><b
                                id="wbm_temp_price"><?= $initial_price ?></b></span></h6>
                </div>
            </div>
            <script type="text/javascript">
                var productId =<?=$post->ID;?>;
                var initial_price =<?=$initial_price?>;
                var wbm_ajax_nonce='<?=wp_create_nonce( "wbm_ajax_nonce" );?>';
            </script>
            <?php
        }

        public function add_class($classes)
        {
            global $post;
            if ($this->wbm_enabled($post->ID)) {
                $classes[] = 'wbm-body-product';
            }
            return $classes;
        }

        public function wbm_enabled($product_id)
        {
            global $sitepress;
            if ($sitepress && method_exists($sitepress, 'get_original_element_id')) {
                $product_id = $sitepress->get_original_element_id($product_id, 'post_product');
            }
            return get_post_meta($product_id, '_wbm_check', true) == 'yes' && get_post_type($product_id) == 'product';
        }

        public function wbm_convert_string_value_to_int($value)
        {

            if ($value == 'yes') {
                return 1;
            } else if ($value == 'no') {
                return 0;
            } else {
                return $value;
            }

        }
    }

    new WBM_Frontend_Product();
}