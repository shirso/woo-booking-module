<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
if (!class_exists('WBM_Frontend_Ajax')) {
    class WBM_Frontend_Ajax{
        public function __construct(){
            add_action('wp_ajax_wbm_load_attribute_tab',array(&$this,'load_attribute_tab'));
            add_action('wp_ajax_nopriv_wbm_load_attribute_tab',array(&$this,'load_attribute_tab'));
        }
        public function load_attribute_tab(){
            $productId=intval($_POST['productId']);
            $taxonomy=esc_attr($_POST['taxonomy']);
            $termId=intval($_POST['termId']);
            $allOptions_select_needed=get_post_meta($productId,'_wbm_select_needed_'.$taxonomy.'_'.$termId,true);
            $thumbnail=get_option('_wbm_variation_attr_'.$termId);
            $tooltip = get_option('_wbm_variation_tooltip_' .$termId);
            $termDetails=get_term($termId,$taxonomy);
            $attr_price=get_post_meta($productId,'_wbm_main_attribute_'.$termDetails->taxonomy.'_'.$termDetails->term_id,true);
            $allOptions_date_needed=get_post_meta($productId,'_wbm_date_needed_'.$taxonomy.'_'.$termId,true);
            ?>
            <div class="chakkbx">
                <input type="checkbox" data-label="choice_label_<?=$taxonomy?>_<?=$termId?>" data-title="<?=$termDetails->name?>" data-price="<?=@$attr_price?>"  data-taxonomy="<?=$taxonomy;?>" data-term="<?=$termId;?>" id="wbm_onoff_<?=$taxonomy?>_<?=$termId?>"  class="wbm_switch wbm_attribute_toggle" checked="checked">
                <p id="choice_label_<?=$taxonomy?>_<?=$termId?>"><?=__('I want this service')?></p>
            </div>
            <div class="dts1-insec clearfix">
                <?php if(isset($thumbnail) && !empty($thumbnail)){?>
                    <img src="<?=$thumbnail?>" class="img-responsive">
                <?php }?>
                <div class="mlftx"> <h2><?=$termDetails->name?></h2>
                <p><?=$termDetails->description?>
                    <?php if(!empty($tooltip)){?>
                        <a href="#" class="toltp">(?) <span><?=esc_html($tooltip)?></span></a>
                    <?php }?>
                </p>

                </div>
            </div>
            <div class="wbm_accordion" id="wbm_accordion_<?=$taxonomy?>_<?=$termId?>">
            <?php
            if(isset($allOptions_select_needed) && !empty($allOptions_select_needed)){
                $allOptions_selects=get_post_meta($productId,'_wbm_attribute_select_'.$taxonomy.'_'.$termId,true);
                if(isset($allOptions_selects) && !empty($allOptions_selects)){?>
                    <div class="accordion_in">
                    <div class="acc_head"><?=__('Details','wbm');?></div>
                    <div class="acc_content">
                    <div class="selectdrp clearfix">
                        <?php $count_select=0; foreach($allOptions_selects as $select){?>
                         <div class="slcd">
                             <?php if(isset($select['thumbnail']) && !empty($select['thumbnail'])) ?>
                             <img src="<?=@$select['thumbnail']?>" class="img-responsive">
                             <h3><?=@$select['title']?></h3>
                             <?php if(isset($select['options']) && !empty($select['options'])){?>
                                <select class="wbm_options" data-count="<?=$count_select?>" data-taxonomy="<?=$taxonomy?>" data-term="<?=$termId?>" data-title="<?=@$select['title']?>">
                                 <option value="">---</option>
                                 <?php foreach($select['options'] as $option){?>
                                  <option data-price="<?=@$option['price']?>" value="<?=@$option['value']?>"><?=@$option['value']?> <?php if(!empty($option['price'])){?> (<?=get_woocommerce_currency_symbol()?><?=@$option['price']?>)<?php }?></option>
                                  <?php } ?>
                                </select>
                                <?php $count_select++; }?>

                         </div>
           <?php }}?> </div></div></div> <?php }?>
           <?php if(!empty($allOptions_date_needed)){ ?>
            <div class="accordion_in">
                <div class="acc_head"><?=__('Dates','wbm');?></div>
                <div class="acc_content">
               <?php  $allOptions_dates=get_post_meta($productId,'_wbm_attribute_date_'.$taxonomy.'_'.$termId,true);
               if(isset($allOptions_dates) && !empty($allOptions_dates)){?>
               <?php $count_date=0; foreach($allOptions_dates as $date){?>
                       <div id="date_div_<?=$taxonomy?>_<?=$termId?>_<?=$count_date?>" data-taxonomy="<?=$taxonomy?>" data-term="<?=$termId?>" data-count="<?=$count_date?>" data-title="<?=@$date['title']?>" class="wbm_date_div">
                           <div class="chakkbx chk2">
                               <label><?=@$date['title']?></label>
                            </div>
                           <div class="dtm-secsec clearfix">
                               <div><label><?=__('Date','wbm')?></label><input readonly type="text" data-taxonomy="<?=$taxonomy?>" data-term="<?=$termId?>" data-count="<?=$count_date?>" class="wbm_datetime_input" data-type="date" id="wbm_checking_date_<?=$taxonomy?>_<?=$termId?>_<?=$count_date?>"/></div>
                               <div><label><?=__('Time','wbm')?></label><input  readonly type="text" data-taxonomy="<?=$taxonomy?>" data-term="<?=$termId?>" data-count="<?=$count_date?>" class="wbm_datetime_input" data-type="time" id="wbm_checking_time_<?=$taxonomy?>_<?=$termId?>_<?=$count_date?>"/></div>
                           </div>
                        </div>
                       <?php $count_date++;}} ?>

                </div>
            </div>
            <?php }?>
            </div>
<!--            <div class="cntn lftbtn"><a class="wbm_previous wbm_navigate_button" data-type="prev" href="#"><< --><?//=__('Previous','wbm')?><!--</a> <a data-type="next" class="wbm_next wbm_navigate_button" href="#">--><?//=__('Next','wbm')?><!-- >></a></div>-->
            <div class="cntn lftbtn"><a data-type="next" class="wbm_next wbm_navigate_button" href="#"><?=__('Continue','wbm')?></a></div>
           <?php  exit;
        }

    }
    new WBM_Frontend_Ajax();
}