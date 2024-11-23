<?php

class AdminTabsController{
    public function prepareViewModel(): AdminTabsViewModel
    {
        $eventPostID = EventHelper::getEventPostID();
        $adminTabsService = new AdminTabsService();
        return $adminTabsService->prepareViewModel($eventPostID, new ShowOptionsService(), new JudgeDataLoader());
    }

    public function getViewHtml(): string
    {
        return AdminTabsView::render($this->prepareViewModel());
    }
}