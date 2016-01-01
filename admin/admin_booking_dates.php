<?php if (!defined('ABSPATH')) exit;
$productId = intval($_GET['product']);
$termId = intval($_GET['term']);
$taxonomy = $_GET['taxonomy'];
$termDetails = get_term($termId, $taxonomy);
$date_needed = get_post_meta($productId, '_wbm_date_needed_' . $taxonomy . '_' . $termId, true);
$attribute_date = get_post_meta($productId, '_wbm_attribute_date_' . $taxonomy . '_' . $termId, true);
$date_for_this_term = @$attribute_date;
$dataArray = array();
if (isset($date_for_this_term) && !empty($date_for_this_term)) {    $dataArray = $date_for_this_term;}?>
<div class="wrap options-general-php ">
    <h2><?= __('Dates for', 'wbm'); ?> <?= $termDetails->name ?></h2>
    <form id="wbm_attribute_date_form">
        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="wbm_main_attribute_date_check"><?= __('Add Date Fields', 'wbm'); ?></label>
                </th>
                <td>
                    <input type="checkbox" <?php if (isset($date_needed) && !empty($date_needed)) echo 'checked'; ?> id="wbm_main_attribute_date_check" name="date_needed">
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>   
                <td colspan="2">
                    <div id="wbm_attribute_date" <?php if (!isset($date_needed) || empty($date_needed)){ ?>class="wbm-hidden"<?php } ?>>
                        <div id="wbm_attribute_date_template" class="wbm_form-field">
                            <lable for="wbm_attribute_date_#index#_title"><?= __('Display Title', 'wbm') ?></lable>
                            <input type="text" id="wbm_attribute_date_#index#_title" name="attribute_date[#index#][title]" class="wbm_text">
                        </div>
                        <div id="wbm_attribute_date_noforms_template"><?= __('No Date Field', 'wbm'); ?></div>
                        <div id="wbm_attribute_date_controls" class="wbm_controls wbmasd">
                            <div id="wbm_attribute_date_add" class="wbmflbtn"><a class="button button-primary"><span><?= __('Add Date Field', 'wbm') ?></span></a>
                            </div>
                            <div id="wbm_attribute_date_remove_last" class="wbmflbtn"><a class="button button-primary"><span><?= __('Remove Date Field', 'wbm') ?></span></a>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button class="button button-hero" id="wbm_attribute_date_save_button"><?= __('Save All', 'wbm'); ?></button> \
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script type="text/javascript">
    var wbm_dates_page = true;
    productId =<?=$productId;?>;
    taxonomy = '<?=$taxonomy?>';
    termId =<?=$termId?>;
    injectData =<?=json_encode($dataArray);?>;
    loadingText = '<?=__('Saving','wbm')?>';
</script>