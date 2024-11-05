<?php

class FancierEntriesView{
    public static function getFancierEntriesHtml(int $eventPostID){
        $fancierEntriesService = new FancierEntriesService($eventPostID, LocationHelper::getIDFromEventPostID($eventPostID));
        $viewModel = $fancierEntriesService->prepareViewModel();
        $html = "<div class = 'fancierEntries content'>";
        $html .= "<div class = 'showStats'>";
        foreach($viewModel->fancierEntries as $userName => $fancierRegistrations){
            $html .= "<div class = 'fancier-entries'>";
            $html .= "<h3 class = 'fancier-name'>".$userName."</h3>";
            foreach($fancierRegistrations['classData'] as $classIndex => $registration){
                $html .= "<p class='single-entry'><span>".$classIndex." ".$registration['className']." ".$registration['age'].": </span><span>".$registration['registrationCount']."</span></p>";
            }

            $html .= "<p class='single-entry'><span>Total Entries:</span><span>".$fancierRegistrations['totalRegistrationCount']."</span></p>";
            $prizeMoney = 0;
            $html .= "<p class='single-entry'><span>Prize Money:</span><span>£"./*number_format((float)$this->userPrizeData->getUserPrizeMoney($userName, $eventOptionalSettings->prizeMoney['firstPrize'], $eventOptionalSettings->prizeMoney['secondPrize'], $eventOptionalSettings->prizeMoney['thirdPrize']), 2, '.', '')*/$prizeMoney."</span></p>";
            $html .= "</div>";
        }
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}