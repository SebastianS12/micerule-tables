<?php

global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$newSection = $_POST['newSection'];
$newClassName = $_POST['newClassName'];
$newAge = $_POST['newAge'];
$penNumber = $_POST['penNumber'];

$eventRegistrationData = EventRegistrationData::create($event_id);
$entryBookData = EntryBookData::create($event_id);
$entry = $entryBookData->entries[$penNumber];
$juniorMember = EventUser::isJuniorMember($entry->userName);

if($entry->sectionName != $newSection || $entry->className != $newClassName || $entry->age != $newAge){
  $newEntry = new Entry($entry->penNumber, $entry->userName, $newAge, $newClassName, $newClassName, $newSection, true, $entry->absent);
  $entryBookData->removeEntry($entry);
  $entryBookData->addEntry($newEntry, $newClassName);

  if($entry->sectionName != "optional"){
    $eventRegistrationData->removeClassRegistration($entry->userName, $entry->className, $entry->age, $juniorMember);
  }else{
    $eventRegistrationData->removeOptionalClassRegistration($entry->userName, $entry->className, $entry->age);
  }

  if($newSection != "optional"){
    $eventRegistrationData->addClassRegistration($entry->userName, $newClassName, $newAge, $juniorMember);
  }else{
    $eventRegistrationData->addOptionalClassRegistration($entry->userName, $newClassName, $newAge);
  }
}

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));
$eventRegistrationData->updatePostMeta($event_id);

wp_die();
