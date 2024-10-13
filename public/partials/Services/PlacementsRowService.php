<?php

class PlacementsRowService{
    public function prepareEntryPlacementData(EntryModel $entry, Prize $prize, Collection $lowerPlacements, Collection $currentPlacements, Collection $higherPlacements, int $indexID, string $age){
        $entryPlacementData = array();
        $entryPlacementData['sectionBestDisabled'] = "";//(PlacementsService::entryInPlacements($entry, $prize)) ? "disabled" : "";
        $entryPlacementData['firstPlaceChecked'] = (PlacementsService::entryHasPlacement($entry, $prize, 1)) ? "checked" : "";
        $entryPlacementData['secondPlaceChecked'] = (PlacementsService::entryHasPlacement($entry, $prize, 2)) ? "checked" : "";
        $entryPlacementData['thirdPlaceChecked'] = (PlacementsService::entryHasPlacement($entry, $prize, 3)) ? "checked" : "";
        $showPlacementInputs = $this->getShowPlacementInputs($entry, $prize, $lowerPlacements, $currentPlacements);
        $entryPlacementData['showFirstPlaceCheck'] = ($showPlacementInputs['firstPlace']) ? "flex" : "none";
        $entryPlacementData['showSecondPlaceCheck'] = ($showPlacementInputs['secondPlace']) ? "flex" : "none";
        $entryPlacementData['showThirdPlaceCheck'] = ($showPlacementInputs['thirdPlace']) ? "flex" : "none";
        $entryPlacementData['age'] = $age;
        $entryPlacementData['entry_id'] = $entry->id;
        $entryPlacementData['index_id'] = $indexID;
        $entryPlacementData['prize'] = $prize->value;

        return $entryPlacementData;
    }

    private function getShowPlacementInputs(EntryModel $entry, Prize $prize, Collection $lowerPlacements, Collection $currentPlacements){
        $showPlacementInputs = array();
        $isFirstPlaceLowerPlacement = PlacementsService::entryHasPlacement($entry, $prize, 1);
        $showPlacementInputs['firstPlace'] = $this->shouldShowPlacementInput($prize, 1, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement, false);
        $firstPlaceIsInSameClass = $this->placementsInSameClass($currentPlacements, 1, $lowerPlacements, 2, $prize) && PlacementsService::entryHasPlacement($entry, $prize, 2);
        $showPlacementInputs['secondPlace'] = $this->shouldShowPlacementInput($prize, 2, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement || $showPlacementInputs['firstPlace'], $firstPlaceIsInSameClass);
        $secondPlaceIsInSameClass = $this->placementsInSameClass($currentPlacements, 2, $lowerPlacements, 3, $prize) && (PlacementsService::entryHasPlacement($entry, $prize, 2) || ($this->placementsInSameClass($currentPlacements, 1, $currentPlacements, 2, $prize) && PlacementsService::entryHasPlacement($entry, $prize, 3)));
        $showPlacementInputs['thirdPlace'] = $this->shouldShowPlacementInput($prize, 3, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement || $showPlacementInputs['secondPlace'], $firstPlaceIsInSameClass || $secondPlaceIsInSameClass);

        return $showPlacementInputs;
    }

    private function placementsInSameClass(Collection $higherPlacements, int $higherPlacementNumber, Collection $currentPlacements, int $currentPlacementNumber, Prize $prize){
        return PlacementsService::placementExists($higherPlacements, $higherPlacementNumber) && PlacementsService::placementExists($currentPlacements, $currentPlacementNumber) && PlacementsService::placementsInSameClass($prize, $currentPlacements[$currentPlacementNumber], $higherPlacements[$higherPlacementNumber]);
    }

    private function shouldShowPlacementInput(Prize $prize, int $placementNumber, EntryModel $entry, Collection $lowerPlacements, Collection $currentPlacements, bool $showPreviousPlacementInput, bool $isInSameClass) {
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
            $showPlacementInput = PlacementsService::entryHasPlacement($entry, $prize, $placementNumber) || !PlacementsService::placementExists($currentPlacements, $placementNumber);
            $showPlacementInput = $showPlacementInput && $this->noOtherPlacementsForEntry($currentPlacements, $placementNumber, $entry);
        }
    
        return $showPlacementInput;
    }

    private function noOtherPlacementsForEntry(Collection $placements, int $placementNumber, EntryModel $entry){
        $noOtherPlacements = true;
        foreach($placements as $placement => $placementModel){
            if($placement != $placementNumber)
                $noOtherPlacements = $noOtherPlacements && $placementModel->entryID != $entry->ID;
        }

        return $noOtherPlacements;
    }
}