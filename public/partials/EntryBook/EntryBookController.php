<?php

class EntryBookController{
    public static function editPlacement($entryID, $placement, $checkValue, $placementModel, $locationID){
        if($checkValue)
            $placementModel->addPlacement($entryID, $placement, $locationID);
        else
            $placementModel->removePlacement($entryID);
    }

    public static function addEntry($eventPostID, $userName, $className, $age){
        $nextPenNumber = NextPenNumber::getNextPennumber(EventProperties::getEventLocationID($eventPostID), $className, $age);
        $userClassRegistration = new UserClassRegistration($eventPostID, $userName, $className, $age);
        $userClassRegistration->addUserRegistration();
        $showEntry = ShowEntry::createWithPenNumber($eventPostID, $nextPenNumber);
        $showEntry->save($userClassRegistration->registrationID, $userClassRegistration->getHighestRegistrationOrder(), $className, true, false);
        NextPenNumber::saveNextPenNumber(EventProperties::getEventLocationID($eventPostID), $className, $age, $nextPenNumber + 1);
    }

    public static function editEntryAbsent($eventPostID, $penNumber, $isAbsent){
        $showEntry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
        $showEntry->editAbsent($isAbsent);
    }

    public static function deleteEntry($eventPostID, $penNumber){
        $showEntry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
        $showEntry->delete();
    }

    public static function moveEntry($eventPostID, $penNumber, $newClassName, $newAge){
        $showEntry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
        $userClassRegistration = new UserClassRegistration($eventPostID, $showEntry->userName, $showEntry->className, $showEntry->age);
        $userClassRegistration->deleteUserRegistration($showEntry->getRegistrationOrder());
        $movedClassRegistration = new UserClassRegistration($eventPostID, $showEntry->userName, $newClassName, $newAge);
        $movedClassRegistration->addUserRegistration();
        $movedShowEntry = ShowEntry::createWithPenNumber($eventPostID, $showEntry->penNumber);
        $movedShowEntry->save($movedClassRegistration->registrationID, $movedClassRegistration->getHighestRegistrationOrder(), $newClassName, false, true);
    }

    public static function editBIS($age, $checkValue, $challengeAwardModel){
        if($checkValue)
            $challengeAwardModel->addAwards($age);
        else
            $challengeAwardModel->removeAwards();
    }

    public static function editVarietyName($entryID, $varietyName){
        $showEntry = ShowEntry::createWithEntryID($entryID);
        if($varietyName == "")
            $varietyName = $showEntry->className;
        $showEntry->editVarietyName($varietyName);
    }
}