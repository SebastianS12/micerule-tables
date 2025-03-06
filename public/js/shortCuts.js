jQuery(document).ready(function($){
    $("#show-prize-cards-btn").on('click', function(){
        showTab($(".prizeCards.tab"));
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#prizeCards-content").offset().top
        }, 2000);
    });

    $("#show-selfs-section-btn").on('click', function(){
        showTab($(".entryBook.tab"));
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#eb-section-selfs").offset().top
        }, 2000);
    });

    $("#show-tans-section-btn").on('click', function(){
        showTab($(".entryBook.tab"));
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#eb-section-tans").offset().top
        }, 2000);
    });

    $("#show-marked-section-btn").on('click', function(){
        showTab($(".entryBook.tab"));
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#eb-section-marked").offset().top
        }, 2000);
    });

    $("#show-satins-section-btn").on('click', function(){
        showTab($(".entryBook.tab"));
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#eb-section-satins").offset().top
        }, 2000);
    });

    $("#show-aovs-section-btn").on('click', function(){
        showTab($(".entryBook.tab"));
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#eb-section-aovs").offset().top
        }, 2000);
    });
});