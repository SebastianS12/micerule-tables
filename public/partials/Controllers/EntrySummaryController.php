<?php

class EntrySummaryController{
    public static function setAllAbsent(int $eventPostID, bool $absent, string $userName): WP_REST_Response
    {
        $entrySummaryService = new EntrySummaryService($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $entrySummaryService->setAllAbsent($userName, $absent);

        return new WP_REST_Response("");
    }
}