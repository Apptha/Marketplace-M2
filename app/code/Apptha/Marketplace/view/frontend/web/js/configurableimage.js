require([
        "jquery",
        "mage/calendar"
        ], function($){
$('.configurable_image').on('change', function(e) {
        	   $('#value_image_type').val($(this).val());

        	   if($(this).val() == 'image_all'){
        	   $('#image_unique_content').hide();
        	   $('#image_all_content').show();
        	   }
        	   else if($(this).val() == 'image_unique'){
        	   $('#image_unique_content').show();
        	   $('#image_all_content').hide();
        	   }
        	   else{
        	   $('#image_all_content').hide();
        	   $('#image_unique_content').hide();
        	   }
        	   });
        	   $('.image_select_unique').on('change', function(e) {
        	       $('#value_image_select_unique').val($(this).val());
        	   $('.image_unique_value').hide();
        	   var partial_it = $('#image_select_unique').val();
        	       $('#image_unique_content_'+partial_it).show();
        	       });

        	   // Price
        	   $('.configurable_price').on('change', function(e) {
        	   if($(this).val() == 'price_all'){
        	   $('#price_all_content').show();
        	   $('#price_unique_content').hide();
        	   }else if($(this).val() == 'price_unique'){
        	   $('#price_unique_content').show();
        	   $('#price_all_content').hide();
        	   }else{
        	   $('#price_all_content').hide();
        	   $('#price_unique_content').hide();
        	   }
        	   });
        	   $('.price_select_unique').on('change', function(e) {
        	   $('.price_unique_value').hide();
        	   var partial_it = $('#price_select_unique').val();
        	       $('#price_unique_content_'+partial_it).show();
        	       });

        	   // Quantity
        	   $('.configurable_qty').on('change', function(e) {
        	   if($(this).val() == 'qty_all'){
        	   $('#qty_all_content').show();
        	   $('#qty_unique_content').hide();
        	   }else if($(this).val() == 'qty_unique'){
        	   $('#qty_unique_content').show();
        	   $('#qty_all_content').hide();
        	   }else{
        	   $('#qty_all_content').hide();
        	   $('#qty_unique_content').hide();
        	   }
        	   });
        	   $('.qty_select_unique').on('change', function(e) {
        	   $('.qty_unique_value').hide();
        	   var partial_it = $('#qty_select_unique').val();
        	       $('#qty_unique_content_'+partial_it).show();
        	    });

});