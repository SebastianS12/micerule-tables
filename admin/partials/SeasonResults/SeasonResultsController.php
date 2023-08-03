<?php 

class SeasonResultsController{
    public static function getSeasonResults($dateFrom, $dateTo){
        $seasonResultsModel = new SeasonResultsModel();
        $fancierResults = $seasonResultsModel->getFancierSeasonResults($dateFrom, $dateTo);
        $seasonResults = array();
        foreach($fancierResults as $fancierResult){
            $fancierSeasonResult = $fancierResult['points'];
            if($fancierResult['judge_count'] != null)
                $fancierSeasonResult += floor($fancierSeasonResult * 0.03 * $fancierResult['judge_count']);
            
            if($fancierResult['fancier_name'] != '')
                $seasonResults[$fancierResult['fancier_name']] = $fancierSeasonResult;
        }
        arsort($seasonResults);
        $seasonResults = array_slice($seasonResults,0,20);

        return $seasonResults;
    }

    public static function createSeasonTable($dateFrom, $dateTo){
        $seasonResultsModel = new SeasonResultsModel();
        $seasonResultsModel->createSeasonTable($dateFrom, $dateTo);
    }

    public static function deleteSeasonTable($id){
        $seasonResultsModel = new SeasonResultsModel();
        $seasonResultsModel->deleteSeasonTable($id);
    }
}