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
    var sectionName = $(this).find(".prize-card-section-name").text();
    var placement = $(this).find(".prize-card-placement").not(".card-info").text();
    var className = $(this).find(".prize-card-class-name").text();
    var age = $(this).find(".prize-card-age").text();

    prizeCardsData.push({prize : prize, sectionName : sectionName, placement : placement, className : className, age : age});
  });
  console.log(prizeCardsData);

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
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
  });
}

function moveToUnprinted(clickedCard){
  var prizeCardsData = [];

  var prize = clickedCard.parents(".class-card").find(".prize").text();
  var sectionName = clickedCard.parents(".class-card").find(".prize-card-section-name").text();
  var placement = clickedCard.parents(".class-card").find(".prize-card-placement").text();
  var className = clickedCard.parents(".class-card").find(".prize-card-class-name").text();
  var age = clickedCard.parents(".class-card").find(".prize-card-age").text();

  prizeCardsData.push({prize : prize, sectionName : sectionName, placement : placement, className : className, age : age});
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
      alert(errorThrown);
    }
  });
}
