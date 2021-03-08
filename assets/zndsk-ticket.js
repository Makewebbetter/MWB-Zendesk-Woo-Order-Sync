jQuery(document).ready(function( $ ) {
    $('.ticket-form').hide();
    $('.hitbutton').click(function(){
        $('.error').hide();  
        $('.hitbutton').hide();
        $('.ticket-table').hide();
        $('.ticket-form').show();
       
    });
    $('.return-back').click(function(){
        $('.ticket-form').hide();
        $('.ticket-table').show();
        $('.hitbutton').show();
        
    });        
});