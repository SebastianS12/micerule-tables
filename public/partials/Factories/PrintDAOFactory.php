<?php

class PrintDAOFactory{
    public static function getPrintDAO(int $prizeID): IPrintDAO{
        $prize = Prize::from($prizeID);
        return match($prize){
            Prize::SECTION, Prize::GRANDCHALLENGE => new ChallengePlacementDAO(),
            Prize::SECTION_AWARD, PRIZE::GC_AWARD => new AwardsPlacementDAO(),
            default => new ClassPlacementDAO()
        };
    }
}