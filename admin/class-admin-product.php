<?php if (!defined('ABSPATH')) exit;
if (!class_exists('WBM_Admin_Product')) {
    class WBM_Admin_Product{
        public function __construct()
        {
            add_filter('product_type_options', array(&$this, 'product_type_options'));
            add_filter('woocommerce_product_data_tabs', array(&$this, 'add_product_data_tab'));
            add_action('woocommerce_product_data_panels', array(&$this, 'add_product_data_panel'));
            add_action('woocommerce_process_product_meta', array(&$this, 'save_custom_fields'), 10, 2);
            add_action('wp_ajax_wbm_save_base_attribute', array(&$this, 'wbm_save_base_attribute'));
            add_action('wp_ajax_wbm_save_sub_attribute', array(&$this, 'wbm_save_sub_attribute'));
            add_action('wp_ajax_wbm_attribute_option_save', array(&$this, 'wbm_attribute_option_save'));
            add_action('wp_ajax_wbm_attribute_date_save', array(&$this, 'wbm_attribute_date_save'));
            add_action('woocommerce_product_options_general_product_data', array(&$this, 'wbm_product_meta'));
        }
        public static function wbm_product_meta()
        {
            oocommerce_wp_textarea_input(
                array('id' => '_wbm_details_link',
                    'label' => __('Details Page Link', 'wbm'),
                    'placeholder' => '',
                    'description' => __('Enter details page link with http://', 'wbm')
                ));
        }
        public static function product_type_options($types)
        {
            $types['wbm_check'] = array(
                'id' => '_wbm_check',
                'wrapper_class' => 'show_if_wbm',
                'label' => __('Enable Booking Module', 'wbm'),
                'description' => __('Enable Booking Module for this Product.', 'wbm')
            );
            return $types;
        }
        public static function add_product_data_tab($tabs)
        {
            $tabs['wbm_configs'] = array(
                'label' => __('Booking Module', 'wbm'),
                'target' => 'wbm_data_default_configuration',
                'class' => array('show_if_wbm_panel wbm_attribute_options')
            );
            return $tabs;
        }
        public function save_custom_fields($post_id, $post)
        {
            update_post_meta($post_id, '_wbm_check', isset($_POST['_wbm_check']) ? 'yes' : 'no');
            if (isset($_POST['wbm_base_attribute']) && $_POST['wbm_base_attribute'] != '') {
                update_post_meta($post_id, '_wbm_base_attribute', $_POST['wbm_base_attribute']);
            }
            if (isset($_POST['wbm_sub_attribute']) && $_POST['wbm_sub_attribute'] != '') {
                update_post_meta($post_id, '_wbm_sub_attribute', $_POST['wbm_sub_attribute']);
            }
            $anw_author_name = $_POST['_wbm_details_link'];
            if(isset($anw_author_name) && !empty($anw_author_name)){
                update_post_meta($post_id,'_wbm_details_link',esc_attr($anw_author_name));
            }
        }
        public static function add_product_data_panel()
        {
            global $wpdb, $post;
            $attributes = maybe_unserialize(get_post_meta($post->ID, '_product_attributes', true));
            $wbm_base_attribute = get_post_meta($post->ID, '_wbm_base_attribute', true);
            $wbm_sub_attribute = get_post_meta($post->ID, '_wbm_sub_attribute', true); ?>
            <div id="wbm_data_default_configuration" class="panel woocommerce_options_panel wc-metaboxes-wrapper">
                <div id="wbm_attribute_tab">
                    <div class="toolbar toolbar-top">
                        <button class="button button-primary" id="wbm_refresh_button"><?= __('Refresh this tab content', 'wbm'); ?></button>
                    </div>
                    <?php if (!isset($post->ID)) {
                        _e('Please publish or save as draft before going forward', 'wbm');
                        exit;
                    } ?>
                    <div class="toolbar toolbar-top">
                        <p class="form-field">
                            <label for="wbm_base_attribute"><?= __('Base Attribute', 'wbm'); ?></label>
                            <select id="wbm_base_attribute" name="wbm_base_attribute">
                                <option value=""><?= __('Choose Base Attribute', 'wbm') ?></option>
                                <?php if (isset($attributes) && !empty($attributes)) {
                                    foreach ($attributes as $attr) {
                                        $selected_base = $attr['name'] == $wbm_base_attribute ? 'selected' : '' ?>
                                        <option <?= $selected_base ?> value="<?= $attr['name'] ?>"><?= esc_html(wc_attribute_label($attr['name'])) ?></option>
                                    <?php }
                                } ?>
                            </select>
                            <button class="button wbm_add_attribute_base" id="wbm_add_attribute_base" type="button"><?= __('Save', 'wbm') ?></button></p> </div>
                    <div class="toolbar toolbar-top">
                        <p class="form-field">
                            <label for="wbm_sub_attribute"><?= __('Secondary Attribute', 'wbm'); ?></label>
                            <select id="wbm_sub_attribute" name="wbm_sub_attribute">
                                <option value=""><?= __('Choose Secondary Attribute', 'wbm') ?></option>
                                <?php if (isset($attributes) && !empty($attributes)) {
                                    foreach ($attributes as $attr) {
                                        $selected_sub = $attr['name'] == $wbm_sub_attribute ? 'selected' : '' ?>
                                        <option <?= $selected_sub ?> value="<?= $attr['name'] ?>"><?= esc_html(wc_attribute_label($attr['name'])) ?></option>
                                    <?php }} ?> </select>
                            <button class="button wbm_add_attribute_sub" id="wbm_add_attribute_sub"  type="button"><?= __('Save', 'wbm') ?></button>
                        </p>
                        <?php if ($wbm_sub_attribute)$allTerms = get_terms($wbm_sub_attribute);
                        if (isset($allTerms) && !empty($allTerms)) {
                            foreach ($allTerms as $term) {
                                if (has_term(absint($term->term_id), $wbm_sub_attribute, $post->ID)) {
                                    ?>
                                    <p class="wbm_attribute_terms form-field ">
                                        <label><?= $term->name ?></label>
                                        <a class="button button-primary" target="_blank"  href="options.php?page=wbm_booking_options&post&product=<?= $post->ID; ?>&term=<?= $term->term_id; ?>&taxonomy=<?= $wbm_sub_attribute; ?>"><?= __('Add / Edit Options', 'wbm'); ?></a>
                                        <a class="button button-primary" target="_blank"  href="options.php?page=wbm_booking_dates&post&product=<?= $post->ID; ?>&term=<?= $term->term_id; ?>&taxonomy=<?= $wbm_sub_attribute; ?>"><?= __('Add / Edit Date Fields', 'wbm'); ?></a>
                                    </p>                                <?php }}} ?>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                var wbm_product_page = true;
                var postId =<?=$post->ID;?>;
            </script>
        <?php }
        public static function wbm_save_base_attribute()
        {
            if (isset($_POST['postId']) && isset($_POST['attribute'])) {
                update_post_meta(intval($_POST['postId']), '_wbm_base_attribute', $_POST['attribute']);
            }            exit;
        }
        public static function wbm_save_sub_attribute()
        {
            if (isset($_POST['postId']) && isset($_POST['attribute'])) {
                update_post_meta(intval($_POST['postId']), '_wbm_sub_attribute', $_POST['attribute']);
            }            exit;
        }
        public static function wbm_attribute_option_save()
        {
            parse_str($_POST['formData'], $params);
            $prductId = intval($_POST['postId']);
            $taxonomy = esc_attr($_POST['taxonomy']);
            $term = intval($_POST['term']);
            if (isset($params['attribute_main']) && !empty($params['attribute_main'])) {
                update_post_meta($prductId, '_wbm_main_attribute_' . $taxonomy . '_' . $term, $params['attribute_main']);
            }
            if (isset($params['select_needed'])) {
                update_post_meta($prductId, '_wbm_select_needed_' . $taxonomy . '_' . $term, $params['select_needed']);
                update_post_meta($prductId, '_wbm_attribute_select_' . $taxonomy . '_' . $term, $params['attribute_select']);
            }
            echo 'success';
            exit;
        }
        public static function wbm_attribute_date_save()
        {
            parse_str($_POST['formData'], $params);
            $prductId = intval($_POST['postId']);
            $taxonomy = esc_attr($_POST['taxonomy']);
            $term = intval($_POST['term']);
            if (isset($params['date_needed']))
            {
                update_post_meta($prductId, '_wbm_date_needed_' . $taxonomy . '_' . $term, $params['date_needed']);
                update_post_meta($prductId, '_wbm_attribute_date_' . $taxonomy . '_' . $term, $params['attribute_date']);
            }
            echo 'success';
            exit;
        }
    }
    new WBM_Admin_Product();
}