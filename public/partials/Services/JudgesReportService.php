<?php

class JudgesReportService{
    private JudgesReportRepository $judgesReportRepository;

    public function __construct(JudgesReportRepository $judgesReportRepository)
    {
        $this->judgesReportRepository = $judgesReportRepository;
    }

    public function prepareReportData(int $eventPostID): array{
        $judgesReportData = array();
        $judgesReportData['placement_reports']['class'] = array();
        $judgesReportData['eventMetaData'] = EventProperties::getEventMetaData($eventPostID);
        $generalCommentsData = $this->judgesReportRepository->getJudgesGeneralComments();
        foreach($generalCommentsData as $judgeCommentData){
            $judgesReportData['judge_data'][$judgeCommentData['judge_name']]['general'] = $judgeCommentData;
        }
        $judgesClassData = $this->judgesReportRepository->getJudgesClassData();
        foreach($judgesClassData as $classData){
            $judgesReportData['judge_data'][$classData['judge_name']]['class'][$classData['class_index']] = $classData;
        }
        $classPlacementReports = $this->judgesReportRepository->getClassPlacementsReports();
        foreach($classPlacementReports as $placementReport){
            $judgesReportData['placement_reports']['class'][$placementReport['class_index']][$placementReport['placement']] = $placementReport;
        }
        $sectionPlacementReports = $this->judgesReportRepository->getSectionPlacementReports();
        foreach($sectionPlacementReports as $placementReport){
            $judgesReportData['placement_reports']['section'][$placementReport['challenge_index']][$placementReport['placement']] = $placementReport;
        }

        $judgesReportData['junior'] = $this->judgesReportRepository->getJuniorData();
        $juniorPlacementReports = $this->judgesReportRepository->getJuniorPlacementReports();
        foreach($juniorPlacementReports as $placementReport){
            $judgesReportData['placement_reports']['junior'][$placementReport['class_index']][$placementReport['placement']] = $placementReport;
        }

        return $judgesReportData;
    }

    //TODO: own service, reportPostDataModel class
    public function prepareReportPostData(int $eventPostID, JudgesRepository $judgesRepository){
        $reportPostData = array();

        $reportPostData['placement_reports']['class'] = array();
        $reportPostData['eventMetaData'] = EventProperties::getEventMetaData($eventPostID);
        $generalCommentsData = $this->judgesReportRepository->getJudgesGeneralComments();
        foreach($generalCommentsData as $judgeCommentData){
            $reportPostData['judge_data'][$judgeCommentData['judge_name']]['general'] = $judgeCommentData;
        }
        $judgesClassData = $this->judgesReportRepository->getJudgesClassData();
        foreach($judgesClassData as $classData){
            $reportPostData['judge_data'][$classData['judge_name']]['class'][$classData['section']][$classData['class_index']] = $classData;
        }
        $classPlacementReports = $this->judgesReportRepository->getClassPlacementsReports();
        foreach($classPlacementReports as $placementReport){
            $reportPostData['placement_reports']['class'][$placementReport['class_index']][$placementReport['placement']] = $placementReport;
        }
        $sectionPlacementReports = $this->judgesReportRepository->getSectionPlacementReports();
        foreach($sectionPlacementReports as $placementReport){
            $reportPostData['placement_reports']['section'][$placementReport['challenge_index']][$placementReport['placement']] = $placementReport;
        }

        $reportPostData['junior'] = $this->judgesReportRepository->getJuniorData();
        $juniorPlacementReports = $this->judgesReportRepository->getJuniorPlacementReports();
        foreach($juniorPlacementReports as $placementReport){
            $reportPostData['placement_reports']['junior'][$placementReport['class_index']][$placementReport['placement']] = $placementReport;
        }

        $reportPostData['grand_challenge'] = $this->judgesReportRepository->getGrandChallengeData();

        $judgesData = $judgesRepository->getAll($eventPostID);
        foreach($judgesData as $judgeSectionData){
            $reportPostData['judge_data'][$judgeSectionData['judge_name']]['sections'][] = $judgeSectionData['section'];
        }

        return $reportPostData;
    }

    public function submitClassReport(int|null $commentID, string|null $comment, int $indexID){
        $this->judgesReportRepository->submitClassComment($commentID, $comment, $indexID);
    }

    public function submitPlacementReports(array|null $placementReports, int $indexID){
        foreach($placementReports as $placementReport){
            $reportID = isset($placementReport->id) && $placementReport->id !== '' ? intval($placementReport->id) : null;
            $placementID = isset($placementReport->placementID) && $placementReport->placementID !== '' ? intval($placementReport->placementID) : null;
            $gender = ($placementReport->buckChecked) ? "Buck" : "Doe";
            
            $this->judgesReportRepository->submitPlacementReport($reportID, $indexID, $placementID, $gender, $placementReport->comment);
        }
    }

    public function submitGeneralComment(int|null $commentID, int $judgeNo, string|null $comment){
        $this->judgesReportRepository->submitGeneralComment($commentID, $judgeNo, $comment);
    }
}