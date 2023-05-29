<?php

/*
File is included in micerule-registerClasses.php:
$event_id
$eventRegistrationsData
*/
$locationID = $_POST['locationID'];
$eventClasses = EventClasses::create($locationID);

$entryBookData = new EntryBookData();
$adPenNumber = 1;
$u8PenNumber = $adPenNumber + 20;
$agePenNumbers = array('Ad' => $adPenNumber, 'U8' => $u8PenNumber);
$classIndex = 1;
$juvenileClassData = new ClassData("juvenile");
foreach(EventProperties::SECTIONNAMES as $sectionName){
  $sectionName = strtolower($sectionName);
  $sectionData = new SectionData($sectionName);
  $entryBookData->addSectionData($sectionData);
  foreach($eventClasses->getSectionClasses($sectionName) as $className){
    $classData = new ClassData($className);
    $classData->setClassIndex("Ad", $eventClasses->getClassIndex($className, "Ad"));
    $classData->setClassIndex("U8", $eventClasses->getClassIndex($className, "U8"));
    $entryBookData->addClassData($classData, $sectionName);
    foreach($eventRegistrationData->getClassRegistrationData($className)->classRegistrations as $age => $classAgeRegistrations){
      foreach($classAgeRegistrations as $classRegistration){
        $newEntry = new Entry($agePenNumbers[$age], $classRegistration->userName, $age, $className, $className, $sectionName);
        $entryBookData->addEntry($newEntry, $className);
        if($classRegistration->juvenile)
          $juvenileClassData->addPenNumber($agePenNumbers[$age]);
        $agePenNumbers[$age]++;
      }
    }
    $classData->setNextPenNumber("Ad", $agePenNumbers["Ad"]);
    $classData->setNextPenNumber("U8", $agePenNumbers["U8"]);
    //round penNumber up to next 10 with at least 5 free Pennumbers
    $agePenNumbers["Ad"] = (floor($agePenNumbers["Ad"] / 20) + 2) * 20 + 1;
    $agePenNumbers["U8"] = $agePenNumbers["Ad"] + 20;
    $classIndex += 2;
  }
  $sectionData->setChallengeIndex("Ad", $classIndex);
  $sectionData->setChallengeIndex("U8", $classIndex + 1);
  $classIndex += 2;
}

$grandChallengeData = new GrandChallengeData();
$grandChallengeData->setChallengeIndex("Ad", $classIndex);
$grandChallengeData->setChallengeIndex("U8", $classIndex + 1);
$entryBookData->grandChallenge = $grandChallengeData;

//optional classes
$sectionName = "optional";
$optionalSettings = EventOptionalSettings::create($locationID);
$sectionData = new SectionData($sectionName);
$entryBookData->optionalSection = $sectionData;
$classIndex += 2; // grand challenge

$penNumber = (floor($agePenNumbers["Ad"] / 20) + 2) * 20 + 1;
foreach($eventClasses->optionalClasses as $className){
    if($className == 'juvenile'){
      $juvenileClassData->setClassIndex("AA", $eventClasses->getClassIndex($className, "AA"));
      $entryBookData->addOptionalClassData($juvenileClassData);
    }else{
      $classData = new ClassData($className);
      $classData->setClassIndex("AA", $eventClasses->getClassIndex($className, "AA"));
      $entryBookData->addOptionalClassData($classData);
      foreach($eventRegistrationData->getOptionalClassRegistrationData($className)->classRegistrations as $age => $classAgeRegistrations){
        foreach($classAgeRegistrations as $classRegistration){
          $newEntry = new Entry($penNumber, $classRegistration->userName, $age, $className, $className, $sectionName);
          $entryBookData->addEntry($newEntry, $className);
          $penNumber++;
        }
      }
    }
    $classData->setNextPenNumber("AA", $penNumber);
    //round penNumber up to next 10 with at least 5 free Pennumbers
    $penNumber = (floor($penNumber / 20) + 1) * 20;
    $classIndex++;
}
$sectionData->setChallengeIndex("AA", $classIndex);

update_post_meta($event_id, 'micerule_data_event_entry_book_test', json_encode($entryBookData, JSON_UNESCAPED_UNICODE));
