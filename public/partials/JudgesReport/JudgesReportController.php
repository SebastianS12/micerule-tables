<?php

class JudgesReportController
{
    private JudgesReportService $judgesReportService;
    
    public function __construct(JudgesReportService $judgesReportService)
    {
        $this->judgesReportService = $judgesReportService;
    }

    public function submit(string $submitType, int $eventPostID)
    {
        if ($submitType == "classReport") {
            $this->submitClassReport($eventPostID);
        }
        if ($submitType == "generalComment") {
            self::submitGeneralComment($eventPostID);
        }
    }

    private function submitClassReport(int $eventPostID)
    {
        $commentID = isset($_POST['commentID']) && $_POST['commentID'] !== '' ? intval($_POST['commentID']) : null;
        $indexID = intval($_POST['indexID']);
        $comment = $_POST['classComment'];
        $placementReports = json_decode(html_entity_decode(stripslashes($_POST['placementReportData'])));

        $this->judgesReportService->submitClassComment($commentID, $eventPostID, $comment, $indexID, new ClassCommentsRepository($eventPostID));
        $this->judgesReportService->submitPlacementReports($placementReports, $eventPostID, $indexID, new PlacementReportsRepository($eventPostID));
    }

    private function submitGeneralComment(int $eventPostID)
    {
        $commentID = isset($_POST['commentID']) && $_POST['commentID'] !== '' ? intval($_POST['commentID']) : null;
        $judgeID = intval($_POST['judgeID']);
        $comment = $_POST['comment'];

        $this->judgesReportService->submitGeneralComment($commentID, $judgeID, $comment, new GeneralCommentRepository($eventPostID));
    }
}
