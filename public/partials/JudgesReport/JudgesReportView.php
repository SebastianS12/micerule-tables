<?php

class JudgesReportView
{
    public static function getHtml($eventPostID)
    {
        $user = wp_get_current_user();
        $userName = $user->display_name;

        $judgesReportService = new JudgesReportService();
        $viewModel = $judgesReportService->prepareViewModel($eventPostID, EventProperties::getEventLocationID($eventPostID));

        $html = "<div class = 'judgesReport content' style = 'display: none'>";

        foreach($viewModel->judgeGeneral as $judgeData){
            if($viewModel->userName == $judgeData['judgeName'] || $viewModel->canAdmin){
                $html .= "<table>";
                $html .= self::getJudgeReportHeaderHtml($judgeData, $viewModel->showName, $viewModel->date);

                foreach($judgeData['sections'] as $section){
                    foreach($viewModel->classReports[$judgeData['judgeName']][$section] as $classReportData){
                        $html .= self::getClassReportHtml($classReportData);
                    }
                    foreach($viewModel->challengeReports[$judgeData['judgeName']][$section] as $challengeReportData){
                        $html .= self::getChallengeReportHtml($challengeReportData);
                    }
                }
                $html .= "</table>";
            }
        }

        foreach($viewModel->optionalClassReports as $optionalClassReportData){
            $html .= self::getClassReportHtml($optionalClassReportData);
        }

        // $judgesReportController = new JudgesReportController(new JudgesReportService(new JudgesReportRepository($eventPostID)));
        // $data = $judgesReportController->prepareReportData($eventPostID);

        // foreach ($data['judge_data'] as $judgeName => $judgeCommentData) {
        //     if ($userName == $judgeName || current_user_can('administrator')) {
        //         $html .= "<table>";
        //         $html .= self::getJudgeReportHeaderHtml($judgeCommentData['general'], $data['eventMetaData']);
        //         $html .= self::getJudgeReportHtml($judgeCommentData['class'], $data['placement_reports']);
        //         $html .= "</table>";
        //     }
        // }
        // $html .= "<table>";
        // $html .= self::getJudgeReportHtml($data['junior'], $data['placement_reports']);
        // $html .= "</table>";

        $html .= "</div>";

        return $html;
    }

