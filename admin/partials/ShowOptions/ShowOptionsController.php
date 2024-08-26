<?php

class ShowOptionsController{
    //TODO: Split Class into two for options and classes
    public static function getShowOptions($locationID){
        $showOptionsModel = new ShowOptionsModel();
        $showOptions = $showOptionsModel->getShowOptions($locationID);
        if($showOptions == null)
            $showOptions = self::getDefaultShowOptions($locationID);

        return $showOptions;
    }

    private static function getDefaultShowOptions($locationID){
        return array(
            "locationID" => $locationID,
            "allowOnlineRegistrations" => false,
            "registrationFee" => 0,
            "pm_first_place" => 0,
            "pm_second_place" => 0,
            "pm_third_place" => 0,
            "allowUnstandardised" => false,
            "allowJunior" => false,
            "allowAuction" => false);
    }

    public static function saveShowOptions($locationID, $allowOnlineRegistrations, $registrationFee, $pmFirstPlace, $pmSecondPlace, $pmThirdPlace, $allowUnstandardised, $allowJunior, $allowAuction){
        $showOptionsModel = new ShowOptionsModel();
        $showOptions = array(
            "location_id" => $locationID,
            "allow_online_registrations" => $allowOnlineRegistrations,
            "registration_fee" => $registrationFee,
            "pm_first_place" => $pmFirstPlace,
            "pm_second_place" => $pmSecondPlace,
            "pm_third_place" => $pmThirdPlace,
            "allow_unstandardised" => $allowUnstandardised,
            "allow_junior" => $allowJunior,
            "allow_auction" => $allowAuction);
        $showOptionsModel->saveShowOptions($showOptions);
    }

    public static function addShowClass($locationID, $className, $section){
        $showClassesModel = new ShowClassesModel();
        $sectionPosition = $showClassesModel->getNextSectionPosition($locationID, $section);
        $showClassesModel->addShowClass($locationID, $className, $section, $sectionPosition);
        self::updateIndices($locationID);
    }

    public static function updateIndices($locationID){
        //TODO: split in functions, pass index by reference
        $showClassesModel = new ShowClassesModel();
        $index = 1;
        foreach(EventProperties::SECTIONNAMES as $section){
            $sectionShowClasses = $showClassesModel->getShowSectionClassesData($locationID, strtolower($section));
            foreach($sectionShowClasses as $showClass){
                $showClassesModel->updateClassIndex($locationID, $showClass['class_name'], 'Ad', $index);
                $showClassesModel->updateClassIndex($locationID, $showClass['class_name'], 'U8', $index + 1);
                $index += 2;
            }
            $showClassesModel->updateChallengeIndex($locationID, strtolower($section), EventProperties::getChallengeName(strtolower($section)), "Ad", $index);
            $showClassesModel->updateChallengeIndex($locationID, strtolower($section), EventProperties::getChallengeName(strtolower($section)), "U8", $index + 1);
            $index += 2;
        }

        $showClassesModel->updateChallengeIndex($locationID, "", EventProperties::GRANDCHALLENGE, "Ad", $index);
        $showClassesModel->updateChallengeIndex($locationID, "", EventProperties::GRANDCHALLENGE, "U8", $index + 1);
        $index += 2;   

        $optionalShowClasses = $showClassesModel->getShowSectionClassesData($locationID, "optional");
        foreach($optionalShowClasses as $optionalShowClass){
            $showClassesModel->updateClassIndex($locationID, $optionalShowClass['class_name'], "AA", $index);
            $index++;
        }
    }

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

    public static function getClassIndex($locationID, $className, $age){
        $showClassesModel = new ShowClassesModel();
        $classIndex = $showClassesModel->getClassIndex($locationID, $className, $age);
        return $classIndex;
    }

    public static function getChallengeIndex($locationID, $challengeName, $age){
        $showClassesModel = new ShowClassesModel();
        $challengeIndex = $showClassesModel->getChallengeIndex($locationID, $challengeName, $age);
        return $challengeIndex;
    }

    public static function swapSectionClasses($locationID, $firstClassName, $secondClassName){
        $showClassesModel = new ShowClassesModel();
        $showClassesModel->swapSectionPosition($locationID, $firstClassName, $secondClassName);
        $showClassesModel->swapClassIndices($locationID, $firstClassName, $secondClassName, "Ad");
        $showClassesModel->swapClassIndices($locationID, $firstClassName, $secondClassName, "U8");
    }

    public static function swapOptionalClasses($locationID, $firstClassName, $secondClassName){
        $showClassesModel = new ShowClassesModel();
        $showClassesModel->swapSectionPosition($locationID, $firstClassName, $secondClassName);
        $showClassesModel->swapClassIndices($locationID, $firstClassName, $secondClassName, "AA");
    }

    public static function deleteClass($locationID, $className){
        $showClassesModel = new ShowClassesModel();
        $showClassesModel->deleteClass($locationID, $className);
        self::updateIndices($locationID);
    }

    public static function userHasPermissions($locationID){
        $locationSecretaryNames = LocationSecretaries::getLocationSecretaryNames($locationID);
        return ((is_user_logged_in() && (in_array(wp_get_current_user()->display_name, $locationSecretaryNames) || current_user_can('administrator'))));
    }
}