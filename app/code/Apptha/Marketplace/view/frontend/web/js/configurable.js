 require([
                "jquery",
                "mage/calendar"
            ], function($){
       $('#btn_configurable_product_generate').on('click', function(e) {
       configurableAssociatedProductFlag = 1;
       $('#variants_generate_flag').val('1');
       $('#configurable_variant_error').hide();
       var configurableProductClone = $('#configurable_product_prepared_data').html();
               $('#configurable_section_content_form').html(configurableProductClone);
               $('#configurable_product_prepared_data').html('');
               $('#configurable-product-form').hide();
               $('#current_variants').show();
               $('#select-attribute').show();
           $('#select-attribute-status').addClass('active');
           $('#summary').hide('active');
           $('#summary-status').removeClass('active');
           $('#btn_configurable_product_next').show();
           $('#btn_configurable_product_generate').hide();
           $('#existing_configurable_section_content_form').hide();
       });

       $('#btn_configurable_product_next').on('click', function(e) {
       if($('#bulk-images-price').is(":visible")){

       var bulkValidateFlag = typeValidateFlag = 0;
           var configurablePriceChecked = $('input[name="configurable_price"]:checked').val();
           var configurableQtyChecked = $('input[name="configurable_qty"]:checked').val();

           if(configurablePriceChecked == 'price_unique'){
           var priceSelectUniqueValue = $('#price_select_unique').val();
           if(priceSelectUniqueValue == ''){
            bulkValidateFlag = 1;
               }else{
               $(".price_unique_value_for_validate_"+priceSelectUniqueValue).each(function(){
                       if($(this).val() == '' ){
                       bulkValidateFlag = 1;
                       }
                       if(!$.isNumeric($(this).val())){
                           typeValidateFlag = 1;
                           }
                       });
               }
               }

           if(configurablePriceChecked == 'price_all'){
               $(".price_all_value_for_validate").each(function(){
               if($(this).val() == ''){
               bulkValidateFlag = 1;
               }
               if(!$.isNumeric($(this).val())){
                   typeValidateFlag = 1;
                   }
               });
               }

           if(configurableQtyChecked == 'qty_unique'){
            var qtySelectUniqueValue = $('#qty_select_unique').val();
               if(qtySelectUniqueValue == ''){
                bulkValidateFlag = 1;
                   }else{
                   $(".qty_unique_value_for_validate_"+qtySelectUniqueValue).each(function(){
                           if($(this).val() == '' ){
                           bulkValidateFlag = 1;
                           }

                           if(!$.isNumeric($(this).val())){
                           typeValidateFlag = 1;
                           }

                           });
                   }
               }

               if(configurableQtyChecked == 'qty_all'){
               $(".qty_all_value_for_validate").each(function(){
               if($(this).val() == ''){
               bulkValidateFlag = 1;
               }
               if(!$.isNumeric($(this).val())){
                   typeValidateFlag = 1;
                   }
               });
               }

               if(bulkValidateFlag == 1){
               $("#bulk_price_qty_error_msg").show();
               return false;
               }else{
               $("#bulk_price_qty_error_msg").hide();
               }

               if(typeValidateFlag == 1){
                   $("#type_price_qty_error_msg").show();
                   return false;
                   }else{
                   $("#type_price_qty_error_msg").hide();
                   }




       $('#bulk-images-price').hide();
       $('#summary').show();
       $('#summary-status').addClass('active');

       $('#btn_configurable_product_next').hide();
       $('#btn_configurable_product_generate').show();


       $('#configurable_product_bulk_form').submit();
       }

           if($('#attribute-values').is(":visible")){
           var optionsCheckboxCount = 0;
           var previousOptionClass = currentOptionClass = selectedClass = '';
           var classNumItems = $('.attribute-options-ul').length;
       $(".attribute-options-checkbox").each(function(){
       currentOptionClass = $(this).attr('title');
       if(previousOptionClass == '' || previousOptionClass != currentOptionClass){
       if ($(this).is(":checked")) {
           optionsCheckboxCount = optionsCheckboxCount + 1;
           previousOptionClass = $(this).attr('title');
           }
           }
           });

           if(optionsCheckboxCount == classNumItems){
          $('#attribute-values').hide();
   $('#bulk-images-price').show();
           $('#btn_configurable_product_back').removeAttr('disabled');
           $('#bulk-images-price-status').addClass('active');
           $('#attribute-options-error').hide();


           // To empty attribute values list
           $('#bulk-images-list').empty();
           // To submit select attribute form
           $('#configurable_options_form').submit();


       }else{
       $('#attribute-options-error').show();
       }

           }
           if($('#select-attribute').is(":visible")){
               var checkboxFlag = 0;
           $(".attribute-checkbox").each(function(){
           if ($(this).is(":checked")) {
               if(checkboxFlag == 0){
           $('#select-attribute').hide();
                   $('#attribute-values').show();
                   $('#btn_configurable_product_back').removeAttr('disabled');
                   $('#attribute-values-status').addClass('active');
                   $('#select-attributes-error').hide();
                   checkboxFlag = 1;
             }
               }
               });
           if(checkboxFlag == 0){
           $('#select-attributes-error').show();
           }else{
               // To empty attribute values list
               $('#attribute-values-list').empty();
               // To submit select attribute form
               $('#configurable_attribute_form').submit();
           }
           }
          });

       $('#btn_configurable_product_back').on('click', function(e) {

       $('#btn_configurable_product_next').show();
       $('#btn_configurable_product_generate').hide();

       if($('#attribute-values').is(":visible")){
       $('#attribute-values-status').removeClass('active');
           $('#attribute-values').hide();
       $('#select-attribute').show();
       $('#btn_configurable_product_back').attr('disabled','disabled');
       $('#attribute-options-error').hide();
       }
       if($('#bulk-images-price').is(":visible")){
       $('#bulk-images-price-status').removeClass('active');
          $('#bulk-images-price').hide();
          $('#attribute-values').show();
           }
       if($('#summary').is(":visible")){
       $('#summary-status').removeClass('active');
           $('#summary').hide();
           $('#bulk-images-price').show();
           $('#next_span_menu').html($('#next_span_menu_label').val());
           }
           });


            // Toggle effect create configurable product
        $('#btn_configurable_product').on('click', function(e) {
        if($('#form_product_validate').valid() == false){
        return false;
            }
        $('.configurable-product-form').toggle("slide", {
        direction: "right"
        }, 1000);
        });

        $('#btn_configurable_product_cancel').on('click', function(e) {
            $('.configurable-product-form').toggle("slide", {
                 direction: "right"
             }, 1000);
        $('#btn_configurable_product_back').attr('disabled','disabled');
        $('#select-attribute').show();
        $('#attribute-values').hide();
        $('#bulk-images-price').hide();
        $('#summary').hide();

        $('#attribute-values-status').removeClass('active');
        $('#bulk-images-price-status').removeClass('active');
        $('#summary-status').removeClass('active');
        });

        // Update configurable attributes
        $( document ).ready(function() {
        var productType = $('#product_type').val();
        var isConfigurableProduct = $('#is_configurable_product').val();

            if(productType == 'configurable' || isConfigurableProduct == 1){
            $('#qty').attr('disabled','disabled');
            $('#qty').hide();
                $('#special_price_section').hide();
        var attributeSetId = $('#default_attribute_set_id').val();
        var currentProductId = $('#current_product_id').val();
        $('#configurable-product-loader').show();
             $.ajax({
                type: "POST",
                url: $('#configurable_attributes_ajax_url').val(),
                data: {attribute_set_id: attributeSetId,current_product_id: currentProductId},
                success: function(transport){
                $('#select-attributes-list').html(transport);
                $('#configurable-product-loader').hide();
                }
                });
            }
        });

        $('#attribute_set').on('change', function(e) {
        var productType = $('#product_type').val();
        if(productType == 'configurable'){
    var attributeSetId = $('#default_attribute_set_id').val();
    if(attributeSetId){
    attributeSetId = $('#default_attribute_set_id').val();
        }
    var currentProductId = $('#current_product_id').val();
     $('#configurable-product-loader').show();
         $.ajax({
             type: "POST",
             url: $('#configurable_attributes_ajax_url').val(),
              data: {attribute_set_id: attributeSetId,current_product_id: currentProductId},
             success: function(transport){
             $('#select-attributes-list').html(transport);
             $('#configurable-product-loader').hide();
             }
             });
        }
        });

        $('#product_type').on('change', function(e) {
            var productType = $('#product_type').val();
            if(productType == 'configurable'){
	            $('#marketplace_downloadable_product_div').hide()
                    $('#qty').attr('disabled','disabled');
                    $('#qty').hide();
                    $('#special_price_section').hide();

                $('#marketplace-configurble-product-content').show();
        var attributeSetId = $('#default_attribute_set_id').val();
        if(attributeSetId){
        attributeSetId = $('#default_attribute_set_id').val();
            }
        var currentProductId = $('#current_product_id').val();
         $('#configurable-product-loader').show();
             $.ajax({
                 type: "POST",
                 url: $('#configurable_attributes_ajax_url').val(),
                  data: {attribute_set_id: attributeSetId,current_product_id: currentProductId},
                 success: function(transport){
                 $('#select-attributes-list').html(transport);
                 $('#configurable-product-loader').hide();
                 }
                 });
            }else{
            $('#qty').removeAttr('disabled','disabled');
            $('#qty').show();
                $('#special_price_section').show();
                $('#marketplace-configurble-product-content').hide();
                }
            });
 $('#product_type').on('change', function(e) {
            var productType = $('#product_type').val();
            if(productType == 'downloadable'){

            	$('.downloadable-quantity').hide();
                $('#marketplace_downloadable_product_div').show();





            }
            });
 $('#product_type').on('change', function(e) {
            var productType = $('#product_type').val();
            if(productType == 'virtual'){

               $('.downloadable-quantity').show()
                $('#marketplace_downloadable_product_div').hide();



            }
            });
          // Ajax for get configurable attributes value
           $("#configurable_attribute_form").on('submit',(function(e){
               e.preventDefault();
                   $('#configurable-product-loader').show();
                   $.ajax({
                       url: $('#configurable_options_ajax_url').val(),
                       type: 'POST',
                       success: function (transport) {
                       $('#attribute-values-list').html(transport);
                       $('#configurable-product-loader').hide();

                       },
                       data: new FormData(this),
                       cache: false,
                       contentType: false,
                       processData: false
                   });

               }));

           // Ajax Configurable attibutes option values for bulk images & price
           $("#configurable_options_form").on('submit',(function(e){
               e.preventDefault();
                   $('#configurable-product-loader').show();
                   $.ajax({
                       url: $('#configurable_bulk_ajax_url').val(),
                       type: 'POST',
                       success: function (transport) {
                       $('#bulk-images-list').html(transport);
                       $('#configurable-product-loader').hide();
                       },
                       data: new FormData(this),
                       cache: false,
                       contentType: false,
                       processData: false
                   });

               }));
          });