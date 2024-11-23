<?php

class AdminTabsController{
    public function prepareViewModel(int $eventPostID): AdminTabsViewModel
    {
        $adminTabsService = new AdminTabsService();
        return $adminTabsService->prepareViewModel($eventPostID, new ShowOptionsService(), new JudgeDataLoader());
    }

    public function getViewHtml(int $eventPostID): string
    {
        return AdminTabsView::render($this->prepareViewModel($eventPostID));
    }
}