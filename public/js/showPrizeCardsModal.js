jQuery(document).ready(function($){
    $("#show-prize-cards-modal").on('click', function(){
        updatePrizeCardsModal();
        $("#prizeCards-modal").modal();
        // assignPrizeCardsListeners();
    });
});

function updatePrizeCardsModal(){
    $('#prizeCards-modal').html($("#prizeCards-content").html());
    assignPrizeCardsListeners();
}