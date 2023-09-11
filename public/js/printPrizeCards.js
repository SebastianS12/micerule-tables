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
  var prizeCardsData = [];

  $(".prize-cards-print").find(".prize-card").each(function(){
    var prize = $(this).find(".prize").text();
    var penNumber = $(this).find(".pen-no").text();

    prizeCardsData.push({prize : prize, penNumber : penNumber});
  });

  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'setPrinted',
      prizeCardsData : JSON.stringify(prizeCardsData),
      print : true,
    },
    success: function (data) {
      updateAdminTabs();
      console.log(data);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}

function moveToUnprinted(clickedCard){
  var prizeCardsData = [];

  var prize = clickedCard.parents(".class-card").find(".prize").text();
  var penNumber = clickedCard.parents(".class-card").find(".pen-no").text();

  prizeCardsData.push({prize : prize, penNumber : penNumber});
  jQuery.ajax({
    type: 'POST',
    url: my_ajax_obj.ajax_url,
    data: {
      _ajax_nonce: my_ajax_obj.nonce,
      action: 'setPrinted',
      prizeCardsData : JSON.stringify(prizeCardsData),
      print : false,
    },
    success: function (data) {
      updateAdminTabs();
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}
