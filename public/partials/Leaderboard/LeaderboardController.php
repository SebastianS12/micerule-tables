<?php

class LeaderboardController{
    public static function getTopTwentyData($seasonStartDate, $seasonEndDate){
        $seasonResultsModel = new SeasonResultsModel();
        $fancierSeasonData = $seasonResultsModel->getFancierSeasonResults($seasonStartDate, $seasonEndDate);
        $topTwentyData = array();
        foreach($fancierSeasonData as $fancierResults){
            if($fancierResults['fancier_name'] != null){
                $topTwentyEntry = array();
                $topTwentyEntry['fancierName'] = $fancierResults['fancier_name'];
                $topTwentyEntry['accumulatedPoints'] = $fancierResults['points'];
                $topTwentyEntry['timesJudged'] = isset($fancierResults['judge_count']) ? $fancierResults['judge_count'] : "";
                $topTwentyEntry['adjustedPoints'] = $fancierResults['points'] + $fancierResults['points'] * 0.03 * $fancierResults['judge_count'];
                $topTwentyEntry['grandTotal'] = round($topTwentyEntry['adjustedPoints']);

                array_push($topTwentyData, $topTwentyEntry);
            }
        }
        $col = array_column( $topTwentyData, "grandTotal" );
        array_multisort($col, SORT_DESC, $topTwentyData);
        $topTwentyData = array_slice($topTwentyData, 0, 20);

        return $topTwentyData;
    }

    public static function getAvatar($userName){
        $leaderboardModel = new LeaderboardModel();
        $userID = $leaderboardModel->getUserID($userName);
        return get_avatar($userID, 96,'monsterid');
    }

    public static function getVarietyPopularityData($seasonStartDate, $seasonEndDate){
        $leaderboardModel = new LeaderboardModel();
        $currentSeasonVarietyData = $leaderboardModel->getSeasonVarietyData($seasonStartDate, $seasonEndDate);
        $currentSeasonVarietyData = array_slice($currentSeasonVarietyData, 0, 20);
        return $currentSeasonVarietyData;
    }

    public static function getPreviousSeasonVarietyStandings($currentSeasonStartDate){
        $leaderboardModel = new LeaderboardModel();
        $previousSeasonVarietyData = array();
        $previousSeasonDates = $leaderboardModel->getPreviousSeasonDates($currentSeasonStartDate);
        if($previousSeasonDates != null)
            $previousSeasonVarietyData = $leaderboardModel->getSeasonVarietyData($previousSeasonDates['dateFrom'], $previousSeasonDates['dateTo']);

        $previousSeasonVarietyStandings = array();
        $position = 1;
        foreach($previousSeasonVarietyData as $index => $varietyData){
            if ($index > 0 && $varietyData['variety_total'] != $previousSeasonVarietyData[$index - 1]['variety_total'])
                $position++;
            
            $previousSeasonVarietyStandings[$varietyData['variety_name']] = $position;
        }

        return $previousSeasonVarietyStandings;
    }

    public static function getBISVarietyChartData($seasonStartDate, $seasonEndDate){
        $leaderboardModel = new LeaderboardModel();
        $bisVarietyChartData = $leaderboardModel->getBISVarietyTimesWon($seasonStartDate, $seasonEndDate);
        $bisVarietyChartData = self::appendBreedDataToChartData($bisVarietyChartData);

        return $bisVarietyChartData;
    }

    private static function appendBreedDataToChartData($chartData){
        foreach($chartData as $index => $bisVarietyData){
            $breedData = Breed::getBreedData($bisVarietyData['variety_name']);
            $chartData[$index]['colour'] = $breedData['colour'];
            $chartData[$index]['css_class'] = $breedData['css_class'];
        }

        return $chartData;
    }

    public static function getSeasonShowCount($seasonStartDate, $seasonEndDate){
        $leaderboardModel = new LeaderboardModel();
        return $leaderboardModel->getSeasonShowCount($seasonStartDate, $seasonEndDate);
    }

    public static function getSeasonBISWinnerCountData($seasonStartDate, $seasonEndDate){
        $leaderboardModel = new LeaderboardModel();
        return $leaderboardModel->getSeasonBISWinnerCountData($seasonStartDate, $seasonEndDate);
    }

    public static function getSeasonSectionLeaderData($seasonStartDate, $seasonEndDate){
        $seasonSectionLeaderData = array();
        foreach(EventProperties::SECTIONNAMES as $sectionName){
            $seasonSectionLeaderData[strtolower($sectionName)] = array();
        }

        $leaderboardModel = new LeaderboardModel();
        foreach($leaderboardModel->getSeasonSectionLeaderData($seasonStartDate, $seasonEndDate) as $sectionWinnerData){
            array_push($seasonSectionLeaderData[$sectionWinnerData['section']], $sectionWinnerData);
        }

        return $seasonSectionLeaderData;
    }

    public static function getSeasonDates(){
        $seasonResultsModel = new SeasonResultsModel();
        $currentSeasonStartDate = $seasonResultsModel->getCurrentSeasonDateFrom();
        //SeasonResultsModel returns Data in reverse order: From earliest to latest season
        $seasonData = array_reverse($seasonResultsModel->getSeasonTableData());

        $seasonDates = array();
        array_push($seasonDates, array("seasonStartDate" => $currentSeasonStartDate, "seasonEndDate" => time()));
        foreach($seasonData as $seasonEntry){
            array_push($seasonDates, array("seasonStartDate" => $seasonEntry->dateFrom, "seasonEndDate" => $seasonEntry->dateTo));
        }

        return $seasonDates;
    }
}