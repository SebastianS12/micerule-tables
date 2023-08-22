<?php

//TODO: Duplicate Code from ShowOptionsController -> create ShowClasses SuperController
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

    public static function registerEntries($eventPostID, $classRegistrations, $optionalClassRegistrations, $userName){
        $registrationTablesModel = new RegistrationTablesModel();
        $eventLocationID = EventProperties::getEventLocationID($eventPostID);
        $isJuniorMember = EventUser::isJuniorMember($userName);
        foreach($classRegistrations as $classRegistrationData){
            $className = $classRegistrationData['className'];
            $registrationCount = intval($classRegistrationData['registrationCount']);
            $age = $classRegistrationData['age'];
            $currentUserRegistrationCount = self::getUserClassRegistrationCount($eventPostID, $userName, $className, $age);

            for($i = $currentUserRegistrationCount; $i < $registrationCount; $i++){
                $registrationTablesModel->addUserRegistration($eventPostID, $userName, $eventLocationID, $className, $age, $isJuniorMember);
            }
            for($i = $currentUserRegistrationCount; $i > $registrationCount; $i--){
                $registrationTablesModel->deleteUserRegistration($eventPostID, $userName, $className, $age);
            }
        }
    }

    public static function getUserClassRegistrationCount($eventPostID, $userName, $className, $age){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getUserClassRegistrationCount($eventPostID, $userName, $className, $age);
    }

    public static function getClassRegistrationCount($eventPostID, $className, $age){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getClassRegistrationCount($eventPostID, $className, $age);
    }

    public static function getSectionRegistrationCount($eventPostID, $locationID, $sectionName, $age){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getSectionRegistrationCount($eventPostID,$locationID, $sectionName, $age);
    }

    public static function getGrandChallengeRegistrationCount($eventPostID, $locationID, $age){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getGrandChallengeRegistrationCount($eventPostID, $locationID, $age);
    }

    public static function getUserRegistrations($eventPostID, $userName){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getUserRegistrations($eventPostID, $userName);
    }
}