<?php

class EntrySummaryController{
    public static function setAllAbsent(bool $absent, string $userName): WP_REST_Response
    {
        $eventPostID = EventHelper::getEventPostID();
        $entrySummaryService = new EntrySummaryService($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $entrySummaryService->setAllAbsent($userName, $absent);

        return new WP_REST_Response("");
    }
}