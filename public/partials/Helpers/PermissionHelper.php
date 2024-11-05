<?php

class PermissionHelper{
    public static function canEditShowClasses(int $locationID){
        $locationSecretaryNames = LocationSecretariesService::getLocationSecretaries($locationID);
        return ((is_user_logged_in() && (in_array(wp_get_current_user()->display_name, $locationSecretaryNames) || current_user_can('administrator'))));
    }
}