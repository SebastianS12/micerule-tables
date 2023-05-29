<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$penNumbers = $_POST['penNumbers'];
$checkValue = ($_POST['checkValue'] == "true");

$entryBookData = EntryBookData::create($event_id);

foreach($penNumbers as $penNumber){
  $entry = $entryBookData->entries[$penNumber];
  $entry->absent = $checkValue;
}

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));
echo(AdminTabs::getAdminTabsHtml($event_id));

wp_die();
