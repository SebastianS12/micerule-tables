<?php

class JudgesReportController
{
    public static function submitPlacementReport($eventPostID, $className, $age, $judgeNo, $placement, $gender, $comment)
    {
        $placementReport = PlacementReport::create($eventPostID, $className, $age, $judgeNo, $placement, $gender, $comment);
        $placementReport->saveToDB();
    }

    public static function submit($eventPostID, $submitType)
    {
        if ($submitType == "classReport") {
            self::submitClassReport($eventPostID);
        }
        if ($submitType == "generalComment") {
            self::submitGeneralComment($eventPostID);
        }
    }

    private static function submitClassReport($eventPostID)
    {
        $classReportData = json_decode(html_entity_decode(stripslashes($_POST['classReportData'])));
        $placementReportData = json_decode(html_entity_decode(stripslashes($_POST['placementReportData'])));
        self::submitClassComment($eventPostID, $classReportData);
        self::submitPlacementReports($eventPostID, $classReportData, $placementReportData);
    }

    private static function submitClassComment($eventPostID, $classReportData)
    {
        $classComment = $_POST['classComment'];
        $classComment = ClassComment::create($eventPostID, $classReportData->className, $classReportData->age, $classReportData->judgeNo, $classComment);
        $classComment->saveToDB();
    }

    private static function submitPlacementReports($eventPostID, $classReportData, $placementReportData)
    {
        foreach ($placementReportData as $placementReport) {
            $gender = ($placementReport->buckChecked == "true") ? "Buck" : "Doe";
            $placementReport = PlacementReport::create($eventPostID, $classReportData->className, $classReportData->age, $classReportData->judgeNo, $placementReport->placement, $gender, $placementReport->comment);
            $placementReport->saveToDB();
        }
    }

    private static function submitGeneralComment($eventPostID)
    {
        $judgeNo = $_POST['judgeNo'];
        $comment = $_POST['comment'];
        $generalComment = GeneralComment::create($eventPostID, $judgeNo, $comment);
        $generalComment->saveToDB();
    }
}
