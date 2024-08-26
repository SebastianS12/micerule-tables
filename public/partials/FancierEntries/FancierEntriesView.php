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
            //junior
            $registrationTablesModel = new RegistrationTablesModel();
            $juniorRegistrationCount = $registrationTablesModel->getUserJuniorRegistrationCount($eventPostID, $userName);
            if($juniorRegistrationCount > 0){
                $juniorClassModel = new ShowClassModel($eventPostID, "Junior", "AA");    
                $html .= "<p class='single-entry'><span>".$juniorClassModel->index." ".$juniorClassModel->name." ".$juniorClassModel->age.": </span><span>".$juniorRegistrationCount."</span></p>";
            }

            $html .= "<p class='single-entry'><span>Total Entries:</span><span>".FancierEntriesController::getFancierRegistrationCount($eventPostID, $userName) + $juniorRegistrationCount."</span></p>";
            $prizeMoney = 0;
            $html .= "<p class='single-entry'><span>Prize Money:</span><span>£"./*number_format((float)$this->userPrizeData->getUserPrizeMoney($userName, $eventOptionalSettings->prizeMoney['firstPrize'], $eventOptionalSettings->prizeMoney['secondPrize'], $eventOptionalSettings->prizeMoney['thirdPrize']), 2, '.', '')*/$prizeMoney."</span></p>";
            $html .= "</div>";
        }
        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }
}