<?php

class EntrySummaryController{
    public static function getEntrySummaryData($eventPostID){
        $registrationTablesModel = new RegistrationTablesModel();
        $entrySummaryModel = new EntrySummaryModel();
        $entrySummaryData = array();
        foreach($registrationTablesModel->getFancierNames($eventPostID) as $fancierName){
            $entrySummaryData[$fancierName] = $entrySummaryModel->getFancierEntries($eventPostID, $fancierName);
        }

        return $entrySummaryData;
    }

    public static function getRegistrationFee($eventPostID, $fancierName){
        $registrationTablesModel = new RegistrationTablesModel();
        $showOptionsModel = new ShowOptionsModel();

        return $registrationTablesModel->getFancierRegistrationCount($eventPostID, $fancierName) * $showOptionsModel->getRegistrationFee(EventProperties::getEventLocationID($eventPostID));
    }

    public static function setAllAbsent($eventPostID, $absent, $userName){
        $entrySummaryModel = new EntrySummaryModel();
        foreach($entrySummaryModel->getFancierEntries($eventPostID, $userName) as $fancierEntry){
            $showEntry = ShowEntry::createWithPenNumber($eventPostID, $fancierEntry['pen_number']);
            $showEntry->editAbsent($absent);
        }
    }
}