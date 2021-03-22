jQuery(document).ready(function( $ ) {
  var email = $('#mwb-zendsk-email').val();
    $('.mwb-zendesk-ticket-form').hide();
    $('.mwb-zendesk-hitbutton').click(function(){
      $('#select_box_email').hide(); 
      $('.mwb-error-messege').hide();  
      $('.mwb-zendesk-hitbutton').hide();
      $('.mwb-zndsk-ticket-table').hide();
      $('.mwb-zendesk-ticket-form').show();   
  });
    $('.return-back').click(function(){
      $('.mwb-zendesk-ticket-form').hide();
      $('.mwb-zndsk-ticket-table').show();
      $('.mwb-zendesk-hitbutton').show();
      $('#select_box_email').show(); 
      
    }); 
    $('#mwb-updated-comment').hide();
    $('.mwb-comment-update-checkbox').click(function(){
      alert('hey');
    $('#mwb-updated-comment').toggle();
    });
    var count_subject= 0;
    var count_comment=0;
  $("#mwb-create-submit-ticket").attr("disabled",true);
  $("#mwb-create-subject").focusout(function() {
    $categoryid = $("#mwb-create-subject").val();
    if ($categoryid == "") {
        $(".mwb-subject-error").html("*Select Subject");
        $(".mwb-subject-error").show();
        $(this).css('border', 'solid 3px red');
        $("#mwb-create-submit-ticket").attr("disabled",true);
        count_subject=0;
    } else {
        count_subject=1;
        $(".mwb-subject-error").hide();
        $(this).css('border', 'solid 3px green');
    }
    check_button_status();
  });
    $("#mwb-create-comment").focusout(function() {
      $categoryid = $("#mwb-create-comment").val();
      if ($categoryid == "") {
          $(".mwb-error-comment").html("*Select Comment");
          $(".mwb-error-comment").show();
          $(this).css('border', 'solid 3px red');
          $("#mwb-create-submit-ticket").attr("disabled",true);
          count_comment=0;
      } else {
          count_comment=1;
          $(".mwb-error-comment").hide();
          $(this).css('border', 'solid 3px green');
      }
      check_button_status();
});

function check_button_status(){
    if ( (  2 <= count_comment + count_subject) ) {
      $("#mwb-create-submit-ticket").attr("disabled",false);
    }
}
    $('#mwb-zendsk-email').on('change', function() {
         email =  this.value ;
        var zndskSecurity        = zndsk_ajax_ticket_object.zndskSecurity;
        var ajaxUrl              = zndsk_ajax_ticket_object.ajax_url;
        jQuery.post(
            ajaxUrl , { 'action' : 'mwb_zndsk_tickt_email', 'email' : email, 'nonce' : zndskSecurity}, function( response ) {
                $('.mwb-zndsk-ticket-table').html(response);
            }
        );
      });
      $(document).ajaxComplete(function(){
        $('.mwb-zendesk-ticket-form').hide();
        $('.mwb-zendesk-hitbutton').click(function(){
            $('#select_box_email').hide(); 
            $('.mwb-error-messege').hide();  
            $('.mwb-zendesk-hitbutton').hide();
            $('.mwb-zndsk-ticket-table').hide();
            $('.mwb-zendesk-ticket-form').show();
        });
        $('.return-back').click(function(){
            $('.mwb-zendesk-ticket-form').hide();
            $('.mwb-zndsk-ticket-table').show();
            $('.mwb-zendesk-hitbutton').show();
            $('#select_box_email').show(); 
            
        });   
      });
});