<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$penNumber = $_POST['penNumber'];

$entryBookData = EntryBookData::create($event_id);
$entry = $entryBookData->entries[$penNumber];
$juvenileMember = EventUser::isJuvenileMember($entry->userName);

$entryBookData->removeEntry($entry);

//TODO: remove registrationData
$eventRegistrationData = EventRegistrationData::create($event_id);
if($entry->sectionName != "optional"){
  $eventRegistrationData->removeClassRegistration($entry->userName, $entry->className, $entry->age, $juvenileMember);
}else{
  $eventRegistrationData->removeOptionalClassRegistration($entry->userName, $entry->className, $entry->age);
}

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));
$eventRegistrationData->updatePostMeta($event_id);

wp_die();
