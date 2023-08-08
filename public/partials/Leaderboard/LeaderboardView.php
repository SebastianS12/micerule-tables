<?php

class LeaderboardView
{
    public static function getSeasonSelectHtml(){
        $html ="<p>Show Season:<p>";
        $html .='<select id="seasonSelect">';
        foreach(LeaderboardController::getSeasonDates() as $index => $seasonDates){
            $displayedSeasonDates = ($index == 0) ? "Current Season" : date("Y", $seasonDates['seasonStartDate'])." / ".date("Y", $seasonDates['seasonEndDate']);
            $html .= '<option value="'.$seasonDates["seasonStartDate"].'/'.$seasonDates["seasonEndDate"].'">'.$displayedSeasonDates.'</option>';
        }
        $html .= "</select>";

        return $html;
    }

    public static function getTopTwentyHtml($seasonStartDate, $seasonEndDate)
    {
        $topTwentyData = LeaderboardController::getTopTwentyData($seasonStartDate, $seasonEndDate);

        $html = "<div id='lbTopTwenty' class='lbTables' style='width:100%;'>";
        $html .= "<div class = 'resultTable'>";
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
        $html .= "</div>";
        $html .= "||";

        return $html;
    }

    private static function getBlurredTableCells()
    {
        $html = "<td class = 'avatarCell'><img src = '" . plugin_dir_url(__FILE__) . "lock.svg' style = 'height:96px; width:96px'></td>";
        $html .= "<td class = 'resultCellBlur'>";
        $html .= self::getBlurredNameDiv();
        $html .= "</td>";

        return $html;
    }

    private static function getBlurredNameDiv(){
        return "<div class ='blurDiv' style='width:" . random_int(35, 82) . "px;background-image: url(" . plugin_dir_url(__FILE__) . "blur.png);height:20px ;display:inline-block;" . random_int(0, 500) . "px 0'></div><span> </span>
                <div class ='blurDiv' style='width:" . random_int(35, 90) . "px;background-image: url(" . plugin_dir_url(__FILE__) . "blur.png);height:20px ;display:inline-block;" . random_int(0, 500) . "px 0'></div>";
    }

