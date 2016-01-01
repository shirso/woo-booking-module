jQuery(function($){
   if(typeof wbm_attribute_page!='undefined'){

       $(document).on('change','#hd_wbm_attribute_type', function(){
         if($(this).is(':checked')){
            $('.wbm_image').removeClass('wbm-hidden');
         }else{
             $("#hd_wbm_attribute_image").val('');
             $('.wbm_image').addClass('wbm-hidden');
         }
      });

       var mediaUploader = null;
       $(document).on('click','#btn_wbm_attribute_image_upload',function(e){
           mediaUploader = wp.media({
               multiple: false
           });
           mediaUploader.on('select', function () {
               $("#hd_wbm_attribute_image").val(mediaUploader.state().get('selection').toJSON()[0].url);
               mediaUploader = null;
           });
           mediaUploader.open();
           e.preventDefault();
       })
   }
    if(typeof wbm_product_page!='undefined'){
        if($("#_wbm_check").is(':checked')){
            $('.wbm_configs_options').removeClass('show_if_wbm_panel');
        }else{
            $('.wbm_configs_options').addClass('show_if_wbm_panel');
        }
        $(document).on('change','#_wbm_check',function(){
            if($(this).is(":checked")){
                $('.wbm_configs_options').removeClass('show_if_wbm_panel');
            }else{
                $('.wbm_configs_options').addClass('show_if_wbm_panel');
                if($("#wbm_data_default_configuration").is(':visible')){
                    $("#wbm_data_default_configuration").hide();
                }
            }
        });

        $(document).on('click',"#wbm_add_attribute_base",function(e){
            e.preventDefault();
            attributeSave('wbm_base_attribute',postId,'wbm_save_base_attribute');
        });
        $(document).on('click',"#wbm_add_attribute_sub",function(e){
            e.preventDefault();
            attributeSave('wbm_sub_attribute',postId,'wbm_save_sub_attribute');
        });
        $(document).on('click','#wbm_refresh_button',function(e){
            e.preventDefault();
            $('#wbm_attribute_tab').block({message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            var this_page = window.location.toString();
            this_page = this_page.replace('post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes.post_id + '&action=edit&');
            $('#wbm_attribute_tab').load(this_page + ' #wbm_attribute_tab', function () {
                $('#wbm_attribute_tab').unblock();
            })

        })
    }
  if(typeof  wbm_options_page!='undefined'){
      var optionsForm = {};
      optionsForm=$("#wbm_attribute_select").sheepIt({
          allowRemoveLast: true,
          allowRemoveCurrent: true,
          allowAdd: true,
          allowRemoveAll: false,
          allowAddN: false,
          separator: '<hr class="wbm-dvdr"/>',
          minFormsCount: 0,
          iniFormsCount: 0,
          data:injectData,
          nestedForms: [
              {
                  id: 'wbm_attribute_select_#index#_options',
                  options: {
                      indexFormat: '#index_options#',
                      allowRemoveLast: true,
                      allowRemoveCurrent: true,
                      allowAdd: true,
                      allowRemoveAll: false,
                      allowAddN: false,
                      minFormsCount: 0,
                      iniFormsCount: 0,
                      separator: ''
                  }
              }
          ]
      });
      $(document).on('change','#wbm_main_attribute_option_check',function(){
          if($(this).is(':checked')){
            $("#wbm_attribute_select").removeClass('wbm-hidden');
          }else{
              $("#wbm_attribute_select").addClass('wbm-hidden');
          }
      });

      $(document).on('click','#wbm_attribute_option_save_button',function(e){
          e.preventDefault();
          var actualText=$(this).text();
          var self=$(this);
          self.attr('disabled','disabled');
          self.text(loadingText);
          var data = {
              'action': 'wbm_attribute_option_save',
              'formData': $("#wbm_attribute_option_form").serialize(),
              'postId': productId,
              'taxonomy':taxonomy,
              'term':termId
          };

          $.post(ajaxurl, data, function (resp) {
              if(resp=='success'){
               self.removeAttr('disabled');
               self.text(actualText);
              }
          });
      });
  }
  if(typeof  wbm_dates_page!='undefined'){
      $(document).on('change','#wbm_main_attribute_date_check',function(){
          if($(this).is(':checked')){
              $("#wbm_attribute_date").removeClass('wbm-hidden');
          }else{
              $("#wbm_attribute_date").addClass('wbm-hidden');
          }
      });
      var datesForm = {};
      datesForm=$("#wbm_attribute_date").sheepIt({
          allowRemoveLast: true,
          allowRemoveCurrent: true,
          allowAdd: true,
          allowRemoveAll: false,
          allowAddN: false,
          separator: '<hr class="wbm-dvdr"/>',
          minFormsCount: 0,
          iniFormsCount: 0,
          data:injectData
      });
     $(document).on('click','#wbm_attribute_date_save_button',function(e){
         e.preventDefault();
         var actualText=$(this).text();
         var self=$(this);
         self.attr('disabled','disabled');
         self.text(loadingText);
         var data = {
             'action': 'wbm_attribute_date_save',
             'formData': $("#wbm_attribute_date_form").serialize(),
             'postId': productId,
             'taxonomy':taxonomy,
             'term':termId
         };
         $.post(ajaxurl, data, function (resp) {
             if(resp=='success'){
                 self.removeAttr('disabled');
                 self.text(actualText);
             }
         });
     });

    }

  $(document).on('click','.wbm_image_upload_button',function(e){
      var self=$(this);
      mediaUploader = wp.media({
          multiple: false
      });
      mediaUploader.on('select', function () {
          self.prev().val(mediaUploader.state().get('selection').toJSON()[0].url);
          mediaUploader = null;
      });
      mediaUploader.open();
      e.preventDefault();
  });
  var attributeSave=function(selectId,postId,action){
      if($("#"+selectId).val()!=''){
          var data = {
              'action': action,
              'attribute': $('#'+selectId).val(),
              'postId':postId
          };
          $('#wbm_attribute_tab').block({message: null,
              overlayCSS: {
                  background: '#fff',
                  opacity: 0.6
              }
          });
          $.post(ajaxurl, data, function (resp) {
              var this_page = window.location.toString();
              this_page = this_page.replace('post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes.post_id + '&action=edit&');
              $('#wbm_attribute_tab').load(this_page + ' #wbm_attribute_tab', function () {
                  $('#wbm_attribute_tab').unblock();
              })
          })

      }
  }
});