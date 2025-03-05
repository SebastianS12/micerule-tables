<?php

class ShowReportPostController{
    public static function prepareViewModel(int $locationID, int $eventPostID): ShowReportPostViewModel
    {
        $showReportPostService = new ShowReportPostService();
        return $showReportPostService->prepareViewModel($locationID, $eventPostID);
    }

    public function createPost(int $eventPostID): WP_REST_Response
    {
        $showReportPostService = new ShowReportPostService();
        $post = $showReportPostService->createPost(LocationHelper::getIDFromEventPostID($eventPostID), $eventPostID);
        $postLink = $showReportPostService->insertPost($post, $eventPostID);

        return new WP_REST_Response($postLink);
    }
}