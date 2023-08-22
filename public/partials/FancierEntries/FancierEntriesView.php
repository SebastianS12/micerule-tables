<?php

class FancierEntriesView{
    public static function getFancierEntriesHtml($eventPostID){
        $fancierEntries = FancierEntriesController::getFancierEntries($eventPostID);
        $html = "<div class = 'fancierEntries content'>";
        $html .= "<div class = 'showStats'>";
        foreach($fancierEntries as $userName => $fancierRegistrations){
            $html .= "<div class = 'fancier-entries'>";
            $html .= "<h3 class = 'fancier-name'>".$userName."</h3>";
            foreach($fancierRegistrations as $classRegistration){
                $html .= "<p class='single-entry'><span>".$classRegistration['class_index']." ".$classRegistration['class_name']." ".$classRegistration['age'].": </span><span>".$classRegistration['registration_count']."</span></p>";
            }
            $html .= "<p class='single-entry'><span>Total Entries:</span><span>".FancierEntriesController::getFancierRegistrationCount($eventPostID, $userName)."</span></p>";
            $prizeMoney = 0;
            $html .= "<p class='single-entry'><span>Prize Money:</span><span>£"./*number_format((float)$this->userPrizeData->getUserPrizeMoney($userName, $eventOptionalSettings->prizeMoney['firstPrize'], $eventOptionalSettings->prizeMoney['secondPrize'], $eventOptionalSettings->prizeMoney['thirdPrize']), 2, '.', '')*/$prizeMoney."</span></p>";
            $html .= "</div>";
        }
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}