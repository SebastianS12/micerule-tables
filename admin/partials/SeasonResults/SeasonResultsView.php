<?php

class SeasonResultsView {
    public static function getSeasonTableHtml($dateFrom, $dateTo){
        $html = "<div class = 'season_resultTable'>";
        $html .= "<table id = 'micerule_season_resultTable'>";
        $html .= "<thead><tr>";
        $html .= "<th class = 'season_resultHeader'>Name</th>";
        $html .= "<th class = 'season_resultHeader'>Points</th>";
        $html .= "</tr></thead><tbody>";

        $seasonTableData = SeasonResultsController::getSeasonResults($dateFrom, $dateTo);
        foreach($seasonTableData as $fancierName => $points){
            $html .= "<tr>";
            $html .= "<td class = 'season_resultCell'>".$fancierName."</td>";
            $html .= "<td class = 'season_resultCell'>".$points."</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody>";
        $html .= "</table>";
        $html .= "</div>";

        return $html;
    }
}