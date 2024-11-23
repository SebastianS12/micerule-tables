<?php

class AdminTabsService{
    public function prepareViewModel(int $eventPostID, ShowOptionsService $showOptionsService, JudgeDataLoader $judgeDataLoader, ): AdminTabsViewModel
    {
        $showOptions = $showOptionsService->getShowOptions(new ShowOptionsRepository(), LocationHelper::getIDFromEventPostID($eventPostID));
        $locationSecretaries = LocationSecretariesService::getLocationSecretaries(LocationHelper::getIDFromEventPostID($eventPostID));
        $judgeDataLoader->load(new JudgesRepository($eventPostID));

        $canViewShowReportTab = ($showOptions->allow_online_registrations && is_user_logged_in()) && ((in_array(wp_get_current_user()->display_name, $locationSecretaries) || PermissionHelper::canViewShowReport(wp_get_current_user()->display_name, $judgeDataLoader->getCollection())) || current_user_can('administrator'));
        $canViewAllTabs = (in_array(wp_get_current_user()->display_name, $locationSecretaries)) || current_user_can('administrator');
        $viewModel = new AdminTabsViewModel($eventPostID, $canViewShowReportTab, $canViewAllTabs);
        
        return $viewModel;
    }
}