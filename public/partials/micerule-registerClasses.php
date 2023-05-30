<?php
  global $post;
  global $wpdb;

  $url     = wp_get_referer();
  $event_id = url_to_postid( $url );

  $classRegistrations = $_POST['classRegistrations'];
  $optionalClassRegistrations = $_POST['optionalClassRegistrations'];
  $userName = $_POST['userName'];

  $isJuniorMember = EventUser::isJuniorMember($userName);
  $eventRegistrationData = EventRegistrationData::create($event_id);

  $userRegistrationData = $eventRegistrationData->getUserRegistrationData($userName);
  foreach($classRegistrations as $classRegistrationData){
    $className = $classRegistrationData['className'];
    $registrationCount = intval($classRegistrationData['registrationCount']);
    $classIndex = intval($classRegistrationData['classIndex']);
    $age = $classRegistrationData['age'];
    $currentRegistrationCount = $userRegistrationData->getUserClassRegistrationCount($className, $age);

    if($currentRegistrationCount < $registrationCount){
      //add
      for($i = $currentRegistrationCount; $i < $registrationCount; $i++){
        $eventRegistrationData->addClassRegistration($userName, $className, $age, $isJuniorMember);
      }
    }
    if($currentRegistrationCount > $registrationCount){
      //remove
      for($i = $currentRegistrationCount; $i > $registrationCount; $i--){
        $eventRegistrationData->removeClassRegistration($userName, $className, $age, $isJuniorMember);
      }
    }
  }

  foreach($optionalClassRegistrations as $optionalClassRegistrationData){
    $className = $optionalClassRegistrationData['className'];
    $registrationCount = intval($optionalClassRegistrationData['registrationCount']);
    $classIndex = intval($adRegistrationData['classIndex']);
    $currentRegistrationCount = $userRegistrationData->getUserClassRegistrationCount($className, "AA");

    if($currentRegistrationCount < $registrationCount){
      //add
      for($i = $currentRegistrationCount; $i < $registrationCount; $i++){
        $eventRegistrationData->addOptionalClassRegistration($userName, $className, "AA");
      }
    }
    if($currentRegistrationCount > $registrationCount){
      //remove
      for($i = $currentRegistrationCount; $i > $registrationCount; $i--){
        $eventRegistrationData->removeOptionalClassRegistration($userName, $className, "AA");
      }
    }
  }

  $eventRegistrationData->updatePostMeta($event_id);
  echo($userRegistrationData->getUserRegistrationOverviewHtml(EventProperties::getEventLocationID($event_id)));
  include("micerule-registerClasses-entryBookData.php");

  wp_die();
