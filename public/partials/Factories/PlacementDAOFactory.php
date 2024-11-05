<?php

class PlacementDAOFactory{
    public static function getPlacementDAO(int $prizeID): IPlacementDAO{
        $prize = Prize::from($prizeID);
        return match($prize){
            Prize::SECTION, Prize::GRANDCHALLENGE => new ChallengePlacementDAO(),
            default => new ClassPlacementDAO()
        };
    }
}