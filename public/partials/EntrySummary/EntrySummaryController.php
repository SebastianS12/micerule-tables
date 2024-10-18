<?php

class EntrySummaryController{
    public static function setAllAbsent($eventPostID, bool $absent, string $userName){
        $entrySummaryService = new EntrySummaryService($eventPostID, EventProperties::getEventLocationID($eventPostID));
        $entrySummaryService->setAllAbsent($userName, $absent);
    }
}