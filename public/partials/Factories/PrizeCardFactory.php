<?php

class PrizeCardFactory {
    public static function getPrizeCardModel(array $prizeCardData, string $judgesNamesString): PrizeCardModel {
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

        $prize = Prize::from($prizeCardData['prize']);
        return match ($prize) {
            Prize::SECTION, Prize::SECTION_AWARD => new SectionPrizeCard(...$params),
            Prize::JUNIOR => new JuniorPrizeCard(...$params),
            Prize::GRANDCHALLENGE, Prize::GC_AWARD  => new GrandChallengePrizeCard(...(self::setJudgesNameForGC($params, $judgesNamesString))),
            default         => new ClassPrizeCard(...$params)
        };
    }

    private static function setJudgesNameForGC(array $params, string $judgesNamesString): array {
        $params[11] = $judgesNamesString;
        return $params;
    }
}