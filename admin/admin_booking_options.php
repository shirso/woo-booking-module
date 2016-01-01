<?php
if ( !defined( 'ABSPATH' ) ) exit;
$productId=intval($_GET['product']);
$termId=intval($_GET['term']);
$taxonomy=$_GET['taxonomy'];
$termDetails=get_term($termId,$taxonomy);
$main_attribute=get_post_meta($productId,'_wbm_main_attribute_'.$taxonomy.'_'.$termId,true);
$select_needed=get_post_meta($productId,'_wbm_select_needed_'.$taxonomy.'_'.$termId,true);
$attribute_select=get_post_meta($productId,'_wbm_attribute_select_'.$taxonomy.'_'.$termId,true);
//print_r();
$select_for_this_term=@$attribute_select;
$dataArray=array();
if(isset($select_for_this_term) && !empty($select_for_this_term)){
    $dataArray=$select_for_this_term;
}

?>
<div class="wrap options-general-php ">
    <h2><?=__('Options and Price for','wbm');?> <?=$termDetails->name?></h2>
    <form id="wbm_attribute_option_form">
    <table class="form-table">
        <tbody>
        <tr>
            <th><label for="wbm_main_attribute_price"><?=__('Price for','wbm');?> <?=$termDetails->name?></label></th>
            <td><input type="text" class="small-text" id="wbm_main_attribute_price" name="attribute_main" value="<?=@$main_attribute?>"> <?=get_woocommerce_currency_symbol();?></td>
        </tr>
        <tr>
            <th>
                <label for="wbm_main_attribute_option_check"><?=__('Add Drop-down Options','wbm');?></label>
            </th>
            <td>
                <input type="checkbox" <?php if(isset($select_needed) && !empty($select_needed))echo 'checked'; ?> id="wbm_main_attribute_option_check" name="select_needed">
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
            <div id="wbm_attribute_select" <?php if(!isset($select_needed) || empty($select_needed)){?>class="wbm-hidden"<?php }?>>
                <div id="wbm_attribute_select_template" class="wbm_form-field">
                    <lable for="wbm_attribute_select_#index#_title"><?=__('Display Title','wbm')?></lable>
                    <input type="text" id="wbm_attribute_select_#index#_title" name="attribute_select[#index#][title]" class="wbm_text">
                    <lable for="wbm_attribute_select_#index#_thumbnail"><?=__('Thumbnail','wbm')?></lable>
                    <input type="text" id="wbm_attribute_select_#index#_thumbnail" name="attribute_select[#index#][thumbnail]" class="wbm_text">
                    <button class="wbm_image_upload_button button"><?=__('Upload','wbm')?></button>
                        <div class="wbm-opsn">
                            <label class="wbmnm"><?=__('Options','wbm');?></label>
                            <div id="wbm_attribute_select_#index#_options">
                                <div id="wbm_attribute_select_#index#_options_template" class="wbm_form-field wbm-left">
                                   <label for=""><?=__('Value','wbm');?></label>
                                    <input type="text" class="wbm_text" id="wbm_attribute_select_#index#_options_#index_options#_value" name="attribute_select[#index#][options][#index_options#][value]">
                                    <label for=""><?=__('Price','wbm');?></label>
                                    <input type="text" class="small-text" id="wbm_attribute_select_#index#_options_#index_options#_price" name="attribute_select[#index#][options][#index_options#][price]">
                                    <?=get_woocommerce_currency_symbol();?>
                                 </div>
                                <div id="wbm_attribute_select_#index#_options_noforms_template"><?=__('No Option','wbm');?></div>
                                <div id="wbm_attribute_select_#index#_options_controls" class="wbm_controls">
                                    <div id="wbm_attribute_select_#index#_options_add" class="wbmflbtn"><a class="button button-default"><span><?=__('Add Option','wbm')?></span></a></div>
                                    <div id="wbm_attribute_select_#index#_options_remove_last" class="wbmflbtn"><a class="button button-default"><span><?=__('Remove Option','wbm')?></span></a></div>
                                </div>
                            </div>
                        </div>
                 </div>
                <div id="wbm_attribute_select_noforms_template"><?=__('No Drop-down','wbm');?></div>
                <div id="wbm_attribute_select_controls" class="wbm_controls wbmasd">
                    <div id="wbm_attribute_select_add" class="wbmflbtn"><a class="button button-primary"><span><?=__('Add Drop-down','wbm')?></span></a></div>
                    <div id="wbm_attribute_select_remove_last" class="wbmflbtn"><a class="button button-primary"><span><?=__('Remove Drop-down','wbm')?></span></a></div>
                 </div>
            </div>
            </td>
        </tr>
        <tr><td colspan="2"><button class="button button-hero" id="wbm_attribute_option_save_button"><?=__('Save All','wbm');?></button></td></tr>
        </tfoot>
    </table>
    </form>
</div>
<script type="text/javascript">
    var wbm_options_page=true;
        productId=<?=$productId;?>;
        taxonomy='<?=$taxonomy?>';
        termId=<?=$termId?>;
        injectData=<?=json_encode($dataArray);?>;
        loadingText='<?=__('Saving','wbm')?>';
</script>
