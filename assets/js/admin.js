jQuery( document ).ready( function ( e ) {
	$('input, textarea').each(function(e, i){
		 var id = $(this).attr('id');
		 $(this).parents('tr').addClass(id);
	})
	/***** Enable Plugin *****/

	var checkEnableStatus = function(){
		 var checked  = $('#enable_refresh').is(":checked")
		 if(!checked){
			 $('tr:not(.enable_refresh)').addClass('enable-hidden');
		 }else{
			 $('.enable-hidden').removeClass('enable-hidden');
		 }
	}

	$('#enable_refresh').on('click', checkEnableStatus);
	checkEnableStatus();

	/***** Random Refresh Toggle *****/
	var checkRandomStatus = function(){
		 var checked  = $('#random').is(":checked")
		 if(!checked){
			 $('.min_refresh, .max_refresh').addClass('random-hidden');
			 $('#delay').prop('disabled', false);
		 }else{
			  $('.min_refresh, .max_refresh').removeClass('random-hidden');
				$('#delay').prop('disabled', true);
		 }
	}

	$('#random').on('click', checkRandomStatus);
	checkRandomStatus();

	/***** Random Refresh Toggle *****/
	var checkURLStatus = function(){
		 var specific  = $('#redirect_specific').is(":checked");
		 var white_list = $('#redirect_list').is(":checked");
		  $('.specific_url, .redirect_list').addClass('hidden');
		 if(specific){
			 $('.specific_url').removeClass('hidden');
		 }else if(white_list){
				$('.redirect_list').removeClass('hidden');
		 }
	}

	$('#redirect_specific, #white_list').on('click', checkURLStatus);
	checkURLStatus();
});
