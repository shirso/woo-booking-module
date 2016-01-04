jQuery(function($){
    "use strict";
   var tabVisited=[];
   var tabId;
   var currentIndex=0;
   var hidden_container=$("#wbm_hidden_container");
   $('.wbm_switch').lc_switch(wbm_params.yes_string,wbm_params.no_string);
    $('.toltp').tooltipster();
   var inactiveTabs=[];
   var noNeedTabs=[];
   for(var i=1;i<$("#wbm_secondary_list").find("li").length-1;i++){
      inactiveTabs.push(i);
   }
   var checkAttributeValidity=function(){
        var validity=false;
        $('.wbm_check').each(function(index,value){
            if($(this).val()=='yes'){
                validity=true;
                return false;
            }
        });
       return validity;
   };
   var wbm_tabs=$("#wbm_horizontalTab").responsiveTabs({
      rotate: false,
      startCollapsed: 'accordion',
      collapsible: 'accordion',
      setHash: false,
      disabled: inactiveTabs,
      activate: function(e, tab) {
          var tabSelector = tab.selector;
          currentIndex=tab.id;
          tabId=$(tabSelector).attr("id");
          var taxonomy = $(tabSelector).data('taxonomy');
          var termId = $(tabSelector).data('term');
          var title=$(tabSelector).data('title');
          var price=$(tabSelector).data('price');

          if(!_.isUndefined(productId) && !_.isUndefined(taxonomy) && !_.isUndefined(termId) && !_.contains(tabVisited,currentIndex)){
               tabVisited.push(currentIndex);
               var secondaryContainer=hidden_container.find("#wbm_attr_"+taxonomy+"_"+termId).find(".wbm_secondary_container");
               $(secondaryContainer).find('.wbm_title').val(title);
               $(secondaryContainer).find('.wbm_term').val(termId);
               $(secondaryContainer).find('.wbm_check').val('no');
               $(secondaryContainer).find('.wbm_price').val(price);
               loadAttributeTab(productId,taxonomy,termId);
          }
          if(_.contains(tabVisited,currentIndex) && !_.contains(noNeedTabs,tabId) && !checkValidity(tabId)) {
              inactiveOtherTabs(currentIndex);
          }
          if(tabId=="wbm_last_tab"){
               $("#wbm_choice_details").html(arrangeFinishStep());
              if(checkAttributeValidity()){
                  $("#wbm_booking_form").removeClass('wbm_hidden');
                  $("#wbm_error_choosing").addClass('wbm_hidden');
                  makeFormElements();
                 console.log(makeAttributeElement());
              }else{
                  $("#wbm_booking_form").addClass('wbm_hidden');
                  $("#wbm_error_choosing").removeClass('wbm_hidden');
              }
          }
      }
  });

   $(document).on('lcs-statuschange','.wbm_switch',function(){
       var status = ($(this).is(':checked')) ? 'checked' : 'unchecked';
       var taxonomy=$(this).data('taxonomy');
       var label='#'+$(this).data('label');
       var term=$(this).data('term');
       var title= $(this).data('title');
       var price= $(this).data('price');
       var accordion='#wbm_accordion_'+taxonomy+'_'+term;
       var relativeUrl="#"+taxonomy+"_"+term;
       var anchor=$('a[href="' + relativeUrl  + '"]');
       if(status=='checked'){
            noNeedTabs= _.without(noNeedTabs,tabId);
            anchor.parent().removeClass('wbm_deactive');
            $(label).text(wbm_params.yes_want);
            $(accordion).removeClass('wbm_hidden');
           var secondaryContainer=hidden_container.find("#wbm_attr_"+taxonomy+"_"+term).find(".wbm_secondary_container");
           $(secondaryContainer).find('.wbm_title').val(title);
           $(secondaryContainer).find('.wbm_term').val(term);
           $(secondaryContainer).find('.wbm_check').val('yes');
           $(secondaryContainer).find('.wbm_price').val(price);
           inactiveOtherTabs(currentIndex);
       }else{
           noNeedTabs.push(tabId);
           anchor.parent().addClass('wbm_deactive');
           $(label).text(wbm_params.no_want);
           $(accordion).find('select').prop('selectedIndex',0);
           $(accordion).find('input[type="text"]').val('');
           //$(accordion).find('input[type="checkbox"]').removeAttr('checked');
          // $(accordion).find('.wbm_date_div').addClass('wbm_hidden');
           $(accordion).addClass('wbm_hidden');
           clearAllPrice(taxonomy,term);
           activateOtherTabs(currentIndex);
       }
       calculatePrice();
   });
  $(document).on('click','.wbm_first_step_divs',function(e){
      e.preventDefault();
      $('.wbm_first_step_divs').removeClass('wbm_active');
      $(this).addClass('wbm_active');
      $("#wbm_secondary_list li").removeClass('wbm_hidden');
      hidden_container.find("#wbm_main_attr").find(".wbm_title").val($(this).data('title'));
      hidden_container.find("#wbm_main_attr").find(".wbm_term").val($(this).data('term'));
  });
  $(document).on('click','.wbm_navigate_button_for_first',function(e){
      e.preventDefault();
      if(hidden_container.find("#wbm_main_attr").find(".wbm_title").val()!="" || hidden_container.find("#wbm_main_attr").find(".wbm_term").val()!=""){
           removeFromInactive(1);
          wbm_tabs.responsiveTabs('activate',currentIndex+1);
      }else{
            jAlert(wbm_params.choose_first_option_msg,wbm_params.alert_heading);
      }

  });
 //$(document).on('change','.wbm_date_check',function(){
 //    var status = ($(this).is(':checked')) ? 'checked' : 'unchecked';
 //    var taxonomy=$(this).data('taxonomy');
 //    var term=$(this).data('term');
 //    var div='#date_div_'+taxonomy+'_'+term;
 //    if(status=='checked'){
 //        $(div).removeClass('wbm_hidden');
 //    }else{
 //        $(div).addClass('wbm_hidden');
 //    }
 //});
 $(document).on('click','.wbm_navigate_button',function(e){
     e.preventDefault();
     var type=$(this).data('type');
     var offset=$("#wbm_container").offset();
     if(type=='prev'){
            currentIndex= currentIndex-1;
            wbm_tabs.responsiveTabs('activate',currentIndex);
     }else{
         var checkValidate=checkValidity(tabId);
         if(!_.contains(noNeedTabs,tabId) && !checkValidate){
             jAlert(wbm_params.alert_msg,wbm_params.alert_heading );
             return false;
         }else{
             currentIndex=currentIndex+1;
             removeFromInactive(currentIndex);
             wbm_tabs.responsiveTabs('activate',currentIndex);
         }

     }
     window.scrollTo(offset.left,offset.top);
 });
$(document).on('change','.wbm_options',function(){
    var self=$(this);
    var taxonomy=self.data('taxonomy');
    var termId=self.data('term');
    var selectBoxContainer=hidden_container.find("#wbm_attr_"+taxonomy+"_"+termId).find(".wbm_selectbox_container");
    var select_count=self.data('count');
    if(self.val()!=''){
        var price=self.find('option:selected').data('price');
        var option_title=self.val();
        $(selectBoxContainer).find('#wbm_selects_'+taxonomy+"_"+termId+"_"+select_count).find('.wbm_option_title').val(option_title);
        $(selectBoxContainer).find('#wbm_selects_'+taxonomy+"_"+termId+"_"+select_count).find('.wbm_price').val(price);

    }else{
        $(selectBoxContainer).find('#wbm_selects_'+taxonomy+"_"+termId+"_"+select_count).find('.wbm_option_title').val('');
        $(selectBoxContainer).find('#wbm_selects_'+taxonomy+"_"+termId+"_"+select_count).find('.wbm_price').val('');
    }
    var checkValidate=checkValidity(tabId);
    if(checkValidate){
        activateOtherTabs(currentIndex);
        hidden_container.find("#wbm_attr_"+taxonomy+"_"+termId).find(".wbm_secondary_container").find('.wbm_check').val('yes');
    }else{
        inactiveOtherTabs(currentIndex);
        hidden_container.find("#wbm_attr_"+taxonomy+"_"+termId).find(".wbm_secondary_container").find('.wbm_check').val('no');
    }
    calculatePrice();
});
$(document).on('click','#wbm_book_button',function(e){
    e.preventDefault();
    jConfirm(wbm_params.confirm_msg, wbm_params.confirm_heading, function(r) {
            if(r==true){
                $("#wbm_booking_form").submit();
            }else{
                return false;
            }
    });

});
var inactiveOtherTabs=function(omitIndex){
    var newDeactiavte = _.without(tabVisited, omitIndex);
    $.each(newDeactiavte,function(index,value){
        wbm_tabs.responsiveTabs('disable', value);
    });
};
var activateOtherTabs=function(omitIndex){
        var newDeactiavte = _.without(tabVisited, omitIndex);
        $.each(newDeactiavte,function(index,value){
            wbm_tabs.responsiveTabs('enable', value);
        });
};
var loadAttributeTab=function(productId,taxonomy,termId){
     var div=$("#"+taxonomy+"_"+termId);
     $(div).block({message: null,
         overlayCSS: {
             background: '#fff',
             opacity: 0.6
         }
     });
     var data={
         'action':'wbm_load_attribute_tab',
         'productId':productId,
         'taxonomy':taxonomy,
         'termId':termId
     };
     $.post(wbm_params.ajaxURL, data, function (resp) {
            $(div).html(resp);
            $('.wbm_switch').lc_switch(wbm_params.yes_string,wbm_params.no_string);
            $("#wbm_accordion_"+taxonomy+"_"+termId).smk_Accordion({activeIndex: 1,showIcon: true,closeAble:false});
            loadHiddenDates(resp,taxonomy,termId);
            loadHiddenSelectbox(resp,taxonomy,termId);
            makeDatetimePicker(resp,taxonomy,termId);
            calculatePrice();
            $(div).unblock();
     });
 }
var loadHiddenSelectbox=function(resp,taxonomy,termId){
    var container_div=hidden_container.find("#wbm_attr_"+taxonomy+"_"+termId);
    var selectbox_container=$(container_div).find('.wbm_selectbox_container');
    $(resp).find('select').each(function(k,v){
        var title=$(v).data('title');
        var div=$('<div id="wbm_selects_'+taxonomy+'_'+termId+'_'+k+'">');
        var hidden_title=$('<input type="hidden" class="wbm_title" value="'+title+'">');
        var hidden_option_title=$('<input type="hidden" class="wbm_option_title" value="">');
        var hidden_price=$('<input type="hidden" class="wbm_price" value="">');
        div.append(hidden_title);
        div.append(hidden_option_title);
        div.append(hidden_price);
        selectbox_container.append(div);
    });
};
var loadHiddenDates=function(resp,taxonomy,termId){
    var container_div=hidden_container.find("#wbm_attr_"+taxonomy+"_"+termId);
    var date_container=$(container_div).find('.wbm_date_container');
    $(resp).find('.wbm_date_div').each(function(k,v){
        var title=$(v).data('title');
        var div=$('<div id="wbm_dates_'+taxonomy+'_'+termId+'_'+k+'">');
        var hidden_title=$('<input type="hidden" class="wbm_title" value="'+title+'">');
        var hidden_date=$('<input type="hidden" class="wbm_date" value="">');
        var hidden_time=$('<input type="hidden" class="wbm_time" value="">');
        div.append(hidden_title);
        div.append(hidden_date);
        div.append(hidden_time);
        date_container.append(div);
    });
};
var makeDatetimePicker=function(resp,taxonomy,termId){
$(resp).find(".wbm_datetime_input").each(function(k,v){
    var self=$(this);
    var type=self.data("type");
    var elementId;
    var dateContainer=$("#wbm_dates_"+self.data("taxonomy")+"_"+self.data("term")+"_"+self.data("count"));
    if(type=="date"){
            elementId="#wbm_checking_date_"+self.data("taxonomy")+"_"+self.data("term")+"_"+self.data("count");
            $(elementId).datetimepicker({
                timepicker:false,
                format:'d/m/Y',
                minDate:'+1970/01/02',
                formatDate:'Y/m/d',
                onChangeDateTime:function(dp,$input){
                    $(dateContainer).find(".wbm_date").val($input.val());
                    var checkValidate=checkValidity(tabId);
                    if(checkValidate){
                        activateOtherTabs(currentIndex);
                        console.log('validate');
                        hidden_container.find("#wbm_attr_"+self.data("taxonomy")+"_"+self.data("term")).find(".wbm_secondary_container").find('.wbm_check').val('yes');
                    }else{
                        inactiveOtherTabs(currentIndex);
                        console.log('not validate');
                        hidden_container.find("#wbm_attr_"+self.data("taxonomy")+"_"+self.data("term")).find(".wbm_secondary_container").find('.wbm_check').val('no');
                    }
                }
            });
    }else{
        elementId="#wbm_checking_time_"+self.data("taxonomy")+"_"+self.data("term")+"_"+self.data("count");
        $(elementId).datetimepicker({
            datepicker:false,
            formatTime:'g:i A',
            format:'g:i A',
            step:30,
            onChangeDateTime:function(dp,$input){
                    $(dateContainer).find(".wbm_time").val($input.val());
                    var checkValidate=checkValidity(tabId);
                    if(checkValidate){
                        activateOtherTabs(currentIndex);
                        hidden_container.find("#wbm_attr_"+self.data("taxonomy")+"_"+self.data("term")).find(".wbm_secondary_container").find('.wbm_check').val('yes');
                    }else{
                        inactiveOtherTabs(currentIndex);
                        hidden_container.find("#wbm_attr_"+self.data("taxonomy")+"_"+self.data("term")).find(".wbm_secondary_container").find('.wbm_check').val('no');
                    }
            }
        });
    }
});
};
var calculatePrice=function(){
    var temp_price=0;
    $('.wbm_price').each(function(){
        if($(this).val()!=""){
            temp_price+=parseFloat($(this).val());
        }
    });
    temp_price=parseFloat(initial_price)+temp_price;
    $("#wbm_temp_price").text(temp_price);
};
var clearAllPrice=function(taxonomy,termId){
    var container_div=hidden_container.find("#wbm_attr_"+taxonomy+"_"+termId);
    $(container_div).find('.wbm_check').val('no');
    $(container_div).find('.wbm_price').val('');
    $(container_div).find('.wbm_option_title').val('');
    $(container_div).find('.wbm_date').val('');
    $(container_div).find('.wbm_time').val('');
};
var arrangeFinishStep=function(){
var mainUl=$('<ul id="wbm_finish_step_list">');
var mainLi=$('<li>'+$("#wbm_main_attr").find(".wbm_title").val()+'</li>');
var attributesDiv=$('<div>');
var mainHidden=$('<input type="hidden" name="wbm_attributes[main][mainattr]" value="'+$("#wbm_main_attr").find(".wbm_title").val()+'">');
attributesDiv.append(mainHidden);
 $(".wbm_attr_container").each(function(k,v){
    var secondary=$(v);
    var check=secondary.find('.wbm_secondary_container').find('.wbm_check').val()=='yes'? true : false;
    if(check){
        var secondaryUl=$('<ul>');
        var secondaryPrice=secondary.find('.wbm_secondary_container').find('.wbm_price').val()!=""? wbm_params.currency_symbol +secondary.find('.wbm_secondary_container').find('.wbm_price').val():"";
        var secondaryLi=$('<li>'+secondary.find('.wbm_secondary_container').find('.wbm_title').val()+" "+ secondaryPrice+'</li>');
        var secondaryHidden=$('<input type="hidden" name="wbm_attributes[main]['+k+'][]" value="'+secondary.find('.wbm_secondary_container').find('.wbm_title').val()+" "+ secondaryPrice+'">');
        attributesDiv.append(secondaryHidden);
        var selectoboxUl=$('<ul>');
        secondary.find('.wbm_selectbox_container').find('div').each(function(m,n){
            var selectboxPrice=$(n).find('.wbm_price').val()!="" ? wbm_params.currency_symbol+$(n).find('.wbm_price').val() : "";
            var selectboxLi=$(n).find('.wbm_option_title').val()!=""? $('<li>'+$(n).find('.wbm_title').val()+" <i class='fa fa-arrow-right'></i> " +$(n).find('.wbm_option_title').val()+" "+selectboxPrice+'</li>') :"";
            secondaryLi.append(selectoboxUl.append(selectboxLi));
            var selectBoxHidden=$('<input type="hidden" name="wbm_attributes[main]['+k+'][selectbox][]" value="'+$(n).find('.wbm_title').val()+" => "+ $(n).find('.wbm_option_title').val()+" "+selectboxPrice+'">');
            attributesDiv.append(selectBoxHidden);
        });
        secondary.find('.wbm_date_container').find('div').each(function(m,n){
            var selectboxLi=$(n).find('.wbm_date').val()!=""&& $(n).find('.wbm_time').val()!="" ? $('<li>'+$(n).find('.wbm_title').val()+" <i class='fa fa-arrow-right'></i> " +$(n).find('.wbm_date').val()+" "+" "+$(n).find('.wbm_time').val()+'</li>'):"";
            var dateHidden=$('<input type="hidden" name="wbm_attributes[main]['+k+'][dates][]" value="'+$(n).find('.wbm_title').val()+" => "+ $(n).find('.wbm_date').val()+" "+$(n).find('.wbm_time').val()+'">');
            attributesDiv.append(dateHidden);
            secondaryLi.append(selectoboxUl.append(selectboxLi));
        });
        mainLi.append(secondaryUl.append(secondaryLi));
        //console.log(attributesDiv);
        $("#wbm_booking_form").append(attributesDiv);
    }
 });
mainUl.append(mainLi);
return mainUl;
};
var makeAttributeElement=function(){
    var menuArray = [],
        subArray;
    $('#wbm_finish_step_list li:not(ul li ul li)').each(function(i){
        var firstMenu = $.trim( $(this).text() );
        subArray = [];
        $(this).find('ul > li').each(function(i){
            var secondMenu = $.trim( $(this).text() );
            subArray.push(secondMenu);
        });
        menuArray[firstMenu] = subArray;
    })
    return menuArray;
};
var removeFromInactive=function(number){
    inactiveTabs= _.without(inactiveTabs,number);
    wbm_tabs.responsiveTabs('enable',number);
};
var checkValidity=function(elementId){
    var validate=true;
    $('#'+elementId +' input, #'+elementId+' select').each(function(k,v){
        if($(v).val()==""){
            validate=false;
            return false;
        }
    });
    return validate;
};
var makeFormElements=function(){
    var priceElement=$('<input type="hidden" name="wbm_product_price" value="'+$("#wbm_temp_price").text()+'" />');
    var checkElement=$('<input type="hidden" name="wbm_product_cart" value="1" />');
    $("#wbm_booking_form").append(priceElement);
    $("#wbm_booking_form").append(checkElement);
};
});