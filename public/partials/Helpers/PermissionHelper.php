<?php

class PermissionHelper{
    public static function canEditShowClasses(int $locationID): bool
    {
        return ((is_user_logged_in() && (self::isLocSecOrAdmin($locationID))));
    }

    public static function isLocSecOrAdmin(int $locationID): bool
    {
        $locationSecretaryNames = LocationSecretariesService::getLocationSecretaries($locationID);
        return (in_array(wp_get_current_user()->display_name, $locationSecretaryNames) || current_user_can('administrator'));
    }

    public static function canViewShowReport(string $judgeName, Collection $judges): bool
    {
        return !empty(array_filter($judges->items, fn(JudgeModel $judge) => $judge->judge_name === $judgeName));
    }
}