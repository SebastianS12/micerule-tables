<?php

class LeaderboardView
{
    public static function getTopTwentyHtml($seasonStartDate, $seasonEndDate)
    {
        $topTwentyData = LeaderboardController::getTopTwentyData($seasonStartDate, $seasonEndDate);

        $html = "<div class = 'resultTable'>";
        $html .= "<table id = 'micerule_resultTable' style='width:100%'>";
        $html .= "<thead><tr>";
        $html .= "<th class = 'resultHeader'></th>";
        $html .= "<th class = 'avatarHeader'></th>";
        $html .= "<th class = 'resultHeader'>Name</th>";
        $html .= "<th class = 'resultHeader'>Points Accum'd</th>";
        $html .= "<th class = 'resultHeader'>Times Judged</th>";
        $html .= "<th class = 'resultHeader'>Adjusted Points</th>";
        $html .= "<th class = 'resultHeader'>Grand Total</th>";
        $html .= "</tr></thead><tbody>";

        $position = 1;
        foreach ($topTwentyData as $index => $topTwentyEntry) {
            if ($index > 0 && $topTwentyEntry['grandTotal'] != $topTwentyData[$index - 1]['grandTotal'])
                $position++;

            $firstPositionClass = ($position == 1) ? 'firstPos' : '';
            $html .= "<tr class = " . $firstPositionClass . ">";
            $html .= "<td class = 'season-position'>" . $position . "</td>";
            if (is_user_logged_in()) {
                $html .= "<td class = 'avatarCell'>" . LeaderboardController::getAvatar($topTwentyEntry['fancierName']) . "</th>";
                $html .= "<td class = 'resultCell'>" . $topTwentyEntry['fancierName'] . "</td>";
            } else {
                $html .= self::getBlurredTableCells();
            }
            $html .= "<td class = 'resultCell2'>" . $topTwentyEntry['accumulatedPoints'] . "</td>";
            $html .= "<td class = 'resultCell2'>" . $topTwentyEntry['timesJudged'] . "</td>";
            $html .= "<td class = 'resultCell2'>" . $topTwentyEntry['adjustedPoints'] . "</td>";
            $html .= "<td class = 'resultCell2'>" . $topTwentyEntry['grandTotal'] . "</td>";
            $html .= "</tr>";
        }

        $html .= "</tbody>";
        $html .= "</table>";
        $html .= "</div>";
        $html .= "||";

        return $html;
    }

    private static function getBlurredTableCells()
    {
        $html = "<td class = 'avatarCell'><img src = '" . plugin_dir_url(__FILE__) . "lock.svg' style = 'height:96px; width:96px'></td>";
        $html .= "<td class = 'resultCellBlur'>";
        $html .= "<div class ='blurDiv' style='width:" . random_int(35, 82) . "px;background-image: url(" . plugin_dir_url(__FILE__) . "blur.png);height:20px ;display:inline-block;" . random_int(0, 500) . "px 0'></div><span> </span>
                  <div class ='blurDiv' style='width:" . random_int(35, 90) . "px;background-image: url(" . plugin_dir_url(__FILE__) . "blur.png);height:20px ;display:inline-block;" . random_int(0, 500) . "px 0'></div>";
        $html .= "</td>";

        return $html;
    }

    public static function getVarietyPopularityHtml($seasonStartDate, $seasonEndDate)
    {
        $seasonVarietyData = LeaderboardController::getVarietyPopularityData($seasonStartDate, $seasonEndDate);
        $previousSeasonVarietyStandings = LeaderboardController::getPreviousSeasonVarietyStandings($seasonStartDate);
        $html = "<div class = 'varietiesResultTable'>";
        $html .= "<table id = 'micerule_varieties_resultTable' style='width:100%'>";

        $html .= "<thead><tr>";
        $html .= "<th class = 'varietiesResultHeader'>Pos.</th>";
        $html .= "<th class = 'varietiesResultHeader'></th>";
        $html .= "<th class = 'varietiesResultHeader'><span>Variety</span></th>";
        $html .= "<th class = 'varietiesResultHeader'><span>Points Won</span></th>";
        $html .= "<th class = 'varietiesResultHeader'><span>Last Y Pos</span></th>";
        $html .= "</tr></thead><tbody>";

        $position = 1;
        //table contents
        foreach($seasonVarietyData as $index => $varietyData) {
            if ($index > 0 && $varietyData['variety_total'] != $seasonVarietyData[$index - 1]['variety_total']) {
                $position++;
            }

            $firstPosClass = ($position == 1) ? 'firstPos' : '';
            $html .= "<tr class = " . $firstPosClass . ">";
            $html .= "<td class = 'resultCell'>" . $position . "</td>";
            $breedData = Breed::getBreedData($varietyData['variety_name']);
            $html .= "<td class = 'resultCell'><div class='variety-icon' style='background:url(" .$breedData['icon_url']. ");background-repeat:no-repeat;background-color:" .$breedData['colour']. "'></div></td>";
            $html .= "<td class = 'resultCell' >" .$varietyData['variety_name']. "</td>";
            $html .= "<td class = 'resultCell2'>" .$varietyData['variety_total']. "</td>";
            $html .= "<td class = 'resultCell' >" .((isset($previousSeasonVarietyStandings[$varietyData['variety_name']]))? $previousSeasonVarietyStandings[$varietyData['variety_name']] : ""). "</td >";
            $html .= "</tr>";
        }
        
        $html .= "</tbody>";
        $html .= "</table>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}
