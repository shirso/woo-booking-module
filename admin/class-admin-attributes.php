<?php if (!defined('ABSPATH')) exit;
if (!class_exists('WBM_Admin_Attributes')) {
    class WBM_Admin_Attributes    {
        public function __construct()
        {
            $attributes = wc_get_attribute_taxonomy_names();
                       // print_r($attributes);
            foreach ($attributes as $attribute) {
                add_action($attribute . '_add_form_fields', array(&$this, 'add_attr_type_to_add_form'), 10, 2);
                add_action($attribute . '_edit_form_fields', array(&$this, 'add_image_uploader_to_edit_form'), 10, 1);
                add_action('edited_' . $attribute, array(&$this, 'save_taxonomy_custom_meta'), 10, 2);
                add_action('create_' . $attribute, array(&$this, 'save_taxonomy_custom_meta'), 10, 2);
                add_action('delete_' . $attribute, array(&$this, 'delete_taxonomy_custom_meta'), 10, 2);
                add_filter('manage_edit-' . $attribute . '_columns', array(&$this, 'woocommerce_product_attribute_columns'));
                add_filter('manage_' . $attribute . '_custom_column', array(&$this, 'woocommerce_product_attribute_column'), 10, 3);
            }
        }
        public function add_attr_type_to_add_form()
        {            ?>
            <script type="text/javascript">
                var wbm_attribute_page = true;
            </script>
            <div class="form-field">
                <label for="hd_wbm_attribute_type">
                    <input type="checkbox" value="1" name="hd_wbm_attribute_type" id="hd_wbm_attribute_type"> <?php _e('Need Image Upload?', 'wbm') ?>
                </label>
                <p class="description"><?= __('If this attribute need image, then check it', 'wbm'); ?></p>
            </div>
            <div class="form-field wbm-hidden wbm_image">
                <label for="hd_wbm_attribute_image"><?php _e('Image', 'wbm') ?></label>
                <div class="wbm-upload-field">
                    <input type="text" class="wide-fat" id="hd_wbm_attribute_image" value="" name="hd_wbm_attribute_image"/>
                </div>
                <button class="button button-secondary" id="btn_wbm_attribute_image_upload"><?php _e('Upload', 'wbm') ?></button>
            </div>
            <div class="form-field">
                <label for="hd_wbm_attribute_tooltip"><?= __('Tooltip', 'wbm') ?></label>
                <textarea id="hd_wbm_attribute_tooltip" name="hd_wbm_attribute_tooltip" cols="40" rows="5"></textarea>
                <p class="description"><?=esc_html__('To insert link in tooltip, use this format <a href="http://www.google.com/">Google</a>','wbm')?></p>
            </div>
        <?php }
        public function add_image_uploader_to_edit_form($term)
        {
            $term_id = $term->term_id;
            $img_or_color = get_option('_wbm_variation_attr_' . $term_id);
            $attr_type = get_option('_wbm_variation_attr_type_' . $term_id);
            $tooltip = get_option('_wbm_variation_tooltip_' . $term_id);
            $checked = $attr_type == 1 ? 'checked' : '';  ?>
            <tr class="form-field">
                <th>
                </th>
                <td class="wbm-upload-field">
                    <label for="hd_wbm_attribute_type">
                        <input type="checkbox" <?= $checked ?> value="1" name="hd_wbm_attribute_type" id="hd_wbm_attribute_type">
                        <?php _e('Need Image Upload?', 'wbm') ?>
                    </label>
                    <p class="description"><?= __('If this attribute need image, then check it', 'wbm'); ?></p>
                </td>
            </tr>
            <script type="text/javascript">var wbm_attribute_page = true;</script>
            <tr class="form-field <?php echo $attr_type == 1 ? '' : 'wbm-hidden'; ?> wbm_image" style="">
                <th>
                    <label for="hd_wbm_attribute_image"><?php _e('Image', 'wbm') ?></label>
                </th>
                <td class="wbm-upload-field">
                    <input type="text" class="wide-fat" id="hd_wbm_attribute_image" value="<?php echo $attr_type == 1 ? $img_or_color : ''; ?>" name="hd_wbm_attribute_image"/>
                    <button class="button button-secondary" id="btn_wbm_attribute_image_upload"><?php _e('Upload', 'wbm') ?></button>
                </td>
            </tr>
            <tr>
                <th><?= __('Tooltip', 'wbm') ?></th>
                <td>
                    <textarea id="hd_wbm_attribute_tooltip" name="hd_wbm_attribute_tooltip" class="large-text" cols="50" rows="5"><?= @$tooltip; ?></textarea>
                    <p class="description"><?=esc_html__('To insert link in tooltip, use this format <a href="http://www.google.com/">Google</a>','wbm')?></p>
                </td>
            </tr>
        <?php }
        public function save_taxonomy_custom_meta($term_id)
        {
            if (isset($_POST['hd_wbm_attribute_type']) && $_POST['hd_wbm_attribute_image'] != '')
            {
                update_option('_wbm_variation_attr_type_' . $term_id, 1);
                update_option('_wbm_variation_attr_' . $term_id, $_POST['hd_wbm_attribute_image']);
            }
            if (isset($_POST['hd_wbm_attribute_tooltip']) && !empty($_POST['hd_wbm_attribute_tooltip']))
            {
                update_option('_wbm_variation_tooltip_' . $term_id, $_POST['hd_wbm_attribute_tooltip']);
            }
        }
        public function delete_taxonomy_custom_meta($term_id)
        {
            delete_option('_wbm_variation_attr_' . $term_id);
            delete_option('_wbm_variation_attr_type_' . $term_id);
            delete_option('_wbm_variation_tooltip_' . $term_id);
        }
        public function woocommerce_product_attribute_columns($columns)
        {
            $columns['wbm_img'] = __('Thumbnail', 'wbm');
            return $columns;
        }
        public function woocommerce_product_attribute_column($columns, $column, $id)
        {
            if ($column == 'wbm_img')
            {
                $wbm_img = get_option('_wbm_variation_attr_' . $id);
                $attr_type = get_option('_wbm_variation_attr_type_' . $id);
                if ($attr_type == 1 && $wbm_img != '')
                {
                    echo '<img src="' . $wbm_img . '" style="height:40px"/>';
                }
            }
        }
    }
    new WBM_Admin_Attributes();
}