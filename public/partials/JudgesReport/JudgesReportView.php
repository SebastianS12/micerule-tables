<?php

class JudgesReportView
{
    public static function getHtml($eventPostID)
    {
        $user = wp_get_current_user();
        $userName = $user->display_name;

        $html = "<div class = 'judgesReport content' style = 'display: none'>";

        $judgesReportController = new JudgesReportController(new JudgesReportService(new JudgesReportRepository($eventPostID)));
        $data = $judgesReportController->prepareReportData($eventPostID);

        foreach ($data['judge_data'] as $judgeName => $judgeCommentData) {
            if ($userName == $judgeName || current_user_can('administrator')) {
                $html .= "<table>";
                $html .= self::getJudgeReportHeaderHtml($judgeCommentData['general'], $data['eventMetaData']);
                $html .= self::getJudgeReportHtml($judgeCommentData['class'], $data['placement_reports']);
                $html .= "</table>";
            }
        }
        $html .= "<table>";
        $html .= self::getJudgeReportHtml($data['junior'], $data['placement_reports']);
        $html .= "</table>";

        $html .= "</div>";

        return $html;
    }

    private static function getJudgeReportHeaderHtml(array|null $judgeCommentData, array|null $eventMetaData)
    {
        $html = "   <thead class='header-wrapper'>
                      <tr class='header-row'>
                        <th>
                          <ul class='show-data-header'>
                            <li>Show: " . $eventMetaData['event_name'] . "</li>
                            <li>Date: " . date("d F Y", strtotime($eventMetaData['event_start_date'])) . "</li>
                            <li>Judge: <span class = 'jr-judge-name'>" . $judgeCommentData['judge_name'] . "</span></li>
                          </ul>
                          <div class='general-comments' data-comment-id = '".$judgeCommentData['comment_id']."' data-judge-no = ".$judgeCommentData['judge_no'].">
                            <h3>General Comments</h3>
                            <div class='textarea-wrapper'>
                            <textarea style='height: 60px; font-size: 16px' name='report'>" . $judgeCommentData['comment'] . "</textarea>
                            </div>
                           <a class = 'button submitGeneralComment'>Submit Changes</a>
                          </div>
                        <th>
                      </tr>
                    </thead>";

        return $html;
    }

    private static function getJudgeReportHtml($judgeClassData, $placementReports)
    {
        $html = "";
        foreach ($judgeClassData as $classData) {
            if ($classData['prize'] == "Class")
                $html .= self::getClassReportHtml($classData, $placementReports['class']);
            if ($classData['prize'] == "Section Challenge"){
                $html .= self::getChallengeReportHtml($classData, $placementReports['section']);
            }
            if($classData['prize'] == "Junior"){
                $html .= self::getClassReportHtml($classData, $placementReports['junior']);
            }
        }

        return $html;
    }

    private static function getClassReportHtml($classData, $placementReports)
    {
        $html = "";
        $html .= "<tr class='body-row'>
                        <td style='background-color: transparent'>
                           <div class='class-report' data-index-id = ".$classData['index_id']." data-comment-id = ".$classData['comment_id'].">
                            <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-class-report' placeholder='Optional class comment'>" . $classData['comment'] . "</textarea>
                            <div class='report-form'>";

        $html .= self::getReportClassDataHtml($classData);

        $html .= "        <table class='class-table'>";
        if(isset($placementReports[$classData['class_index']])){
            foreach($placementReports[$classData['class_index']] as $placementReport){
                $html .= self::getClassReportPlacementHtml($placementReport);
            }
        }else{
            $html .= "<tr><td colspan = 3>No Entries</td></tr>";
        }

        $html .=  "        </table>
                            </div>";
        $html .= "<a class = 'button submitReport'>Submit Changes</a>";

        $html .= "      </div>
                          </td>
                         </tr>";

        return $html;
    }

    private static function getReportClassDataHtml($judgeClassData)
    {
        $html = "                 <div class='class-details'>
                                    <ul style='list-style: none'>
                                      <li>" . $judgeClassData['section'] . " Class " . $judgeClassData['class_index'] . "</li>
                                      <li class = 'jr-classData-li'><span class = 'jr-classData-className'>" . $judgeClassData['class_name'] . "</span> <span class = 'jr-classData-age'>" . $judgeClassData['age'] . "</span></li>
                                      <li>Entries: " . $judgeClassData['entry_count'] . "</li>
                                    </ul>
                                  </div>";

        return $html;
    }

