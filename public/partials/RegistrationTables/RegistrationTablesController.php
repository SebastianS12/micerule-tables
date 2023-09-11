<?php

//TODO: Duplicate Code from ShowOptionsController -> create ShowOptions Model
class RegistrationTablesController{
    public static function getShowSectionClassesData($locationID, $section){
        $showClassesModel = new ShowClassesModel();
        $showSectionClassesData = $showClassesModel->getShowSectionClassesData($locationID, $section);
        if($showSectionClassesData == null)
            $showSectionClassesData = array();

        return $showSectionClassesData;
    }

    public static function getShowOptionalClassesData($locationID){
        $showClassesModel = new ShowClassesModel();
        $showOptionalClassesData = $showClassesModel->getShowOptionalClassesData($locationID);
        if($showOptionalClassesData == null)
            $showOptionalClassesData = array();

        return $showOptionalClassesData;
    }

    public static function getAllowOnlineRegistrations($locationID){
        $showOptionsModel = new ShowOptionsModel();
        $showOptions = $showOptionsModel->getShowOptions($locationID);
        $allowOnlineRegistrations = (isset($showOptions)) ? $showOptions['allow_online_registrations'] : false;
        return $allowOnlineRegistrations;
    }

    public static function getChallengeIndex($locationID, $challengeName, $age){
        $showClassesModel = new ShowClassesModel();
        $challengeIndex = $showClassesModel->getChallengeIndex($locationID, $challengeName, $age);
        return $challengeIndex;
    }

    //TODO:optional classes
    public static function registerEntries($eventPostID, $classRegistrations, $optionalClassRegistrations, $userName){
        foreach($classRegistrations as $classRegistrationData){
            $className = $classRegistrationData['className'];
            $registrationCount = intval($classRegistrationData['registrationCount']);
            $age = $classRegistrationData['age'];
            $userClassRegistrationModel = new UserClassRegistration($eventPostID, $userName, $className, $age);
            $currentUserRegistrationCount = $userClassRegistrationModel->getUserClassRegistrationCount();

            for($i = $currentUserRegistrationCount; $i < $registrationCount; $i++){
                $userClassRegistrationModel->addUserRegistration();
            }
            for($i = $currentUserRegistrationCount; $i > $registrationCount; $i--){
                $userClassRegistrationModel->deleteUserRegistration($userClassRegistrationModel->getUserHighestClassRegistrationOrder());
            }
        }
    }

    public static function getClassRegistrationCount($eventPostID, $classID, $age){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getClassRegistrationCount($eventPostID, $classID, $age);
    }

    public static function getSectionRegistrationCount($eventPostID, $sectionName, $age){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getSectionRegistrationCount($eventPostID, $sectionName, $age);
    }

    public static function getGrandChallengeRegistrationCount($eventPostID, $age){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getGrandChallengeRegistrationCount($eventPostID, $age);
    }

    public static function getUserRegistrations($eventPostID, $userName){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getUserRegistrations($eventPostID, $userName);
    }

    public static function createEntriesFromRegistrations($eventPostID, $locationID){
        $showClassesModel = new ShowClassesModel();
        $registrationTablesModel = new RegistrationTablesModel();
        $agePenNumbers = array('Ad' => 1, 'U8' => 21);
        foreach(EventProperties::SECTIONNAMES as $sectionName){
            $sectionName = strtolower($sectionName);
            foreach($showClassesModel->getShowSectionClassNames($locationID, $sectionName) as $className){
                foreach($registrationTablesModel->getClassRegistrations($eventPostID, $className) as $classRegistration){
                    $entry = ShowEntry::createWithPenNumber($eventPostID, $agePenNumbers[$classRegistration['age']]);
                    $entry->save($classRegistration['class_registration_id'], $classRegistration['registration_order'], $className, false, false);
                    $agePenNumbers[$classRegistration['age']]++;
                }
                NextPenNumber::saveNextPennumber($locationID, $className, "Ad", $agePenNumbers["Ad"]);
                NextPenNumber::saveNextPennumber($locationID, $className, "U8", $agePenNumbers["U8"]);
                $agePenNumbers["Ad"] = (floor($agePenNumbers["Ad"] / 20) + 2) * 20 + 1;
                $agePenNumbers["U8"] = $agePenNumbers["Ad"] + 20;
            }
        }

        $penNumber = (floor($agePenNumbers["Ad"] / 20) + 2) * 20 + 1;
        foreach($showClassesModel->getShowSectionClassNames($locationID, "optional") as $className){
            foreach($registrationTablesModel->getClassRegistrations($eventPostID, $className) as $classRegistration){
                $entry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
                $entry->save($classRegistration['class_registration_id'], $classRegistration['registration_order'], $className, false, false);
                $penNumber++;
            }
            NextPenNumber::saveNextPennumber($locationID, $className, "AA", $penNumber);
            $penNumber = (floor($penNumber / 20) + 1) * 20;
        }
    }
}