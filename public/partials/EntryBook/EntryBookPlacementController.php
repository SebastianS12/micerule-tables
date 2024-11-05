<?php

class EntryBookPlacementController{

    //TODO: Thin out controller, move functions to services, controller doesn't interact with repositories, only through services
    public function prepareEntryPlacementData($entry, $lowerPlacements, $currentPlacements, $higherPlacements, $indexID, $prize, $entries){
        $entryPlacementData = array();
        $entryPlacementData['sectionBestDisabled'] = (PlacementsService::entryInPlacements($entry, $higherPlacements)) ? "disabled" : "";
        $entryPlacementData['firstPlaceChecked'] = (PlacementsService::entryHasPlacement($currentPlacements, 1, $entry)) ? "checked" : "";
        $entryPlacementData['secondPlaceChecked'] = (PlacementsService::entryHasPlacement($currentPlacements, 2, $entry)) ? "checked" : "";
        $entryPlacementData['thirdPlaceChecked'] = (PlacementsService::entryHasPlacement($currentPlacements, 3, $entry)) ? "checked" : "";
        $showPlacementInputs = $this->getShowPlacementInputs($entry, $lowerPlacements, $currentPlacements, $entries, $prize);
        $entryPlacementData['showFirstPlaceCheck'] = ($showPlacementInputs['firstPlace']) ? "flex" : "none";
        $entryPlacementData['showSecondPlaceCheck'] = ($showPlacementInputs['secondPlace']) ? "flex" : "none";
        $entryPlacementData['showThirdPlaceCheck'] = ($showPlacementInputs['thirdPlace']) ? "flex" : "none";
        $entryPlacementData['age'] = $entry->age;
        $entryPlacementData['entry_id'] = $entry->ID;
        $entryPlacementData['index_id'] = $indexID;
        $entryPlacementData['prize'] = $prize;

        return $entryPlacementData;
    }

    private function getShowPlacementInputs($entry, $lowerPlacements, $currentPlacements, $entries, $prize){
        $showPlacementInputs = array();
        $isFirstPlaceLowerPlacement = PlacementsService::entryHasPlacement($lowerPlacements, 1, $entry);
        $showPlacementInputs['firstPlace'] = $this->shouldShowPlacementInput(1, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement, false);
        $firstPlaceIsInSameClass = $this->placementsInSameClass($currentPlacements, 1, $lowerPlacements, 2, $entries, $prize) && PlacementsService::entryHasPlacement($lowerPlacements, 2, $entry);
        $showPlacementInputs['secondPlace'] = $this->shouldShowPlacementInput(2, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement || $showPlacementInputs['firstPlace'], $firstPlaceIsInSameClass);
        $secondPlaceIsInSameClass = $this->placementsInSameClass($currentPlacements, 2, $lowerPlacements, 3, $entries, $prize) && (PlacementsService::entryHasPlacement($lowerPlacements, 2, $entry) || ($this->placementsInSameClass($currentPlacements, 1, $currentPlacements, 2, $entries, $prize) && PlacementsService::entryHasPlacement($lowerPlacements, 3, $entry)));
        $showPlacementInputs['thirdPlace'] = $this->shouldShowPlacementInput(3, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement || $showPlacementInputs['secondPlace'], $firstPlaceIsInSameClass || $secondPlaceIsInSameClass);

        return $showPlacementInputs;
    }

    private function placementsInSameClass($higherPlacements, $higherPlacementNumber, $currentPlacements, $currentPlacementNumber, $entries, $prize){
        return PlacementsService::placementExists($higherPlacements, $higherPlacementNumber) && PlacementsService::placementExists($currentPlacements, $currentPlacementNumber) && PlacementsService::placementsInSameClass($prize, $currentPlacements[$currentPlacementNumber], $higherPlacements[$higherPlacementNumber], $entries);
    }

    private function shouldShowPlacementInput($placementNumber, $entry, $lowerPlacements, $currentPlacements, $showPreviousPlacementInput, $isInSameClass) {
        $showPlacementInput = false;

        if(!isset($lowerPlacements)){
            $showPlacementInput = true;
        }
    
        if ($showPreviousPlacementInput) {
            $showPlacementInput = true;
        }

        if($isInSameClass){
            $showPlacementInput = true;
        }

        if($showPlacementInput){
            $showPlacementInput = PlacementsService::entryHasPlacement($currentPlacements, $placementNumber, $entry) || !PlacementsService::placementExists($currentPlacements, $placementNumber);
            $showPlacementInput = $showPlacementInput && $this->noOtherPlacementsForEntry($currentPlacements, $placementNumber, $entry);
        }
    
        return $showPlacementInput;
    }

    private function noOtherPlacementsForEntry($placements, $placementNumber, $entry){
        $noOtherPlacements = true;
        foreach($placements as $placement => $placementModel){
            if($placement != $placementNumber)
                $noOtherPlacements = $noOtherPlacements && $placementModel->entryID != $entry->ID;
        }

        return $noOtherPlacements;
    }

    public function editPlacement(int $eventPostID, PlacementsRepository $placementsRepository, int $placement, int $indexID, int $entryID, Prize $prize){
        $placements = $placementsRepository->getAllPlacements($eventPostID, $indexID);
        if(isset($placements[$placement])){
            $placementsRepository->removePlacement($placements[$placement]->id);
        }else{
            $placementsRepository->addPlacement($placement, $indexID, $entryID, $prize);
        }
    }
}