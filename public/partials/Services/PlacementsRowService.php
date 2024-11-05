<?php

class PlacementsRowService{
    public function prepareEntryPlacementData(EntryModel $entry, Prize $prize, Collection $lowerPlacements, Collection $currentPlacements, Prize $higherPrize, int $indexID, string $age, Prize $lowerPrice = null){
        $entryPlacementData = array();
        // echo(var_dump($entry));
        $entryPlacementData['sectionBestDisabled'] = (PlacementsService::entryInPlacements($entry, $higherPrize)) ? "disabled" : "";
        $entryPlacementData['firstPlaceChecked'] = (PlacementsService::entryHasPlacement($entry, $prize, 1)) ? "checked" : "";
        $entryPlacementData['secondPlaceChecked'] = (PlacementsService::entryHasPlacement($entry, $prize, 2)) ? "checked" : "";
        $entryPlacementData['thirdPlaceChecked'] = (PlacementsService::entryHasPlacement($entry, $prize, 3)) ? "checked" : "";
        $showPlacementInputs = $this->getShowPlacementInputs($entry, $prize, $lowerPlacements, $currentPlacements, $lowerPrice);
        $entryPlacementData['showFirstPlaceCheck'] = ($showPlacementInputs['firstPlace']) ? "flex" : "none";
        $entryPlacementData['showSecondPlaceCheck'] = ($showPlacementInputs['secondPlace']) ? "flex" : "none";
        $entryPlacementData['showThirdPlaceCheck'] = ($showPlacementInputs['thirdPlace']) ? "flex" : "none";
        $entryPlacementData['age'] = $age;
        $entryPlacementData['entry_id'] = $entry->id;
        $entryPlacementData['index_id'] = $indexID;
        $entryPlacementData['prize'] = $prize->value;

        return $entryPlacementData;
    }

    private function getShowPlacementInputs(EntryModel $entry, Prize $prize, Collection $lowerPlacements, Collection $currentPlacements, Prize|null $lowerPrice){
        $showPlacementInputs = array();
        $isFirstPlaceLowerPlacement = (!isset($lowerPrice)) || PlacementsService::entryHasPlacement($entry, $lowerPrice, 1);
        $showPlacementInputs['firstPlace'] = $this->shouldShowPlacementInput($prize, 1, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement, false);
        $firstPlaceIsInSameClass = $this->placementsInSameClass($currentPlacements, 1, $lowerPlacements, 2, $prize) && ((!isset($lowerPrice)) || PlacementsService::entryHasPlacement($entry, $lowerPrice, 2));
        $showPlacementInputs['secondPlace'] = $this->shouldShowPlacementInput($prize, 2, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement || $showPlacementInputs['firstPlace'], $firstPlaceIsInSameClass);
        $secondPlaceIsInSameClass = $this->placementsInSameClass($currentPlacements, 2, $lowerPlacements, 3, $prize) && (((!isset($lowerPrice)) || PlacementsService::entryHasPlacement($entry, $lowerPrice, 2)) || ($this->placementsInSameClass($currentPlacements, 1, $currentPlacements, 2, $prize) && ((!isset($lowerPrice)) || PlacementsService::entryHasPlacement($entry, $lowerPrice, 3))));
        $showPlacementInputs['thirdPlace'] = $this->shouldShowPlacementInput($prize, 3, $entry, $lowerPlacements, $currentPlacements, $isFirstPlaceLowerPlacement || $showPlacementInputs['secondPlace'], $firstPlaceIsInSameClass || $secondPlaceIsInSameClass);

        return $showPlacementInputs;
    }

    private function placementsInSameClass(Collection $higherPlacements, int $higherPlacementNumber, Collection $currentPlacements, int $currentPlacementNumber, Prize $prize){
        return PlacementsService::placementExists($higherPlacements, $higherPlacementNumber) && PlacementsService::placementExists($currentPlacements, $currentPlacementNumber) && PlacementsService::placementsInSameClass($prize, $currentPlacements, $currentPlacementNumber, $higherPlacements, $higherPlacementNumber);
    }

    private function shouldShowPlacementInput(Prize $prize, int $placementNumber, EntryModel $entry, Collection $lowerPlacements, Collection $currentPlacements, bool $showPreviousPlacementInput, bool $isInSameClass) {
        $showPlacementInput = $prize == Prize::STANDARD || $showPreviousPlacementInput || $isInSameClass || PlacementsService::entryHasPlacement($entry, $prize, $placementNumber);

        // if(count($lowerPlacements) == 0){
        //     $showPlacementInput = true;
        // }
    
        // if ($showPreviousPlacementInput) {
        //     $showPlacementInput = true;
        // }

        // if($isInSameClass){
        //     $showPlacementInput = true;
        // }

        if($showPlacementInput){
            $showPlacementInput = PlacementsService::entryHasPlacement($entry, $prize, $placementNumber) || !PlacementsService::placementExists($currentPlacements, $placementNumber);
            $showPlacementInput = $showPlacementInput && $this->noOtherPlacementsForEntry($currentPlacements, $placementNumber, $entry);
        }
    
        return $showPlacementInput;
    }

    private function noOtherPlacementsForEntry(Collection $placements, int $placementNumber, EntryModel $entry){
        $noOtherPlacements = true;
        foreach($placements as $placementModel){
            if($placementModel->placement != $placementNumber)
                $noOtherPlacements = $noOtherPlacements && $placementModel->entry_id != $entry->id;
        }

        return $noOtherPlacements;
    }

    public function editPlacement(int $eventPostID, Prize $prize, int $indexID, int $placementNumber, int $entryID): void
    {
        $placementsRepository = new PlacementsRepository($eventPostID, PlacementDAOFactory::getPlacementDAO($prize->value));
        $placements = $placementsRepository->getIndexPlacements($indexID);
        if(PlacementsService::placementExists($placements, $placementNumber)){
            $placements = $placements->groupByUniqueKey("placement");
            $placementsRepository->removePlacement($placements[$placementNumber]->id);
        }else{
            $placementsRepository->addPlacement($placementNumber, $indexID, $entryID, $prize);
        }
    }
}