    public static function getVarietyPopularityHtml($seasonStartDate, $seasonEndDate)
    {
        $seasonVarietyData = LeaderboardController::getVarietyPopularityData($seasonStartDate, $seasonEndDate);
        $previousSeasonVarietyStandings = LeaderboardController::getPreviousSeasonVarietyStandings($seasonStartDate);
        $html = "<div id='lbVarieties' class='lbTables' style='width:100%;'>";
        $html .= "<div class = 'varietiesResultTable'>";
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
        foreach ($seasonVarietyData as $index => $varietyData) {
            if ($index > 0 && $varietyData['variety_total'] != $seasonVarietyData[$index - 1]['variety_total']) {
                $position++;
            }

            $firstPosClass = ($position == 1) ? 'firstPos' : '';
            $html .= "<tr class = " . $firstPosClass . ">";
            $html .= "<td class = 'resultCell'>" . $position . "</td>";
            $breedData = Breed::getBreedData($varietyData['variety_name']);
            $html .= "<td class = 'resultCell'><div class='variety-icon' style='background:url(" . $breedData['icon_url'] . ");background-repeat:no-repeat;background-color:" . $breedData['colour'] . "'></div></td>";
            $html .= "<td class = 'resultCell' >" . $varietyData['variety_name'] . "</td>";
            $html .= "<td class = 'resultCell2'>" . $varietyData['variety_total'] . "</td>";
            $html .= "<td class = 'resultCell' >" . ((isset($previousSeasonVarietyStandings[$varietyData['variety_name']])) ? $previousSeasonVarietyStandings[$varietyData['variety_name']] : "") . "</td >";
            $html .= "</tr>";
        }

        $html .= "</tbody>";
        $html .= "</table>";
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    public static function getMobileChartLegendHtml($seasonStartDate, $seasonEndDate)
    {
        $legendHtml = "<div class = 'chartLegend mobile'>";
        $legendHtml .= "<table id = 'chartLegend_Table' >";
        $legendHtml .= "<thead>";
        $legendHtml .= "<tr>";
        $legendHtml .= "<th>#</th>";
        $legendHtml .= "<th>Variety</th>";
        $legendHtml .= "<th>Shows Won</th>";
        $legendHtml .= "<th>%</th>";
        $legendHtml .= "</tr>";
        $legendHtml .= "</thead>";

        //table contents
        $bisVarietyChartData = LeaderboardController::getBISVarietyChartData($seasonStartDate, $seasonEndDate);
        $seasonShowCount = LeaderboardController::getSeasonShowCount($seasonStartDate, $seasonEndDate);
        foreach ($bisVarietyChartData as $varietyChartData) {
            $legendHtml .= "<tr>";
            $legendHtml .= "<td class = 'legendCellNumber'><div class='legend-number' style='background-color:" . $varietyChartData['colour'] . " '></div></td>";
            $legendHtml .= "<td class = 'legendCellVariety'>" . $varietyChartData['variety_name'] . "</td>";
            $legendHtml .= "<td class = 'legendCellNumber'>" . $varietyChartData['times_won'] . "</td>";
            $legendHtml .= "<td class = 'legendCellNumber'>" . sprintf("%.1f", (($varietyChartData['times_won'] / $seasonShowCount) * 100)) . "%</td>";
            $legendHtml .= "</tr>";
        }
        $legendHtml .= "</table>";
        $legendHtml .= "</div>";

        return $legendHtml;
    }

    public static function getSeasonBISWinnerHtml($seasonStartDate, $seasonEndDate)
    {
        $html = "<div id='lbBIS' class='lbTables' style='width:100%;'>";
        $html .= "<div class = 'bisResultTable'>";
        $html .= "<table id = 'micerule_bis_resultTable' style='width:100%'>";
        $html .= "<thead><tr>";
        $html .= "<th class = 'bisResultHeader'>Pos.</th>";
        $html .= "<th class = 'bisResultHeader'>Fancier</th>";
        $html .= "<th class = 'bisResultHeader'>Shows Won</th>";
        $html .= "</tr></thead><tbody>";

        $position = 1;
        $seasonBISWinnerCountData = LeaderboardController::getSeasonBISWinnerCountData($seasonStartDate, $seasonEndDate);
        foreach ($seasonBISWinnerCountData as $index => $winnerCountData) {
            if ($index > 0 && $winnerCountData['times_won'] != $seasonBISWinnerCountData[$index - 1]['times_won'])
                $position++;

            $firstPosClass = ($position == 1) ? 'firstPos' : '';
            $html .= "<tr class = " . $firstPosClass . ">";
            $html .= "<td class = 'resultCell'>" . $position . "</td>";
            if (is_user_logged_in()) {
                $html .= "<td class = 'resultCell'>" . $winnerCountData['fancier_name'] . "</td>";
            } else {
                $html .= "<td class = 'resultCellBlur'>";
                $html .= self::getBlurredNameDiv();
                $html .= "</td>";
            }
            $html .= "<td class = 'resultCell2'>" . $winnerCountData['times_won'] . "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody>";
        $html .= "</table>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "||";

        return $html;
    }

    public static function getSeasonSectionLeaderHtml($seasonStartDate, $seasonEndDate)
    {
        $seasonSectionLeaderData = LeaderboardController::getSeasonSectionLeaderData($seasonStartDate, $seasonEndDate);
        $sectionLeadersHtml = "<div id='lbSectionLeaders' class='lbTables' style='width:100%;'>";
        foreach ($seasonSectionLeaderData as $sectionName => $sectionLeaderData) {
            $sectionLeadersHtml .= "<div class='section-card'>";
            $sectionLeadersHtml .= "<p class='sectionTitle'>" .ucfirst($sectionName). "</p>";
            $sectionLeadersHtml .= "<div class='sectionResultHeader'>";
            $sectionLeadersHtml .= "<p>Rank</p>";
            $sectionLeadersHtml .= "<p>Name</p>";
            $sectionLeadersHtml .= "<p>Points</p>";
            $sectionLeadersHtml .= "</div>";
            $sectionLeadersHtml .= "<div id='micerule_section_resultTable'>";

            $position = 1;
            foreach($sectionLeaderData as $index => $sectionWinnerData) {
                if ($index > 0 && $sectionWinnerData['section_total'] != $sectionLeaderData[$index - 1]['section_total']) {
                    $position++;
                }
                $firstPosClass = ($position == 1) ? 'firstPos' : '';
                $sectionLeadersHtml .= "<ul class = " . $firstPosClass . ">";
                $sectionLeadersHtml .= "<li>";
                $sectionLeadersHtml .= "<span class='resultCell0'>" . $position . "</span>";
                if (is_user_logged_in()) {
                    $sectionLeadersHtml .= "<span class='resultCell'>" .$sectionWinnerData['fancier_name']. "</span>";
                } else {
                    $sectionLeadersHtml .= "<span class = 'resultCellBlur'>";
                    $sectionLeadersHtml .= self::getBlurredNameDiv();
                    $sectionLeadersHtml .= "</span>";
                }
                $sectionLeadersHtml .= "<span class='resultCell2'>" .$sectionWinnerData['section_total']. "</span>";
                $sectionLeadersHtml .= "</li>";
                $sectionLeadersHtml .= "</ul>";
            }
            $sectionLeadersHtml .= "</div>";
            $sectionLeadersHtml .= "</div>";
        }
        $sectionLeadersHtml .= "</div>";

        return $sectionLeadersHtml;
    }
}
