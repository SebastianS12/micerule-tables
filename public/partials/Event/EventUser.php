<?php

class EventUser {
  const memberShipRoles = array('pms_subscription_plan_6551', 'pms_subscription_plan_6549', 'pms_subscription_plan_13315', 'pms_subscription_plan_13314', 'pms_subscription_plan_13309', 'pms_subscription_plan_13305', 'pms_subscription_plan_13317', 'pms_subscription_plan_13307',
  'pms_subscription_plan_13322', 'pms_subscription_plan_13320', 'pms_subscription_plan_13319', 'pms_subscription_plan_13318', 'pms_subscription_plan_13311', 'pms_subscription_plan_13306', 'pms_subscription_plan_13313', 'pms_subscription_plan_13312');

  const juniorMemberShipRoles = array('pms_subscription_plan_13305', 'pms_subscription_plan_13305', 'pms_subscription_plan_13309', 'pms_subscription_plan_13307', 'pms_subscription_plan_13317');

  public static function isJuniorMember($userName){
    $isJuniorMember = false;
    $userObject = self::getUserObject($userName);
    if($userObject){
      $userRoles = $userObject->roles;
      $isJuniorMember = (count(array_intersect(self::juniorMemberShipRoles, $userRoles)) > 0);
    }

    return $isJuniorMember;
  }

  public static function isMember($userName){
    $isMember = false;
    $userObject = self::getUserObject($userName);
    if($userObject){
      $userRoles = $userObject->roles;
      $isMember = (count(array_intersect(self::memberShipRoles, $userRoles)) > 0);
    }

    return $isMember;
  }

  private static function getUserObject($userName){
    global $wpdb;
    $userObject = Null;
    $userID = $wpdb->get_row( $wpdb->prepare(
          "SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $userName
      ));

    if($userID){
      $user = get_user_by('id', $userID->ID);
    }

    return $user;
  }
}
