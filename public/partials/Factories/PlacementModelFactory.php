<?php

class PlacementModelFactory{
    public static function getPlacementModel(int $prizeID, array $modelAttributes): IPlacementModel
    {
        $prize = Prize::from($prizeID);
        return match($prize){
            Prize::SECTION, Prize::GRANDCHALLENGE => ChallengePlacementModel::createWithID(...$modelAttributes),
            default => ClassPlacementModel::createWithID(...$modelAttributes),
        };
    }
}