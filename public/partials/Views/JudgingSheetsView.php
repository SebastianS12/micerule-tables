<?php

class JudgingSheetsView
{
    public static function getHtml($eventPostID)
    {
        $judgingSheetsService = new JudgingSheetsService($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $viewModel = $judgingSheetsService->prepareViewModel();
        $html = "<div class = 'judgingSheets content' style = 'display : none'>";
        $html .= "<div class = 'sheet-set'>";

        $html .= self::getGrandChallengeSheetsHtml($viewModel->grandChallengeSheets);
        foreach($viewModel->classSheets as $judgeName => $judgeClassSheets){
            foreach($judgeClassSheets as $sectionName => $sectionClassSheets){
                foreach($sectionClassSheets as $classSheet){
                    $html .= self::getClassSheetHtml($classSheet);
                }

                foreach($viewModel->sectionChallengeSheets[$judgeName][$sectionName] as $sectionChallengeSheet){
                    $html .= self::getChallengeSheetHtml($sectionChallengeSheet);
                }
            }
        }

        $html .= self::getOptionalClassSheetsHtml($viewModel);
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    private static function getGrandChallengeSheetsHtml(array $grandChallengeSheets)
    {
        $html = "";

        foreach($grandChallengeSheets as $sheet){
            $html .= self::getChallengeSheetHtml($sheet);
        }
        
        return $html;
    }

    private static function getClassSheetHtml(array $classSheet)
    {
        $noEntriesClass = (count($classSheet['penNumbers']) == 0) ? "no-entries" : "";
        $html = "<div class='breed-class-report ".$noEntriesClass."'>
              <table>
                <thead>";
        $html .= self::getSheetHeaderHtml($classSheet['classIndex'], $classSheet['className'], $classSheet['age'], $classSheet['judgeName']);
        $html .= "  </thead> <tbody>";
        $showVarietyPrompt = $classSheet['showVarietyPrompt'];
        if (count($classSheet['penNumbers']) > 0) {
            foreach ($classSheet['penNumbers'] as $penNumber) {
                $html .= self::getSheetEntryRowHtml($penNumber, $showVarietyPrompt);
            }
        } else {
            $html .= "<tr><span>No Entries</span></tr>";
        }
        
        $html .= self::getEmptyRowHtml();
        $html .= "</tbody></table></div>";

        return $html;
    }

    private static function getSheetHeaderHtml(int $index, string $className, string $age, string $judgeName)
    {
        $html = "<tr>
                  <th colspan=3><span>Class " . $index . " | " . $className . " | " . $age . " - Judge: <strong>" . $judgeName . "</strong></span></th>
                  <th></th>
                  <th colspan ='2' class='side-slip'><p> Class " . $index . "</p></th>
                  </tr>
                  <tr>
                  <th class='js-pen-no'>№</th>
                  <th class='js-award'>Pl.</th>
                  <th class='js-notes'>Judge's Notes</th>
                  <th class='perforation'></th>
                  <th class='js-pen-no'>№</th>
                  <th class='js-award'>Pl.</th>
                </tr>";

        return $html;
    }

    private static function getSheetEntryRowHtml(string|int $penNumber, bool $showVarietyPrompt, string|int $placement = "")
    {
        $html = "<tr>
                  <td class='js-pen-no'>" . $penNumber . "</td>
                  <td class='js-award'>" . $placement . "</td>
                  <td class='js-notes'>";
        $html .= ($showVarietyPrompt) ? "<span class='variety-prompt'>! →</span>" : "";
        $html .= "</td>
                  <td class='perforation'></td>
                  <td class='js-pen-no'>";
        $html .= ($showVarietyPrompt) ? "<hr ></td>" : "";
        $html .= "<td class='js-award'>" . $placement . "</td>
                </tr>";

        return $html;
    }

    private static function getEmptyRowHtml()
    {
        $html = "<tr>
                  <td class='js-pen-no'></td>
                  <td class='js-award'></td>
                  <td class='js-notes'></td>
                  <td class='perforation'></td>
                  <td class='js-pen-no'></td>
                  <td class='js-award'></td>
                </tr>";

        return $html;
    }

    private static function getChallengeSheetHtml(array $challengeSheet): string
    {
        $html = "<div class='breed-class-report challenge'><table><thead>";
        $html .= self::getSheetHeaderHtml($challengeSheet['challengeIndex'], $challengeSheet['challengeName'], $challengeSheet['age'], $challengeSheet['judgeName']);
        $html .= " </thead><tbody>";

        for ($placement = 1; $placement < 4; $placement++) {
            $html .= self::getSheetEntryRowHtml("", "style = 'display : none'", $placement);
        }

        if ($challengeSheet['addSectionBestSheet']) {
            //TODO: Enum for this?
            $section = ($challengeSheet['challengeName'] == EventProperties::GRANDCHALLENGE) ? "" : explode(" ", $challengeSheet['challengeName'])[0];
            $html .= self::getChallengeSheetBestHtml($section, "Best");
            $html .= self::getChallengeSheetBestHtml($section, "BOA");
        }

        $html .= self::getEmptyRowHtml();
        $html .=   "</tbody></table></div>";

        return $html;
    }

    private static function getChallengeSheetBestHtml($section, $best)
    {
        //static Best/BOA block
        $html =     "<tr>
        <td class='js-pen-no'></td>
        <td class='js-award'></td>
        <td class='js-notes'></td>
        <td class='perforation'></td>
        <td class='js-pen-no'></td>
        <td class='js-award'></td>
        </tr>";
        $html .=     "<tr>
        <td class='js-pen-no'></td>
        <td class='js-award'></td>
        <td class='js-notes'></td>
        <td class='perforation'></td>
        <td class='js-pen-no' colspan = 2>" . $best . " " . $section . "</td>
        </tr>";
        $html .=     "<tr>
        <td class='js-pen-no'></td>
        <td class='js-award'></td>
        <td class='js-notes'></td>
        <td class='perforation'></td>
        <td class='js-pen-no'>№</td>
        <td class='js-award'></td>
        </tr>";
        $html .=     "<tr>
        <td class='js-pen-no'></td>
        <td class='js-award'></td>
        <td class='js-notes'></td>
        <td class='perforation'></td>
        <td class='js-pen-no'>Age</td>
        <td class='js-award'></td>
        </tr>";
        $html .=     "<tr>
        <td class='js-pen-no'></td>
        <td class='js-award'></td>
        <td class='js-notes'></td>
        <td class='perforation'></td>
        <td class='js-pen-no'>Var</td>
        <td class='js-award'></td>
        </tr>";

        return $html;
    }

    private static function getOptionalClassSheetsHtml(JudgingSheetsViewModel $viewModel)
    {
        $html = "";
        foreach ($viewModel->optionalClassSheets as $classSheet) {
            $html .= self::getClassSheetHtml($classSheet);
        }

        return $html;
    }
}
