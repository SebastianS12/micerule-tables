jQuery(document).ready(function($){
  assignPrizeCardsListeners();
});

function assignPrizeCardsListeners(){
  $(".print-button").on('click', function(){
    window.print();
    window.onafterprint = printPrizeCards();
  });

  $('.move-to-unprinted').on('click', function(){
    moveToUnprinted($(this));
  });
}

function updatePrizeCardsHtml(prizeCardsHtml){
  $(".prizeCards.content").replaceWith(prizeCardsHtml);
  assignPrizeCardsListeners();
}

function printPrizeCards(){
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'printAll',
    },
    success: function (data) {
      console.log(data);
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}

function moveToUnprinted(clickedCard){

  var prizeID = clickedCard.parents(".class-card").data("prize-id");
  var placementID = clickedCard.parents(".class-card").data("placementid");

  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'moveToUnprinted',
      placementID : placementID,
      prizeID : prizeID,
    },
    success: function (data) {
      console.log(data);
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}
