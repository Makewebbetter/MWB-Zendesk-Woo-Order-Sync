(function( $ ) {
	'use strict';
	// zndsk_ajax_object variables.
	var ajaxUrl              = zndsk_ajax_object.ajax_url;
	var zndskSecurity        = zndsk_ajax_object.zndskSecurity;
	var zndskMailSuccess     = zndsk_ajax_object.zndskMailSuccess;
	var zndskMailFailure     = zndsk_ajax_object.zndskMailFailure;
	var zndskMailAlreadySent = zndsk_ajax_object.zndskMailAlreadySent;
	jQuery( document ).ready(
		function() {
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
		}
	);
})( jQuery );