    private static function getJudgeReportHeaderHtml(array $judgeData, string $showName, string $date)
    {
        $html = "   <thead class='header-wrapper'>
                      <tr class='header-row'>
                        <th>
                          <ul class='show-data-header'>
                            <li>Show: " . $showName . "</li>
                            <li>Date: " . $date . "</li>
                            <li>Judge: <span class = 'jr-judge-name'>" . $judgeData['judgeName'] . "</span></li>
                          </ul>
                          <div class='general-comments' data-comment-id = '".$judgeData['commentID']."' data-judge-id = ".$judgeData['judgeID'].">
                            <h3>General Comments</h3>
                            <div class='textarea-wrapper'>
                            <textarea style='height: 60px; font-size: 16px' name='report'>" . $judgeData['comment'] . "</textarea>
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

    private static function getClassReportHtml(array $classData)
    {
        $html = "";
        $html .= "<tr class='body-row'>
                        <td style='background-color: transparent'>
                           <div class='class-report' data-index-id = ".$classData['indexID']." data-comment-id = ".$classData['commentID'].">
                            <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-class-report' placeholder='Optional class comment'>" . $classData['comment'] . "</textarea>
                            <div class='report-form'>";

        $html .= self::getReportClassDataHtml($classData);

        $html .= "        <table class='class-table'>";
        if(count($classData['placements']) > 0){
            foreach($classData['placements'] as $placementReport){
                $html .= self::getClassReportPlacementHtml($classData, $placementReport);
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

    private static function getReportClassDataHtml(array $reportClassData)
    {
        $html = "                 <div class='class-details'>
                                    <ul style='list-style: none'>
                                      <li>" . $reportClassData['section'] . " Class " . $reportClassData['index'] . "</li>
                                      <li class = 'jr-classData-li'><span class = 'jr-classData-className'>" . $reportClassData['className'] . "</span> <span class = 'jr-classData-age'>" . $reportClassData['age'] . "</span></li>
                                      <li>Entries: " . $reportClassData['entryCount'] . "</li>
                                    </ul>
                                  </div>";

        return $html;
    }

    private static function getClassReportPlacementHtml(array $reportClassData, array $reportPlacementData)
    {
        $html = "";
        $html .= "<tr class = 'jr-placement-tr'>";
        $html .= self::getPlacementFancierDataHtml($reportPlacementData);
        $html .= self::getPlacementReportHtml($reportClassData, $reportPlacementData);
        $html .= "</tr>";

        return $html;
    }

    private static function getPlacementFancierDataHtml(array $reportPlacementData)
    {
        $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
        $html = "<td class='jr-placement'><span>" . $reportPlacementData['placement'] . "</span></td>
                    <td class='jr-exhibitor'>
                        <div class='exhibit-details'>
                        <div>
                            <span>" . $reportPlacementData['userName']. "</span>
                        </div>
                        <div>";
        //$classSelectOptions = ClassSelectOptions::getClassSelectOptionsHtml($entry->sectionName, $this->locationID, $entry->varietyName);
        //$html .= (!$this->standardClasses->isStandardClass($entry->className, $entry->sectionName)) ? "<select class='classSelect-judgesReports' id = '" . $entry-> mber . "&-&varietySelect' autocomplete='off'><option value=''>Select a Variety</option>" . $classSelectOptions . "</select>" : "";
        $html .=      "</div>
                    </div>
                    </td>";

        return $html;
    }

    private static function getPlacementReportHtml(array $reportClassData, array $reportPlacementData)
    {
        //TODO: Enum
        $buckChecked = ($reportPlacementData['gender'] == "Buck") ? "checked" : "";
        $doeChecked = ($reportPlacementData['gender'] == "Doe") ? "checked" : "";
        $html = "<td class = placement-report data-placement-id = ".$reportPlacementData['placementID']." data-report-id = ".$reportPlacementData['reportID'].">";
        $html .= "<div style='display: flex'>
                   <div style='display: flex; flex-direction: column; justify-content: space-around;'>
                    <div style='display: flex; align-items: center; width: 62px;'>
                     <input type='radio' class='buck' id = '".$reportClassData['className']."-".$reportPlacementData['placement']."-".$reportClassData['age']."-B' name = 'gender-radio-".$reportClassData['className']."-".$reportPlacementData['placement']."-".$reportClassData['age']."' value='B' " . $buckChecked . ">
                     <label for='".$reportClassData['className']."-".$reportPlacementData['placement']."-".$reportClassData['age']."-B'>B</label>
                    </div>
                   <div style='display: flex; align-items: center;'>
                    <input type='radio' class='doe'id = '".$reportClassData['className']."-".$reportPlacementData['placement']."-".$reportClassData['age']."-D' name = 'gender-radio-".$reportClassData['className']."-".$reportPlacementData['placement']."-".$reportClassData['age']."' value='D' " . $doeChecked . ">
                    <label for='".$reportClassData['className']."-".$reportPlacementData['placement']."-".$reportClassData['age']."-D'>D</label>
                   </div>
                  </div>
                  <textarea style='height: 60px; font-size: 16px' name='report' class = 'jr-report'>" . $reportPlacementData['comment'] . "</textarea>";
        $html .= "</td>";

        return $html;
    }

    private static function getChallengeReportHtml(array $challengeReportData)
    {
        $html = "<tr class='body-row'>
                  <td style='background-color: transparent'>
                     <div class='section-report'>
                      <div class='report-form'>";

        $html .= self::getReportClassDataHtml($challengeReportData);
        $html .= "        <table class='section-table'>";

        if(count($challengeReportData['placements']) > 0){
            foreach($challengeReportData['placements'] as $placementReport){
                $html .= self::getChallengeReportPlacementHtml($challengeReportData, $placementReport);
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

    private static function getChallengeReportPlacementHtml(array $challengeReportData, array $placementReport)
    {
        $html = "";
        $html .= "<tr class = 'jr-placement-tr' id = '" . $challengeReportData['className'] . "&-&" . $placementReport['placement'] . "'>";
        $html .= self::getChallengePlacementFancierDataHtml($placementReport);
        $html .= "<td></td>";
        $html .= "</tr>";

        return $html;
    }

    private static function getChallengePlacementFancierDataHtml(array $placementReport)
    {
        $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
        $html = "<td class='jr-placement'><span>" . $displayedPlacements[$placementReport['placement']] . "</span></td>
                <td class='jr-exhibitor'>
                <div class='exhibit-details'>
                    <div>
                    <span>" .$placementReport['userName'] . "</span>
                    </div>
                    <div>
                    <span>" . $placementReport['varietyName'] . "</span>
                    </div>
                </div>
                </td>";

        return $html;
    }
}
