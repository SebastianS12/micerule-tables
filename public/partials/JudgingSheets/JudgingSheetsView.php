<?php

class JudgingSheetsView
{
    public static function getHtml($eventPostID)
    {
        $eventJudgesModel = new EventJudgesHelper();
        $html = "<div class = 'judgingSheets content' style = 'display : none'>";
        $html .= "<div class = 'sheet-set'>";

        $html .= self::getGrandChallengeSheetsHtml($eventPostID);
        foreach ($eventJudgesModel->getEventJudgeNames($eventPostID) as $judgeName) {
            foreach ($eventJudgesModel->getJudgeSections($eventPostID, $judgeName) as $sectionName) {
                $html .= self::getSectionClassSheetsHtml($eventPostID, $sectionName, $judgeName);
                $html .= self::getSectionChallengeSheetsHtml($eventPostID, $sectionName, $judgeName);
            }
        }
        $html .= self::getOptionalClassSheetsHtml($eventPostID);
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

    private static function getGrandChallengeSheetsHtml($eventPostID)
    {
        $html = "";
        $grandChallengeJudgeName = EventJudgesHelper::getGrandChallengeJudges($eventPostID);
        
        $html .= self::getChallengeSheetHtml($eventPostID, EventProperties::GRANDCHALLENGE, "Grand Challenge", "Ad", $grandChallengeJudgeName, false);
        $html .= self::getChallengeSheetHtml($eventPostID, EventProperties::GRANDCHALLENGE, "Grand Challenge", "U8", $grandChallengeJudgeName, true);

        return $html;
    }

    private static function getSectionClassSheetsHtml($eventPostID, $sectionName, $judgeName)
    {
        $showClassesModel = new ShowClassesModel();
        $html = "";
        foreach ($showClassesModel->getShowSectionClassNames(EventProperties::getEventLocationID($eventPostID), $sectionName) as $className) {
            $html .= self::getClassSheetHtml($eventPostID, $className, "Ad", $judgeName);
            $html .= self::getClassSheetHtml($eventPostID, $className, "U8", $judgeName);
        }

        return $html;
    }

    private static function getClassSheetHtml($eventPostID, $className, $age, $judgeName)
    {
        $classModel = new ShowClassModel($eventPostID, $className, $age);
        $html = "<div class='breed-class-report'>
              <table>
                <thead>";
        $html .= self::getSheetHeaderHtml($classModel, $judgeName);
        $html .= "  </thead> <tbody>";
        if (count($classModel->penNumbers) > 0) {
            foreach ($classModel->penNumbers as $penNumber) {
                $entry = ShowEntry::createWithPenNumber($eventPostID, $penNumber);
                $showVarietyPrompt = (Breed::classIsStandardBreed($entry->className)) ? "style = 'display : none'" : "";
                $html .= self::getSheetEntryRowHtml($entry->penNumber, $showVarietyPrompt);
            }
        } else {
            $html .= "<tr><span>No Entries</span></tr>";
        }
        $html .= self::getEmptyRowHtml();
        $html .= "</tbody></table></div>";

        return $html;
    }

    private static function getSheetHeaderHtml($classModel, $judgeName)
    {
        $html = "<tr>
                  <th colspan=3><span>Class " . $classModel->index . " | " . $classModel->name . " | " . $classModel->age . " - Judge: <strong>" . $judgeName . "</strong></span></th>
                  <th></th>
                  <th colspan ='2' class='side-slip'><p> Class " . $classModel->index . "</p></th>
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

    private static function getSheetEntryRowHtml($penNumber, $showVarietyPrompt, $placement = "")
    {
        $html = "<tr>
                  <td class='js-pen-no'>" . $penNumber . "</td>
                  <td class='js-award'>" . $placement . "</td>
                  <td class='js-notes'><span class='variety-prompt' " . $showVarietyPrompt . ">! →</span></td>
                  <td class='perforation'></td>
                  <td class='js-pen-no'><hr " . $showVarietyPrompt . "></td>
                  <td class='js-award'>" . $placement . "</td>
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

    private static function getSectionChallengeSheetsHtml($eventPostID, $sectionName, $judgeName)
    {
        $html = self::getChallengeSheetHtml($eventPostID, EventProperties::getChallengeName($sectionName), $sectionName, "Ad", $judgeName, false);
        $html .= self::getChallengeSheetHtml($eventPostID, EventProperties::getChallengeName($sectionName), $sectionName, "U8", $judgeName, true);

        return $html;
    }

    private static function getChallengeSheetHtml($eventPostID, $challengeName, $sectionName, $age, $judgeName, $addSheetBestHtml)
    {
        $challengeModel = new ShowChallengeModel($eventPostID, $challengeName, $sectionName, $age);
        $html = "<div class='breed-class-report challenge'><table><thead>";
        $html .= self::getSheetHeaderHtml($challengeModel, $judgeName);
        $html .= " </thead><tbody>";

        for ($placement = 1; $placement < 4; $placement++) {
            $html .= self::getSheetEntryRowHtml("", "style = 'display : none'", $placement);
        }

        if ($addSheetBestHtml) {
            $section = ($challengeModel->name == EventProperties::GRANDCHALLENGE) ? "" : explode(" ", $challengeModel->name)[0];
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

    private static function getOptionalClassSheetsHtml($eventPostID)
    {
        $showClassesModel = new ShowClassesModel();
        $html = "";
        foreach ($showClassesModel->getShowSectionClassNames(EventProperties::getEventLocationID($eventPostID), "optional") as $className) {
            $html .= self::getClassSheetHtml($eventPostID, $className, "AA", "");
        }

        return $html;
    }
}
