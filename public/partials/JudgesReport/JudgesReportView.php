<?php

class JudgesReportView
{
    public static function getHtml($eventPostID)
    {
        $judgesModel = new EventJudgesHelper();
        $user = wp_get_current_user();
        $userName = $user->display_name;

        $html = "<div class = 'judgesReport content' style = 'display: none'>";

        foreach ($judgesModel->getEventJudgeNames($eventPostID) as $judgeNo => $judgeName) {
            if ($userName == $judgeName || current_user_can('administrator')) {
                $html .= "<table>";
                $html .= self::getJudgeReportHeaderHtml($eventPostID, $judgeName, $judgeNo + 1);
                $html .= self::getJudgeReportHtml($eventPostID, $judgeName, $judgeNo + 1);
                $html .= "</table>";
            }
        }
        $html .= "</div>";

        return $html;
    }

    private static function getJudgeReportHeaderHtml($eventPostID, $judgeName, $judgeNo)
    {
        $eventMetaData = EventProperties::getEventMetaData($eventPostID);
        $generalComment = GeneralComment::loadFromDB($eventPostID, $judgeNo);
        $html = "   <thead class='header-wrapper'>
                      <tr class='header-row'>
                        <th>
                          <ul class='show-data-header'>
                            <li>Show: " . $eventMetaData['event_name'] . "</li>
                            <li>Date: " . date("d F Y", strtotime($eventMetaData['event_start_date'])) . "</li>
                            <li>Judge: <span class = 'jr-judge-name'>" . $judgeName . "</span></li>
                          </ul>
                          <div class='general-comments' data-judge_no = ".$judgeNo.">
                            <h3>General Comments</h3>
                            <div class='textarea-wrapper'>
                            <textarea style='height: 60px; font-size: 16px' name='report'>" . $generalComment->comment . "</textarea>
                            </div>
                           <a class = 'button submitGeneralComment'>Submit Changes</a>
                          </div>
                        <th>
                      </tr>
                    </thead>";

        return $html;
    }

    private static function getJudgeReportHtml($eventPostID, $judgeName, $judgeNo)
    {
        $judgesReportModel = new JudgesReportModel();
        $registrationTablesModel = new RegistrationTablesModel();
        $html = "";
        foreach ($judgesReportModel->getJudgeClassesData($eventPostID, $judgeName) as $judgeClassData) {
            if ($judgeClassData['prize'] == "Class")
                $html .= self::getClassReportHtml($eventPostID, $judgeClassData, $judgeNo);
            if ($judgeClassData['prize'] == "Section Challenge"){
                $placementsModel = new SectionPlacements($eventPostID, $judgeClassData['age'], $judgeClassData['section']);
                $registrationCount = $registrationTablesModel->getSectionRegistrationCount($eventPostID, $judgeClassData['section'], $judgeClassData['age']);
                $html .= self::getChallengeReportHtml($eventPostID, $judgeClassData, $placementsModel, $registrationCount);
            }
                
        }

        return $html;
    }

    private static function getClassReportHtml($eventPostID, $judgeClassData, $judgeNo)
    {
        $registrationTablesModel = new RegistrationTablesModel();
        $placementsModel = new ClassPlacements($eventPostID, $judgeClassData['age'], $judgeClassData['class_name']);
        //$classCommentModel = ClassComment::loadFromDB($eventPostID, $judgeClassData['class_name'], $judgeClassData['age'], $judgeNo);
        $html = "";
        $html .= "<tr class='body-row'>
                        <td style='background-color: transparent'>
                           <div class='class-report' data-class_name = '".$judgeClassData['class_name']."' data-age = ".$judgeClassData['age']." data-judge_no = ".$judgeNo.">
                            <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-class-report' placeholder='Optional class comment'>" . $judgeClassData['comment'] . "</textarea>
                            <div class='report-form'>";

        $html .= self::getReportClassDataHtml($judgeClassData, $registrationTablesModel->getClassRegistrationCount($eventPostID, $judgeClassData['class_name'], $judgeClassData['age']));

        $html .= "        <table class='class-table'>";
        foreach ($placementsModel->placements as $placement => $placementEntryID) {
            if (isset($placementEntryID))
                $html .= self::getClassReportPlacementHtml($eventPostID, $placement, $placementEntryID, $judgeNo);
        }
        $html .= ($placementsModel->noEntriesChecked()) ? "<tr><td colspan = 3>No Entries</td></tr>" : "";

        $html .=  "        </table>
                            </div>";
        $html .= "<a class = 'button submitReport'>Submit Changes</a>";

        $html .= "      </div>
                          </td>
                         </tr>";

        return $html;
    }

    private static function getReportClassDataHtml($judgeClassData, $entryCount)
    {
        $html = "                 <div class='class-details'>
                                    <ul style='list-style: none'>
                                      <li>" . $judgeClassData['section'] . " Class " . $judgeClassData['class_index'] . "</li>
                                      <li class = 'jr-classData-li'><span class = 'jr-classData-className'>" . $judgeClassData['class_name'] . "</span> <span class = 'jr-classData-age'>" . $judgeClassData['age'] . "</span></li>
                                      <li>Entries: " . $entryCount . "</li>
                                    </ul>
                                  </div>";

        return $html;
    }

