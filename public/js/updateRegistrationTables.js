jQuery(document).ready(function($){
  $("#userSelectRegistration").select2();
  $("#userSelectRegistration").on('change',function(){
    userName = $(this).val();
    updateEntryFields(userName);
  });
});

function updateEntryFields(userName){
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'updateRegistrationTables',
      userName: userName,
    },
    success: function (data) {
      console.log(data);
      var registrationTablesHtml = JSON.parse(data);
      $("#registrationTables").replaceWith(registrationTablesHtml);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}

//focus to select2 text field after open 
$(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
  });
