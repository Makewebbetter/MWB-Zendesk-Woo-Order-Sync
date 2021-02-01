(function( $ ) {
	'use strict';
	// zndsk_ajax_object variables.
	var ajaxUrl              = zndsk_ajax_object.ajax_url;
	var zndskSecurity        = zndsk_ajax_object.zndskSecurity;
	var zndskMailSuccess     = zndsk_ajax_object.zndskMailSuccess;
	var zndskMailFailure     = zndsk_ajax_object.zndskMailFailure;
	var zndskMailAlreadySent = zndsk_ajax_object.zndskMailAlreadySent;
	jQuery(document).ready(function() {

		jQuery( '.mwb-reject-button' ).on(
			'click',function(){

				jQuery( '#zndsk_loader' ).show();

				jQuery.post(
					ajaxUrl , {'action' : 'mwb_zndsk_suggest_later', 'zndskSecurity' : zndskSecurity }, function(response){
						location.reload();
					}
				);
			}
		);
		jQuery( '.mwb-accept-button' ).on(
			'click', function() {
				jQuery( '#zndsk_loader' ).show();
				jQuery.post(
					ajaxUrl , { 'action' : 'mwb_zndsk_suggest_accept', 'zndskSecurity' : zndskSecurity}, function( response ) {

						if ( response == '"success"' ) {
							alert( zndskMailSuccess );
							location.reload();
						} else if ( response == '"alreadySent"' ) {
							alert( zndskMailAlreadySent );
							location.reload();
						} else {
							alert( zndskMailFailure );
							location.reload();
						}
					}
				);
			}
		);
		
		// Drag and Drop initialization.

		$('.mwb-zndsk-field-drag').draggable({
			cursor: "move",
			revert: true,
			distance: 50,
			containment: "parent",
			connectToSortable: "#dvdest",
			drop: function(event, ui) {
				$(this).find('ul').append(ui.draggable);
			}
		});
		
		$('.mwb-zndsk-field-drop').droppable({

			hoverClass: "hoverDrop",
			tolerance: "pointer",
			drop: function(event, ui) {
				$(this).append(ui.draggable);
			}
		});

		// Order config options ajax handling.
		var latest_orders_count = '';
		var source_fields = [];
		var selected_source_fields = [];

		$(document).on( 'submit', '#mwb-zndsk-order-config-form', function(e){

			e.preventDefault();

			// Reinitialize array on every click.
			source_fields = [];
			selected_source_fields = [];

			latest_orders_count = $('#mwb-zndsk-latest-orders-count').val();

			$('#mwb-zndsk-dvsource li').each(function(i, li) {
				source_fields.push( $(this).data('name') );
			});
			
			$('#mwb-zndsk-dvdest li').each(function(i, li) {
				selected_source_fields.push( $(this).data('name') );
			});

			jQuery.post(ajaxUrl,{
				action:'mwb_zndsk_save_order_config_options', 
				latest_orders_count:latest_orders_count,
				source_fields:source_fields,
				selected_source_fields:selected_source_fields,
				zndskSecurity:zndskSecurity
			},
			function(data){

				// var selected_options_saved_html = '';
				
				// if( 'true' == data ) {

				// 	selected_options_saved_html = "<p class='notice_text' >" + selected_options_saved_text + "</p>";
				// }

				// else {

				// 	selected_options_saved_html = "<p class='notice_text' >" + selected_options_not_saved_text + "</p>";
				// }

				// $('.arfw_notice').html( selected_options_saved_html );
				// $('.notice_text').delay( 4000 ).fadeOut( 'slow' );
				
			});
		});

	});

	var acc = document.getElementsByClassName("zndsk_accordion");
    var i;
      $(document).on("click","button.data.zndsk_accordion", function(e) {
      	e.preventDefault();
        this.classList.toggle("active");
       var panel = this.nextElementSibling;
        if (panel.style.maxHeight){
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
        } 
      });


})( jQuery );
