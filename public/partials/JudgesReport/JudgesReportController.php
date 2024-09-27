<?php

class JudgesReportController
{
    private JudgesReportService $judgesReportService;
    
    public function __construct(JudgesReportService $judgesReportService)
    {
        $this->judgesReportService = $judgesReportService;
    }

    public function prepareReportData(int $eventPostID): array{
        return $this->judgesReportService->prepareReportData($eventPostID);
    }

    public static function submitPlacementReport($eventPostID, $className, $age, $judgeNo, $placement, $gender, $comment)
    {
        $placementReport = PlacementReport::create($eventPostID, $className, $age, $judgeNo, $placement, $gender, $comment);
        $placementReport->saveToDB();
    }

    public function submit($submitType)
    {
        if ($submitType == "classReport") {
            $this->submitClassReport();
        }
        if ($submitType == "generalComment") {
            self::submitGeneralComment();
        }
    }

    private function submitClassReport()
    {
        $commentID = isset($_POST['commentID']) && $_POST['commentID'] !== '' ? intval($_POST['commentID']) : null;
        $indexID = intval($_POST['indexID']);
        $comment = $_POST['classComment'];
        $placementReports = json_decode(html_entity_decode(stripslashes($_POST['placementReportData'])));

        $this->judgesReportService->submitClassReport($commentID, $comment, $indexID);
        $this->judgesReportService->submitPlacementReports($placementReports, $indexID);
    }

    private static function submitClassComment($classReportData)
    {
        // $classComment = $_POST['classComment'];
        // $classComment = ClassComment::create($eventPostID, $classReportData->className, $classReportData->age, $classReportData->judgeNo, $classComment);
        // $classComment->saveToDB();
    }

    // private function submitPlacementReports(array $placementReports, int $indexID)
    // {
    //     foreach ($placementReports as $placementReport) {
    //         $gender = ($placementReport->buckChecked == "true") ? "Buck" : "Doe";
    //         $placementReport = PlacementReport::create($classReportData->className, $classReportData->age, $classReportData->judgeNo, $placementReport->placement, $gender, $placementReport->comment);
    //         $placementReport->saveToDB();
    //     }
    // }

    private function submitGeneralComment()
    {
        $commentID = isset($_POST['commentID']) && $_POST['commentID'] !== '' ? intval($_POST['commentID']) : null;
        $judgeNo = intval($_POST['judgeNo']);
        $comment = $_POST['comment'];

        $this->judgesReportService->submitGeneralComment($commentID, $judgeNo, $comment);
    }
}
