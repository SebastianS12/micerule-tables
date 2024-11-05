<?php

class ShowOptionsController{
    public static function getShowOptions(int $locationID, ShowOptionsService $showOptionsService, ShowOptionsRepository $showOptionsRepository): ShowOptions
    {
        return $showOptionsService->getShowOptions($showOptionsRepository, $locationID);
    }

    public static function saveShowOptions(int $locationID, bool $allowOnlineRegistrations, float $registrationFee, float $pmFirstPlace, float $pmSecondPlace, float $pmThirdPlace, bool $allowUnstandardised, bool $allowJunior, bool $allowAuction): void
    {
        $showOptionsService = new ShowOptionsService();
        $showOptionsService->saveShowOptions(new ShowOptionsRepository, $locationID, $allowOnlineRegistrations, $registrationFee, $pmFirstPlace, $pmSecondPlace, $pmThirdPlace, $allowUnstandardised, $allowJunior, $allowAuction);
    }


    public static function updateIndices(int $locationID){
        // $showClassesModel = new ShowClassesModel();
        // $index = 1;
        // foreach(EventProperties::SECTIONNAMES as $section){
        //     $sectionShowClasses = $showClassesModel->getShowSectionClassesData($locationID, strtolower($section));
        //     foreach($sectionShowClasses as $showClass){
        //         $showClassesModel->updateClassIndex($locationID, $showClass['class_name'], 'Ad', $index);
        //         $showClassesModel->updateClassIndex($locationID, $showClass['class_name'], 'U8', $index + 1);
        //         $index += 2;
        //     }
        //     $showClassesModel->updateChallengeIndex($locationID, strtolower($section), EventProperties::getChallengeName(strtolower($section)), "Ad", $index);
        //     $showClassesModel->updateChallengeIndex($locationID, strtolower($section), EventProperties::getChallengeName(strtolower($section)), "U8", $index + 1);
        //     $index += 2;
        // }

        // $showClassesModel->updateChallengeIndex($locationID, Section::GRAND_CHALLENGE->value, EventProperties::GRANDCHALLENGE, "Ad", $index);
        // $showClassesModel->updateChallengeIndex($locationID, Section::GRAND_CHALLENGE->value, EventProperties::GRANDCHALLENGE, "U8", $index + 1);
        // $index += 2;   

        // $optionalShowClasses = $showClassesModel->getShowSectionClassesData($locationID, "optional");
        // foreach($optionalShowClasses as $optionalShowClass){
        //     $showClassesModel->updateClassIndex($locationID, $optionalShowClass['class_name'], "AA", $index);
        //     $index++;
        // }
        $indicesService = new IndicesService();
        $indicesService->updateIndices($locationID);
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
}