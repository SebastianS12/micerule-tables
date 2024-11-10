<?php

class ShowReportPostController{
    public static function prepareViewModel(int $locationID, int $eventPostID): ShowReportPostViewModel
    {
        $showReportPostService = new ShowReportPostService();
        return $showReportPostService->prepareViewModel($locationID, $eventPostID);
    }

    public static function createPost(ShowReportPostViewModel $viewModel): array
    {
        $post = array(
        'post_title' => $viewModel->postTitle,
        'post_content' => html_entity_decode(ShowReportPostView::render($viewModel)),
        'post_status' => 'draft',
        'post_type' => array(1),
        );

        return $post;
    }
}