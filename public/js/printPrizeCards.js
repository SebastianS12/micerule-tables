jQuery(document).ready(function($){
  assignPrizeCardsListeners();
});

// Function to check if an element has an ancestor with a given class
function hasAncestorWithClass(element, className) {
  while (element) {
      if (element.classList && element.classList.contains(className)) {
          return true;
      }
      element = element.parentElement;
  }
  return false;
}

// Function to handle click event on prize card
function toggleExpanded(event) {
  // Check if the clicked element is a prize card with the appropriate ancestor
  if (event.target.closest('.prize-card') && hasAncestorWithClass(event.target.closest('.prize-card'), 'prize-cards-sent')) {
      const clickedCard = event.target.closest('.prize-card');

      if (clickedCard.classList.contains('expanded')) {
          // If the clicked card is already expanded, remove the expanded class
          clickedCard.classList.remove('expanded');
      } else {
          // Remove .expanded class from all prize cards with the appropriate ancestor
          document.querySelectorAll('.prize-card').forEach(function(card) {
              if (hasAncestorWithClass(card, 'prize-cards-sent')) {
                  card.classList.remove('expanded');
              }
          });

          // Add .expanded class to the clicked prize card
          clickedCard.classList.add('expanded');
      }

      // Stop the click event from propagating to the document
      event.stopPropagation();
  }
}

function assignPrizeCardsListeners(){
  $(".print-button").on('click', function(){
    window.print();
    window.onafterprint = printPrizeCards();
  });

  $('.move-to-unprinted').on('click', function(){
    console.log("Move");
    moveToUnprinted($(this));
  });

  // Add event listener to handle clicks on prize cards using event delegation
  document.addEventListener('click', toggleExpanded);
  
  // Add click event listener to the document to remove .expanded class when clicked outside
  document.addEventListener('click', function(event) {
      if (!event.target.closest('.prize-card')) {
        console.log("Click2");
          document.querySelectorAll('.prize-card').forEach(function(card) {
              if (hasAncestorWithClass(card, 'prize-cards-sent')) {
                  card.classList.remove('expanded');
              }
          });
      }
  });
}

function updatePrizeCardsHtml(prizeCardsHtml){
  $(".prizeCards.content").replaceWith(prizeCardsHtml);
  assignPrizeCardsListeners();
}

function printPrizeCards(){
    var prizeCardsToPrint = [];
    $(".prize-cards-print").find(".prize-card").each(function(){
      let placementID = $(this).data("placementId");
      let prize = $(this).data("prize");
      prizeCardsToPrint.push({"placementID" : placementID, "prize" : prize});
    });

  jQuery.ajax({
    type: 'PUT',
    url: getRoute("printAll"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    contentType: 'application/json',
    data: JSON.stringify({
      prizeCardsData: prizeCardsToPrint,
    }),
    success: function (data) {
      console.log(data);
      updateAdminTabs().then(() => updatePrizeCardsModal());
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}

function moveToUnprinted(clickedCard){

  var prizeID = clickedCard.parents(".class-card").data("prize");
  var placementID = clickedCard.parents(".class-card").data("placementId");

  jQuery.ajax({
    type: 'PUT',
    url: getRoute("moveToUnprinted"),
    beforeSend: function ( xhr ) {
      xhr.setRequestHeader( 'X-WP-Nonce', miceruleApi.nonce );
    },
    contentType: 'application/json',
    data: JSON.stringify({
      placementID : placementID,
      prizeID : prizeID,
    }),
    success: function (data) {
      console.log(data);
      updateAdminTabs().then(() => updatePrizeCardsModal());
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}


