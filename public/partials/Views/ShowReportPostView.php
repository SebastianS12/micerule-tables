<?php

class ShowReportPostView{
    public static function render(ShowReportPostViewModel $viewModel): string
    {
        $html = "";
        foreach ($viewModel->judges as $judgeName => $judgeReportData) {
            $html .= self::getJudgeReportHeader($judgeName, $judgeReportData['comment']);

            foreach($judgeReportData['sections'] as $section){
                $html .= self::getJudgeSectionReportHtml($section, $viewModel->classReports[$judgeName][$section], $viewModel->challengeReports[$judgeName][$section]);
            }
        }

        $html .= "<h1>Grand Challenge</h1><div class = 'section-placement-reports' style= 'flex-direction: column'>";
        $html .= self::getChallengeReportsHtml($viewModel->grandChallengeReport);
        $html .= "</div>";
        $html .= "<h1>Junior</h1><div class = 'section-placement-reports' style= 'flex-direction: column'>";
        $html .= self::getClassReportsHtml($viewModel->juniorReport);
        $html .= "</div>";

        return $html;
    }

    private static function getJudgeReportHeader(string $judgeName, string $comment): string
    {
        $html = "<H2 class='judge-header'>".$judgeName."</h2>";

        $html .= "<h4 class = 'p1'>General Comments</h4>";
        $html .= "<div>".$comment."</div>";

        return $html;
    }

    private static function getJudgeSectionReportHtml(string $sectionName, array $classReports, array $challengeReports):string
    {
        $html = "<h1>".Section::from($sectionName)->getDisplayStringPlural()."</h1>";
        $html .= "<div class = 'section-placement-reports'>";
        $html .= "<div class = 'class-reports' style = 'width: 65.667%'>";
        $html .= self::getClassReportsHtml($classReports);
        $html .= "</div>";

        $html .= "<div class = 'section-challenge-reports' style = 'width: 31.3333%'>";
        $html .= self::getChallengeReportsHtml($challengeReports);
        $html .= "</div>";

        $html .= "</div>";

        return $html;
    }

    private static function getClassReportsHtml(array $sectionClassReports): string
    {
        $html = "";
        foreach($sectionClassReports as $classReport){
            $html .= self::getReportDataItemHtml($classReport);
        }

        return $html;
    }

    private static function getReportDataItemHtml(array $classReport): string
    {
        $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');
    
        $html = "<h4>".$classReport['className']." ".$classReport['age']." - ".$classReport['entryCount']."</h4>";
        $html .= "<div class = 'class-comments'>".$classReport['comment']."</div>";
        foreach ($classReport['placements'] as $placementReport) {
            $displayedGender = ($placementReport['gender'] != "" && $placementReport['gender'] == "Buck") ? "B" : "D";
            $displayedVariety = ($classReport['className'] != $placementReport['varietyName']) ? $placementReport['varietyName'] : "";
            $html .= "<div>".$displayedPlacements[$placementReport['placement']]." ".FancierNameFormatter::getShowReportFancierName($placementReport['userName'])." ".$displayedVariety." ".$displayedGender." ".$placementReport['comment']."</div>";
        }
    
        return $html;
    }

    private static function getChallengeReportsHtml(array $challengeReports): string
    {
        $html = "";
        foreach($challengeReports as $challengeReport){
            $html .= self::getChallengeReportDataItemHtml($challengeReport);
        }

        return $html;
    }

    private static function getChallengeReportDataItemHtml(array $challengeReport): string
    {
        $displayedPlacements = array('1' => '1st', '2' => '2nd', '3' => '3rd');

        $html = "<h4>".$challengeReport['challengeName']." ".$challengeReport['age']." - ".$challengeReport['entryCount']."</h4>";
        foreach ($challengeReport['placements'] as $placementReport) {
            $html .= "<div>".$displayedPlacements[$placementReport['placement']]." ".FancierNameFormatter::getShowReportFancierName($placementReport['userName'])." ".$challengeReport['varietyName']."</div>";
        }

        return $html;
    }
}