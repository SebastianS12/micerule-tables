<?php

class ChallengeRowView{
    public static function render($data){
       $html = self::renderChallengeRow($data['ad'], $data['showBIS'], $data['prizeID']);
       $html .= self::renderChallengeRow($data['u8'], $data['showBIS'], $data['prizeID']);
       return $html;
    }

    private static function renderChallengeRow(array $ageData, string $showBIS, int $prizeID){
        $html = "<table><tbody>";
        $html .= "<tr class='challenge-row'><td class='table-pos'>" . $ageData['challengeIndex'] . "</td><td class='breed-class'>" . $ageData['challengeName']. " " . $ageData['age'] . "</td><td class='age'></td><td class='placement-" . $ageData['age'] . "'></td><td class='sectionBest-" . $ageData['age'] . "'><div class='placement-checks' style='display:".$showBIS."'>";
        $html .= "<input type = 'checkbox' class = 'BISCheck' id = 'BIS-" . $ageData['challengeIndexID']. "-check' " . $ageData['bisChecked'] . " " . $ageData['bisDisabled'] . " data-index-id = ".$ageData['challengeIndexID']." data-oa-index-id = ".$ageData['oaChallengeIndexID']." data-prize-id = ".$prizeID."></input><label for = 'BIS-" . $ageData['challengeIndexID'] . "-check'><span class='is-best'>BEST</span><span class='is-boa'>BOA</span></label>";
        $html .= "</div></td><td class='ageBest-" . $ageData['age'] . "'></td></tr>";
        foreach ($ageData['placements'] as $placementData) {
            $html .= "<tr>";
            $html .= "<td>" . $placementData['penNumber'] . "</td>";
            $html .= "<td>" . $placementData['userName'] . "</td>";
            $html .= "<td>" . $placementData['varietyName'] . "</td>";
            $html .= "<td>" . $placementData['placement'] . "</td>";
            $html .= "<td></td>";
            $html .= "<td></td>";
        }
        $html .= "</tbody></table>";

        return $html;
    }
}