<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$penNumber = $_POST['penNumber'];
$selectValue = $_POST['selectValue'];

$entryBookData = EntryBookData::create($event_id);
$entry = $entryBookData->entries[$penNumber];

if($selectValue != ""){
  $entry->varietyName = $selectValue;
}else{
  $entry->varietyName = $entry->className;
}

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));

wp_die();
