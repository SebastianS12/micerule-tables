<?php

class PlacementsService{
    public static function entryInPlacements($entry, $placements){
        if($placements){
            foreach($placements as $placementModel){
                if($placementModel->entryID == $entry->ID){
                    return true;
                }
            }
        }
    
        return false;
    }

    public static function entryHasPlacement($placements, int $placementNumber, $entry){
        return isset($placements[$placementNumber]) && $placements[$placementNumber]->entryID == $entry->ID;
    }

    public static function placementExists($placements, $placementNumber){
        return isset($placements[$placementNumber]);
    }

    public static function placementsInSameClass($prize, $lowerPlacement, $higherPlacement, $entries){
        $placementsInSameClass = false;
        if($prize == "Section Challenge"){
            $placementsInSameClass = self::inSameClass($entries[$lowerPlacement->entryID], $entries[$higherPlacement->entryID]);
        }
        if($prize == "Grand Challenge"){
            $placementsInSameClass = self::inSameSection($entries[$lowerPlacement->entryID], $entries[$higherPlacement->entryID]);
        }

        return $placementsInSameClass;
    }

    private static function inSameClass(EntryModel $lowerPlacementEntry, EntryModel $higherPlacementEntry): bool{
        return $lowerPlacementEntry->getClassName() == $higherPlacementEntry->getClassName();
    }

    private static function inSameSection(EntryModel $lowerPlacementEntry, EntryModel $higherPlacementEntry): bool{
        return $lowerPlacementEntry->getSectionName() == $higherPlacementEntry->getSectionName();
    }
}