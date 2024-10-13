<?php

class PlacementsService{
    public static function entryInPlacements(EntryModel $entry, Prize $prize){
        foreach($entry->placements as $placementModel){
            if($placementModel->prize == $prize) return true;
        }

        return false;
    }

    public static function entryHasPlacement(EntryModel $entry, Prize $prize, int $placementNumber): bool
    {
        foreach($entry->placements() as $placementModel){
            if($placementModel->prize == $prize && $placementModel->placement == $placementNumber) return true;
        }    

        return false;
    }

    public static function placementExists(Collection $placements, int $placementNumber): bool
    {
        $placements = $placements->groupBy("placement");
        return isset($placements[$placementNumber]);
    }

    public static function placementsInSameClass(Prize $prize, PlacementModel $lowerPlacement, PlacementModel $higherPlacement){
        $placementsInSameClass = false;
        if($prize == "Section Challenge"){
            $placementsInSameClass = self::inSameClass($lowerPlacement->entry()->showClass(), $higherPlacement->entry()->showClass());
        }
        if($prize == "Grand Challenge"){
            $placementsInSameClass = self::inSameSection($lowerPlacement->entry()->showClass(), $higherPlacement->entry()->showClass());
        }

        return $placementsInSameClass;
    }

    private static function inSameClass(EntryClassModel $lowerPlacementEntryClassModel, EntryClassModel $higherPlacementEntryClassModel): bool{
        return $lowerPlacementEntryClassModel->className == $higherPlacementEntryClassModel->className;
    }

    private static function inSameSection(EntryClassModel $lowerPlacementEntryClassModel, EntryClassModel $higherPlacementEntryClassModel): bool{

        if(!isset($lowerPlacementEntryClassModel) || !isset($higherPlacementEntryClassModel)){
            throw new InvalidArgumentException("Show Class Model not set!");
        }

        return $lowerPlacementEntryClassModel->sectionName == $higherPlacementEntryClassModel->sectionName;
    }
}