    private static function getClassReportPlacementHtml($placementReport)
    {
        $html = "";
        $html .= "<tr class = 'jr-placement-tr'>";
        $html .= self::getPlacementFancierDataHtml($placementReport);
        $html .= self::getPlacementReportHtml($placementReport);
        $html .= "</tr>";

        return $html;
    }

    private static function getPlacementFancierDataHtml($placementReport)
    {
        $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
        $html = "<td class='jr-placement'><span>" . $placementReport['placement'] . "</span></td>
                    <td class='jr-exhibitor'>
                        <div class='exhibit-details'>
                        <div>
                            <span>" . $placementReport['user_name']. "</span>
                        </div>
                        <div>";
        //$classSelectOptions = ClassSelectOptions::getClassSelectOptionsHtml($entry->sectionName, $this->locationID, $entry->varietyName);
        //$html .= (!$this->standardClasses->isStandardClass($entry->className, $entry->sectionName)) ? "<select class='classSelect-judgesReports' id = '" . $entry->penNumber . "&-&varietySelect' autocomplete='off'><option value=''>Select a Variety</option>" . $classSelectOptions . "</select>" : "";
        $html .=      "</div>
                    </div>
                    </td>";

        return $html;
    }

    private static function getPlacementReportHtml($placementReport)
    {
        //TODO: Enum
        $buckChecked = ($placementReport['gender'] == "Buck") ? "checked" : "";
        $doeChecked = ($placementReport['gender'] == "Doe") ? "checked" : "";
        $html = "<td class = placement-report data-placement-id = ".$placementReport['placement_id']." data-report-id = ".$placementReport['id'].">";
        $html .= "<div style='display: flex'>
                   <div style='display: flex; flex-direction: column; justify-content: space-around;'>
                    <div style='display: flex; align-items: center; width: 62px;'>
                     <input type='radio' class='buck' id = '".$placementReport['class_name']."-".$placementReport['placement']."-".$placementReport['age']."-B' name = 'gender-radio-".$placementReport['class_name']."-".$placementReport['placement']."-".$placementReport['age']."' value='B' " . $buckChecked . ">
                     <label for='".$placementReport['class_name']."-".$placementReport['placement']."-".$placementReport['age']."-B'>B</label>
                    </div>
                   <div style='display: flex; align-items: center;'>
                    <input type='radio' class='doe'id = '".$placementReport['class_name']."-".$placementReport['placement']."-".$placementReport['age']."-D' name = 'gender-radio-".$placementReport['class_name']."-".$placementReport['placement']."-".$placementReport['age']."' value='D' " . $doeChecked . ">
                    <label for='".$placementReport['class_name']."-".$placementReport['placement']."-".$placementReport['age']."-D'>D</label>
                   </div>
                  </div>
                  <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-report'>" . $placementReport['comment'] . "</textarea>";
        $html .= "</td>";

        return $html;
    }

    private static function getChallengeReportHtml($sectionData, $placementReports)
    {
        $html = "<tr class='body-row'>
                  <td style='background-color: transparent'>
                     <div class='section-report'>
                      <div class='report-form'>";

        $html .= self::getReportClassDataHtml($sectionData);
        $html .= "        <table class='section-table'>";

        if(isset($placementReports[$sectionData['class_index']])){
            foreach($placementReports[$sectionData['class_index']] as $placementReport){
                $html .= self::getChallengeReportPlacementHtml($placementReport);
            }
        }else{
            $html .= "<tr><td colspan = 3>No Entries</td></tr>";
        }

        $html .=  "        </table>
                      </div>";

        $html .= "     </div>
                    </td>
                   </tr>";

        return $html;
    }

    private static function getChallengeReportPlacementHtml($placementReport)
    {
        $html = "";
        $html .= "<tr class = 'jr-placement-tr' id = '" . $placementReport['class_name'] . "&-&" . $placementReport['placement'] . "'>";
        $html .= self::getChallengePlacementFancierDataHtml($placementReport);
        $html .= "<td></td>";
        $html .= "</tr>";

        return $html;
    }

    private static function getChallengePlacementFancierDataHtml($placementReport)
    {
        $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
        $html = "<td class='jr-placement'><span>" . $displayedPlacements[$placementReport['placement']] . "</span></td>
                <td class='jr-exhibitor'>
                <div class='exhibit-details'>
                    <div>
                    <span>" .$placementReport['user_name'] . "</span>
                    </div>
                    <div>
                    <span>" . $placementReport['variety_name'] . "</span>
                    </div>
                </div>
                </td>";

        return $html;
    }
}
