<?php

class JudgesReportController
{
    public function submitClassReport(int $eventPostID, ?int $commentID, int $indexID, string $comment, array $placementReports): WP_REST_Response
    {
        $judgesReportService = new JudgesReportService();
        $judgesReportService->submitClassComment($commentID, $eventPostID, $comment, $indexID, new ClassCommentsRepository($eventPostID));
        $judgesReportService->submitPlacementReports($placementReports, $eventPostID, $indexID, new PlacementReportsRepository($eventPostID));

        return new WP_REST_Response("");
    }

    public function submitGeneralComment(int $eventPostID, ?int $commentID, int $judgeID, string $comment): WP_REST_Response
    {
        $judgesReportService = new JudgesReportService();
        $judgesReportService->submitGeneralComment($commentID, $judgeID, $comment, new GeneralCommentRepository($eventPostID));

        return new WP_REST_Response("");
    }
}
