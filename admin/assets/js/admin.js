jQuery(function($) {

	jQuery(document).ready( function(){
		jQuery('#sync_old_order_form').on('click', '.button-primary', function(e) {
			e.preventDefault();
			$(this).prop("disabled",true);

			$.getJSON( "results.json", function( data ) {

				var obj = jQuery.parseJSON( data );
				$.each( obj, function( key, val ) {
					// alert(key + " = " + val.ID);
					var ID = val.ID;

					jQuery.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							action : 'contacts_sync',
							ID : ID
						},
						success : function( response ) {
							// alert(response);
						}
					});

				});
			});


			$.getJSON( "results1.json", function( data ) {

				var obj = jQuery.parseJSON( data );
				$.each( obj, function( key, val ) {
					// alert(key + " = " + val.ID);
					var ID = val.ID;

					jQuery.ajax({
						url : ajaxurl,
						type : 'post',
						data : {
							action : 'contacts_sync_update',
							ID : ID
						},
						success : function( response ) {
							// alert(response);
						}
					});
				});
			});
		});
	});
});