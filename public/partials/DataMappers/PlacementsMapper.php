<?php

class PlacementsMapper{
    public static function mapClassPlacementsToEntries(Collection &$entryCollection, Collection &$placementCollection): void
    {
        ModelHydrator::mapExistingCollections(
            $entryCollection,
            $placementCollection, 
            ClassPlacementModel::class,
            "id", 
            "entry_id"
        );
    }

    public static function mapChallengePlacementsToEntries(Collection &$entryCollection, Collection &$placementCollection): void
    {
        ModelHydrator::mapExistingCollections(
            $entryCollection,
            $placementCollection, 
            ChallengePlacementModel::class,
            "id", 
            "entry_id"
        );
    }
}