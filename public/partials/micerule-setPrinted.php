<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$prizeCards = json_decode(html_entity_decode(stripslashes($_POST['prizeCardsData'])));
$print = ($_POST['print'] == "true");

$entryBookData = EntryBookData::create($event_id);

foreach($prizeCards as $prizeCardData){
  if($prizeCardData->prize == "Grand Challenge"){
    $entryBookData->grandChallenge->getPlacementData($prizeCardData->age)->editPlacementPrinted($prizeCardData->placement, $print);
  }

  if($prizeCardData->prize == "Section Challenge"){
    $entryBookData->sections[$prizeCardData->sectionName]->getPlacementData($prizeCardData->age)->editPlacementPrinted($prizeCardData->placement, $print);
  }

  if($prizeCardData->prize == "Class"){
    $entryBookData->classes[$prizeCardData->className]->getPlacementData($prizeCardData->age)->editPlacementPrinted($prizeCardData->placement, $print);
  }
}

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));
echo(AdminTabs::getAdminTabsHtml($event_id));
wp_die();
