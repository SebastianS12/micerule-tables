<?php

class LocationSecretariesService{
    public static function saveLocationSecretaryNames(int $locationPostID, array $locationSecretariesFormData): void
    {
        $locationID = LocationHelper::getIDFromLocationPostID($locationPostID);
        $locationSecretaries = array();
        foreach($locationSecretariesFormData as $secretaryName){
            if($secretaryName != ""){
                $locationSecretaries[] = $secretaryName;
            }
        }
        update_post_meta($locationID, 'micerule_location_secretaries',$locationSecretaries);
    }

    public static function getLocationSecretaries(int $locationID): array
    {
        $locationSecretaries = get_post_meta($locationID, 'micerule_location_secretaries',true);
        return ($locationSecretaries != "") ? $locationSecretaries : array();
    }
}