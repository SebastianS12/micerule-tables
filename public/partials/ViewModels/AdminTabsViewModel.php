<?php

class AdminTabsViewModel{
    public int $eventPostID;
    public bool $canViewShowReportTab;
    public bool $canViewAllTabs;

    public function __construct(int $eventPostID, bool $canViewShowReportTab, bool $canViewAllTabs)
    {
        $this->eventPostID = $eventPostID;
        $this->canViewShowReportTab = $canViewShowReportTab;
        $this->canViewAllTabs = $canViewAllTabs;
    }
}