jQuery(document).ready(function($){
  initRegistrationTablesJS();
});

function initRegistrationTablesJS(){
  $("#userSelectRegistration").select2();
  $("#userSelectRegistration").on('change',function(){
    fancierName = $(this).val();
    updateEntryFields(fancierName);
  });
  $(".registerClassesButton").on('click',function(){
    registerClasses();
  });
}

function updateEntryFields(fancierName){
  jQuery.ajax({
    type: 'GET',
    url: getRoute("registrations"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    data: {
      fancierName: fancierName,
    },
    success: function (data) {
      console.log(data);
      $("#registrationTables").replaceWith(data);
      initRegistrationTablesJS();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}

//focus to select2 text field after open 
$(document).on('select2:open', () => {
  document.querySelector('.select2-search__field').focus();
});
