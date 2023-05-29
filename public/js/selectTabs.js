jQuery(document).ready(function($){
  selectTab();
  assignTabListeners();
});

function assignTabListeners(){
  $(".tab").on('click', function(){
    showTab($(this));
  });
}

function selectTab(){
  if(getTabFromUrl() != null){
    showTab($("." + getTabFromUrl() + ".tab"));
  }
}

function showTab(tab){
  $(".tab").each(function(){
    $(this).removeClass('active');
  });
  tab.addClass('active');

  var tabClass = tab.attr('class').split(' ')[0];
  $(".content").each(function(){
    $(this).hide();

    if($(this).hasClass(tabClass)){
      $(this).show();
    }
  });

  addTabToUrl(tabClass);
}

function addTabToUrl(tabClass){
  const url = new URL(window.location.href);
  url.searchParams.set('tab', tabClass);
  window.history.replaceState(null, null, url);
}

function getTabFromUrl(){
  const urlParameterString = window.location.search;
  const urlParams = new URLSearchParams(urlParameterString);
  const tabClass = urlParams.get('tab');

  return tabClass;
}
