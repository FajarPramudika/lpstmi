function pixelform_form_alert(e, r) {
    "error" == r ? jQuery(".pixelform_form-alert").css("background", "#e9382b") : jQuery(".pixelform_form-alert").css("background", "#056105"), jQuery(".pixelform_form-alert").html(e), jQuery(".pixelform_form-alert").css("display", "block"), setTimeout(function() {
        jQuery(".pixelform_form-alert").css("display", "none")
    }, 5e3)
}
/* jQuery Validate Emails with Regex */
function pixelform_formvalidateEmail(Email) {
	var pattern = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
	return jQuery.trim(Email).match(pattern) ? true : false;
}

(function( $ ) {
	'use strict';
	
	$(".pixelform_form_submit").click(function(){
		var $this = $(this);
		var obj = $(this).parents('.pixelform_form_render');
		var form_id = $(this).data('form_id');
		obj.find('.pixelform_form_req_field').each(function(){
			if( $(this).val().length == 0 ){
				$(this).addClass('pixelform_form_empty_error');
			}
		});
		var messages = {};
		var m = 0;
		$('.pixelform_form_messages input').each(function () {
			messages[m] = $(this).val();
			m++;
	    });
			
		if( $('.pixelform_form_empty_error').length == 0 ){
			var fieldType;
			var attachments = new FormData();
			var fields = {};
			var label,attach_invalid;
			obj.find('.form-group').each(function(){
				fieldType = $(this).attr('data-fieldtype');
				
				if( fieldType != 'button' && fieldType != 'select' ){
					if( $(this).find('.pixelform_form_label').text() != '' ){
						label = ($(this).find('.pixelform_form_label').text()).replace(' ', '_');
					}else if( ($(this).find('.form-control').attr('placeholder')) != '' ){
						label = ($(this).find('.form-control').attr('placeholder')).replace(' ', '_');
					}
				}
				
				switch( fieldType ){
					case 'text':
						fields[label] = $(this).find('input').val();
					break;
					case 'textarea':
						fields[label] = $(this).find('textarea').val();
					break;
					case 'number':
						fields[label] = $(this).find('input').val();
					break;
					case 'email':
						if( ($(this).find('input').val().length != 0) && pixelform_formvalidateEmail( $(this).find('input').val() ) == true ){
							fields[label] = $(this).find('input').val();
							fields['user_email'] = $(this).find('input').val();
						}else{
							$(this).find('input').addClass('pixelform_form_empty_error invalid_email');
							pixelform_form_alert( messages[4], "error" );
							setTimeout(function(){
								obj.find('input').removeClass('pixelform_form_empty_error invalid_email');
							}, 7000);
						}
					break;
					case 'select':
						fields['Options'] = $(this).find('select').val();
					break;
					case 'radiogroup':
						fields[label] = $(this).find('input').val();
					break;
					case 'checkboxgroup':
						var options = '';
						var i =0;
						$(this).find('input:checked').each(function(){
							options += $(this).val() + ',';
							i++;
						});
						fields[label] = options.slice(0, -1);
					break;
					case 'date':
						fields[label] = $(this).find('input').val();
					break;
					case 'fileupload':
						var input_file = $(this).find('input[type=file]');
							$.each(input_file[0].files, function(i, file) {
								attachments.append('file-'+i, file);
							});
					break;
				}
				
			});
			attachments.append('action', 'pixelform_form_submit'); //Ajax Action
			attachments.append('form_id', form_id);
			for ( var key in fields ) {
				attachments.append(key, fields[key]);
			}
			
			
			if(attach_invalid == 'invalid'){
				pixelform_form_alert( "The file you tried to attach is invalid.", "error" );
				setTimeout(function(){
					obj.find('.pixelform_form_req_field').removeClass('pixelform_form_empty_error');
				}, 7000);
				return;
			}
			
			if($(".invalid_email").length != 0){
				return false;
			}
			
			$('.mt-btn-loader').css('display','inline-block');
			 jQuery.ajax({
				 url: ajax_obj.ajax_url,
				 data: attachments,
				 cache: false,
				 contentType: false,
				 processData: false,
				 method: 'POST',
				 type: 'POST', // For jQuery < 1.9
				 success: function(response){
					var res = JSON.parse(response);
					if(typeof(res.success) !== "undefined"){
						$('.mt-btn-loader').hide(2000);
						 obj.find('.form-group').each(function(){
							$(this).find('.form-control').val('');
						});
						pixelform_form_alert( messages[0], "success" );
					}else{
						pixelform_form_alert( res.error, "error" );
					}
					$('.mt-btn-loader').hide();
				 },
				 error: function(response){
					 pixelform_form_alert( messages[1], "error" );
					 $('.mt-btn-loader').hide();
				 }
			 });
			
		}else{
			pixelform_form_alert( messages[2], "error" );
			setTimeout(function(){
				obj.find('.pixelform_form_req_field').removeClass('pixelform_form_empty_error pixelform_form_error');
			}, 7000);
		}
		
	});
	
// 	$('.custom_date').datepicker({
// 			format: 'dd-mm-yyyy',
// 			});
	
	
	//Newsletter Submit
	$(".pixelform_form_newsletter_submit").click(function(){
		var $this = $(this);
		var name_field = $this.parents().find('.responder_data').data('name_field');
		var formid = $this.parents().find('.responder_data').data('formid');
		var email_field = $this.parents().find('.responder_data').data('email_field');
		var obj = $(this).parents('.pixelform_form_render');
	
		obj.find('.pixelform_form_req_field').each(function(){
			if( $(this).val().length == 0 ){
				$(this).addClass('pixelform_form_empty_error');
			}
		});
		
		var messages = {};
		var m = 0;
		$('.pixelform_form_messages input').each(function () {
			messages[m] = $(this).val();
			m++;
		});
		
		if( $('.pixelform_form_empty_error').length == 0 ){
			var fieldType;
			var fields = {};
			var label;
			//selected name field
			fields['name'] = $('input[name='+name_field+']').val();
			obj.find('.form-group').each(function(){
				fieldType = $(this).attr('data-fieldtype');
				
				if( fieldType != 'button' ){
					if( $(this).find('.pixelform_form_label').text() != '' ){
						label = ($(this).find('.pixelform_form_label').text()).replace(' ', '_');
					}else if( ($(this).find('.form-control').attr('placeholder')) != '' ){
						label = ($(this).find('.form-control').attr('placeholder')).replace(' ', '_');
					}
				}
				
				switch( fieldType ){
					case 'text':
						fields[label] = $(this).find('input').val();
					break;
					case 'email':
						if( ($(this).find('input').val().length != 0) && pixelform_formvalidateEmail( $(this).find('input').val() ) == true ){
							fields['email'] = $(this).find('input').val();
						}else{
							$(this).find('input').addClass('pixelform_form_empty_error invalid_email');
							pixelform_form_alert( messages[4], "error" );
							setTimeout(function(){
								obj.find('input').removeClass('pixelform_form_empty_error invalid_email');
							}, 5000);
						}
					break;
					case 'number':
						fields['number'] = $(this).find('input').val();
					break;
					
				}
				
			});
			
			if($(".invalid_email").length != 0){
				return false;
			}
			
			$('.mt-btn-loader').css('display','inline-block');
			
			 $.ajax({
				 url: ajax_obj.ajax_url,
				 data: {
					 action:'pixelform_form_newsletter_submit',
					 formid: formid,
					 fields: fields
				 },
				 type: 'POST',
				 success: function(response){ 
				     console.log(response);
					 var res = JSON.parse(response);					 
					 if( res.msg == 'success' ){
						 obj.find('.form-group').each(function(){
							$(this).find('.form-control').val('');
						 });
						pixelform_form_alert( messages[0], "success" );
					 }else{
						 pixelform_form_alert( messages[1], "error" );
					 }
					  $('.mt-btn-loader').hide();
				 },
				 error: function(response){
					 pixelform_form_alert( messages[1], "error" );
					 $('.mt-btn-loader').hide();
				 }
			 });
		}else{
			pixelform_form_alert( 'Please fill all the required fields.', "error" );
			setTimeout(function(){
				obj.find('.pixelform_form_req_field').removeClass('pixelform_form_empty_error pixelform_form_error');
			}, 5000);
		}
	});
	
	//Like Btn
	$(document).on('click','.likes_btn', function(){
	    var ids = $(this).attr('data-id');
	    $(this).next().children().addClass('liked');
	    $.ajax({
	        type: 'POST',
            url: ajax_obj.ajax_url,
            data: {'id': ids,'action':'post_like_func'},
            success: function(response) {
                var result = JSON.parse(response);
                if(result.status == 'true'){
                    $('.liked').text(result.likes);
                    $('.lc_post_likes ').removeClass('liked');
                }
            }
        });
       
	});
	
	
})( jQuery );