    private static function getClassReportPlacementHtml($eventPostID, $placement, $placementEntryID, $judgeNo)
    {
        $html = "";
        $entry = ShowEntry::createWithEntryID($placementEntryID);
        $html .= "<tr class = 'jr-placement-tr'>";
        $html .= self::getPlacementFancierDataHtml($entry, $placement);
        $html .= self::getPlacementReportHtml($eventPostID, $entry, $placement, $judgeNo);
        $html .= "</tr>";

        return $html;
    }

    private static function getPlacementFancierDataHtml($entry, $placement)
    {
        $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
        $html = "";
        if (isset($entry)) {
            $html = "<td class='jr-placement'><span>" . $displayedPlacements[$placement] . "</span></td>
                        <td class='jr-exhibitor'>
                          <div class='exhibit-details'>
                            <div>
                              <span>" . $entry->userName . "</span>
                            </div>
                         <div>";
            //$classSelectOptions = ClassSelectOptions::getClassSelectOptionsHtml($entry->sectionName, $this->locationID, $entry->varietyName);
            //$html .= (!$this->standardClasses->isStandardClass($entry->className, $entry->sectionName)) ? "<select class='classSelect-judgesReports' id = '" . $entry->penNumber . "&-&varietySelect' autocomplete='off'><option value=''>Select a Variety</option>" . $classSelectOptions . "</select>" : "";
            $html .=      "</div>
                       </div>
                      </td>";
        }

        return $html;
    }

    private static function getPlacementReportHtml($eventPostID, $entry, $placement, $judgeNo)
    {
        $placementReport = PlacementReport::loadFromDB($eventPostID, $entry->className, $entry->age, $judgeNo, $placement);
        //TODO: Enum
        $buckChecked = ($placementReport->gender == "Buck") ? "checked" : "";
        $doeChecked = ($placementReport->gender == "Doe") ? "checked" : "";
        $html = "<td class = placement-report data-placement = ".$placement.">";
        $html .= "<div style='display: flex'>
                   <div style='display: flex; flex-direction: column; justify-content: space-around;'>
                    <div style='display: flex; align-items: center; width: 62px;'>
                     <input type='radio' class='buck' id = '".$entry->className."-".$placement."-".$entry->age."-B' name = 'gender-radio-".$entry->className."-".$placement."-".$entry->age."' value='B' " . $buckChecked . ">
                     <label for='".$entry->className."-".$placement."-".$entry->age."-B'>B</label>
                    </div>
                   <div style='display: flex; align-items: center;'>
                    <input type='radio' class='doe'id = '".$entry->className."-".$placement."-".$entry->age."-D' name = 'gender-radio-".$entry->className."-".$placement."-".$entry->age."' value='D' " . $doeChecked . ">
                    <label for='".$entry->className."-".$placement."-".$entry->age."-D'>D</label>
                   </div>
                  </div>
                  <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-report'>" . $placementReport->comment . "</textarea>";
        $html .= "</td>";

        return $html;
    }

    private static function getChallengeReportHtml($eventPostID, $judgeClassData, $placementsModel, $registrationCount)
    {
        $html = "<tr class='body-row'>
                  <td style='background-color: transparent'>
                     <div class='section-report'>
                      <div class='report-form'>";

        $html .= self::getReportClassDataHtml($judgeClassData, $registrationCount);
        $html .= "        <table class='section-table'>";

        foreach ($placementsModel->placements as $placement => $placementEntryID) {
            if (isset($placementEntryID))
                $html .= self::getChallengeReportPlacementHtml($placement, $placementEntryID);
        }
        $html .= ($placementsModel->noEntriesChecked()) ? "<tr><td colspan = 3>No Entries</td></tr>" : "";

        $html .=  "        </table>
                      </div>";

        $html .= "     </div>
                    </td>
                   </tr>";

        return $html;
    }

    private static function getChallengeReportPlacementHtml($placement, $placementEntryID)
    {
        $html = "";
        if ($placementEntryID != NULL) {
            $entry = ShowEntry::createWithEntryID($placementEntryID);
            $html .= "<tr class = 'jr-placement-tr' id = '" . $entry->className . "&-&" . $placement . "'>";
            $html .= self::getChallengePlacementFancierDataHtml($entry, $placement);
            $html .= "<td></td>";
            $html .= "</tr>";
        }

        return $html;
    }

    private static function getChallengePlacementFancierDataHtml($entry, $placement)
    {
        $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
        $html = "";
        if (isset($entry)) {
            $html = "<td class='jr-placement'><span>" . $displayedPlacements[$placement] . "</span></td>
                   <td class='jr-exhibitor'>
                    <div class='exhibit-details'>
                     <div>
                      <span>" . $entry->userName . "</span>
                     </div>
                     <div>
                      <span>" . $entry->varietyName . "</span>
                     </div>
                    </div>
                   </td>";
        }

        return $html;
    }
}
