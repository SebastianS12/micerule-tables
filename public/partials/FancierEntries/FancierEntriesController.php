<?php 

class FancierEntriesController{
    public static function getFancierEntries($eventPostID){
        $registrationTablesModel = new RegistrationTablesModel();
        $fancierEntries = array();
        foreach($registrationTablesModel->getFancierNames($eventPostID) as $fancierName){
            $fancierEntries[$fancierName] = $registrationTablesModel->getUserRegistrations($eventPostID, $fancierName);
        }

        return $fancierEntries;
    }

    public static function getFancierRegistrationCount($eventPostID, $userName){
        $registrationTablesModel = new RegistrationTablesModel();
        return $registrationTablesModel->getFancierRegistrationCount($eventPostID, $userName);
    }
}