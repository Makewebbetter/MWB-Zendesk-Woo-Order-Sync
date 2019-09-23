jQuery(document).ready(function(){
    jQuery('.mwb-reject-button').on('click',function(){

        jQuery('#zndsk_loader').show();

        jQuery.post( zndsk_ajax_object.ajax_url , {'action' : 'mwb_zndsk_suggest_later'}, function(response){
            location.reload();
        });
    });
    jQuery('.mwb-accept-button').on('click',function(){  
        jQuery('#zndsk_loader').show();         
        jQuery.post( zndsk_ajax_object.ajax_url , { 'action' : 'mwb_zndsk_suggest_accept'}, function( response ) {

            if( response != null ) {

                var data_status = response;

                if( data_status == 'success' ) {

                    alert('Sent');
                    location.reload();
                }
                else{
                    alert('Failed');
                    location.reload();
                }
            }
            else {
                alert( 'Failed' );
                location.reload();
            }
        });   
    });
});