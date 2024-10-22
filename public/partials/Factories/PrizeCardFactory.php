<?php

class PrizeCardFactory {
    public static function getPrizeCardModel(array $prizeCardData): PrizeCardModel {
        $params = [
            $prizeCardData['placement_id'], 
            $prizeCardData['placement'], 
            $prizeCardData['prize'], 
            $prizeCardData['age'], 
            $prizeCardData['user_name'], 
            $prizeCardData['class_name'], 
            $prizeCardData['variety_name'], 
            $prizeCardData['pen_number'], 
            $prizeCardData['index_number'],
            $prizeCardData['section'], 
            $prizeCardData['printed'],
            $prizeCardData['judge_name'], 
            $prizeCardData['entry_count'],
            $prizeCardData['award']
        ];

        return match ($prizeCardData['prize']) {
            Prize::SECTION, Prize::SECTION_AWARD => new SectionPrizeCard(...$params),
            Prize::JUNIOR => new JuniorPrizeCard(...$params),
            Prize::GRANDCHALLENGE, Prize::GC_AWARD  => new GrandChallengePrizeCard(...$params),
            default         => new ClassPrizeCard(...$params)
        };
    }
}