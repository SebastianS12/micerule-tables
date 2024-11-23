<?php

class ShowReportPostController{
    public static function prepareViewModel(int $locationID, int $eventPostID): ShowReportPostViewModel
    {
        $showReportPostService = new ShowReportPostService();
        return $showReportPostService->prepareViewModel($locationID, $eventPostID);
    }

    public function createPost(ShowReportPostViewModel $viewModel): WP_REST_Response
    {
        $post = array(
        'post_title' => $viewModel->postTitle,
        'post_content' => html_entity_decode(ShowReportPostView::render($viewModel)),
        'post_status' => 'draft',
        'post_type' => array(1),
        );

        return new WP_REST_Response($post);
    }
}