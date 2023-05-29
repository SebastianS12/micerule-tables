<?php
global $post;

$url     = wp_get_referer();
$event_id = url_to_postid( $url );

$section = $_POST['section'];
$className = $_POST['className'];
$age = $_POST['age'];
$userName = $_POST['userName'];

$juvenileMember = EventUser::isJuvenileMember($userName);
$entryBookData = EntryBookData::create($event_id);
$classData = $entryBookData->classes[$className];
$absent = false;
$moved = false;
$added = true;
$newEntry = new Entry($classData->getNextPenNumber($age), $userName, $age, $className, $className, $section, $moved, $absent, $added);
$entryBookData->addEntry($newEntry, $className);
$classData->setNextPenNumber($age, $classData->getNextPenNumber($age) + 1);

$eventRegistrationData = EventRegistrationData::create($event_id);
if($section != "optional"){
  $eventRegistrationData->addClassRegistration($userName, $className, $age, $juvenileMember);
}else{
  $eventRegistrationData->addOptionalClassRegistration($userName, $className, $age);
}

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));
$eventRegistrationData->updatePostMeta($event_id);

wp_die();
