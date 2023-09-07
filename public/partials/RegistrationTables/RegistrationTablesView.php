<?php

class RegistrationTablesView
{
  public static function getRegistrationTablesHtml($eventID, $userName)
  {
    $eventLocationID = EventProperties::getEventLocationID($eventID);
    $html = "<div id='registrationTables'>";
    $html .= "<div class='classes-wrapper'>";
    $html .= self::getSectionRegistrationTableHtml($eventID, $eventLocationID, $userName);
    $html .= self::getOptionalRegistrationTableHtml($eventID, $eventLocationID, $userName);
    $html .= "</div>";
    $html .= self::getUpdateEntriesHtml($eventID, $eventLocationID, $userName);
    $html .= "</div>";

    return $html;
  }

  private static function getSectionRegistrationTableHtml($eventPostID, $eventLocationID, $userName)
  {
    $html = "";
    foreach (EventProperties::SECTIONNAMES as $sectionName) {
      $html .= "<div class='show-section'>";
      $html .= "<h3 class='schedule-title'>" . $sectionName . "</h3>";
      $sectionName = strtolower($sectionName);
      $html .= "<table id = '" . $sectionName . "-registrationTable'>";
      $html .= "<tbody>";
      $html .= self::getSectionHeaderRowHtml($eventPostID, $eventLocationID);

      foreach (RegistrationTablesController::getShowSectionClassesData($eventLocationID, $sectionName) as $classData) {
        $classAdRegistrationCount = RegistrationTablesController::getClassRegistrationCount($eventPostID, $classData['class_name'], "Ad");
        $classU8RegistrationCount = RegistrationTablesController::getClassRegistrationCount($eventPostID, $classData['class_name'], "U8");
        $html .= self::getClassRowHtml($classData, $classAdRegistrationCount, $classU8RegistrationCount, $userName, $eventPostID, $eventLocationID);
      }

      //add challenge row
      $sectionAdRegistrationCount = RegistrationTablesController::getSectionRegistrationCount($eventPostID, $eventLocationID, $sectionName, "Ad");
      $sectionU8RegistrationCount = RegistrationTablesController::getSectionRegistrationCount($eventPostID, $eventLocationID, $sectionName, "U8");
      $html .= self::getSectionChallengeRowHtml(EventProperties::getChallengeName($sectionName), $sectionAdRegistrationCount, $sectionU8RegistrationCount, $eventPostID, $eventLocationID);

      $html .= "</tbody>";
      $html .= "</table>";
      $html .= "</div>";
    }

    return $html;
  }

  private static function getSectionHeaderRowHtml($eventPostID, $eventLocationID)
  {
    $html  = "<tr class = 'headerRow'>";
    $html .= "<td class = 'headerCell Ad'>Ad</td>";
    if (RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID)) {
      $html .= (time() > EventProperties::getEventDeadline($eventPostID)) ? "<td class='entries-count-Ad'>Entries</td><td></td><td class='entries-count-U8'>Entries</td>" : "";
      $html .= (time() < EventProperties::getEventDeadline(($eventPostID))) ? "<td class='test' colspan='3'></td>" : "";
    } else {
      $html .= "<td class = 'registrationsDisabled'></td><td class = 'registrationsDisabled'></td><td class = 'registrationsDisabled'></td>";
    }
    $html .= "<td class = 'headerCell U8'>u8</td>";
    $html .= "</tr>";

    return $html;
  }

  private static function getClassRowHtml($classData, $classAdRegistrationCount, $classU8RegistrationCount, $userName, $eventPostID, $eventLocationID)
  {
    $html  = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell'>" . $classData['class_name'] . "</td></tr>";
    $html .= "<tr class = 'classRow' id = '" . $classData['class_name'] . "-tr'>";
    $html .= self::getClassRowAdCellHtml($classData, $classAdRegistrationCount, $userName, $eventPostID, $eventLocationID);
    $html .= "<td class = 'classNameCell'><span>" . $classData['class_name'] . "</span></td>";
    $html .= self::getClassRowU8CellHtml($classData, $classU8RegistrationCount, $userName, $eventPostID, $eventLocationID);
    $html .= "</tr>";

    return $html;
  }

  private static function getClassRowAdCellHtml($classData, $classAdRegistrationCount, $userName, $eventPostID, $eventLocationID)
  {
    $userClassRegistrationModel = new UserClassRegistration($eventPostID, $userName, $classData['class_name'], "Ad");
    $userRegistrations = $userClassRegistrationModel->getUserClassRegistrationCount();
    $html = "<td class = 'positionCell Ad'>" . $classData['ad_index'] . "</td>";
    if (RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID)) {
      $html .= (time() > EventProperties::getEventDeadline($eventPostID)) ? "<td class='entries-count-Ad'>(" . $classAdRegistrationCount . ")</td>" : "";
      $html .= (time() < EventProperties::getEventDeadline($eventPostID) && is_user_logged_in() && (EventUser::isMember($userName) || current_user_can('administrator'))) ? "<td id = '" . $classData['class_name'] . "&-&Ad&-&RegistrationInput' class = 'registrationInput'><input type = 'number' min = '0' value = '" . $userRegistrations . "'></input></td>" : "";
      $html .= (time() < EventProperties::getEventDeadline($eventPostID) && !is_user_logged_in()) ? "<td></td>" : "";
    } else {
      $html .= "<td class = 'registrationsDisabled Ad-Registrations'></td>";
    }

    return $html;
  }

  private static function getClassRowU8CellHtml($classData, $classU8RegistrationCount, $userName, $eventPostID, $eventLocationID)
  {
    $userClassRegistrationModel = new UserClassRegistration($eventPostID, $userName, $classData['class_name'], "U8");
    $userRegistrations = $userClassRegistrationModel->getUserClassRegistrationCount();
    $html = "";
    if (RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID)) {
      $html .= (time() < EventProperties::getEventDeadline($eventPostID) && is_user_logged_in() && (EventUser::isMember($userName) || current_user_can('administrator'))) ? "<td id = '" . $classData['class_name'] . "&-&U8&-&RegistrationInput' class = 'registrationInput'><input type = 'number' min = '0' value = '" . $userRegistrations . "'></input></td>" : "";
      $html .= (time() < EventProperties::getEventDeadline($eventPostID) && !is_user_logged_in()) ? "<td></td>" : "";
      $html .= (time() > EventProperties::getEventDeadline($eventPostID)) ? "<td class='entries-count-U8'>(" . $classU8RegistrationCount . ")</td>" : "";
    } else {
      $html .= "<td class = 'registrationsDisabled U8Registrations'></td>";
    }
    $html .= "<td class = 'positionCell U8'>" . $classData['u8_index'] . "</td>";

    return $html;
  }

  private static function getSectionChallengeRowHtml($challengeName, $sectionAdRegistrationCount, $sectionU8RegistrationCount, $eventPostID, $eventLocationID)
  {
    $html  = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell challenge'>" . $challengeName . "</td></tr>";
    $html .= "<tr class = 'classRow challenge' id = '" . $challengeName . "-tr'>";
    $html .= "<td class = 'positionCell ad'>" . RegistrationTablesController::getChallengeIndex($eventLocationID, $challengeName, "Ad") . "</td>";
    $html .= (time() > EventProperties::getEventDeadline($eventPostID) && RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID)) ? "<td class='entries-count-Ad' id = '" . $challengeName . "&-&Ad'>(" . $sectionAdRegistrationCount . ")</td>" : "<td class = 'registrationsDisabled'></td>";
    $html .= "<td class = 'classNameCell challenge'><span>" . $challengeName . "</span></td>";
    $html .= (time() > EventProperties::getEventDeadline($eventPostID) && RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID)) ? "<td class='entries-count-U8' id = '" . $challengeName . "&-&U8'>(" . $sectionU8RegistrationCount . ")</td>" : "<td class = 'registrationsDisabled'></td>";
    $html .= "<td class = 'positionCell u8'>" . RegistrationTablesController::getChallengeIndex($eventLocationID, $challengeName, "U8") . "</td>";
    $html .= "</tr>";

    return $html;
  }

  private static function getOptionalRegistrationTableHtml($eventPostID, $eventLocationID, $userName)
  {
    $html = "<div class='show-section'>";
    $html .= "<h3 class='schedule-title'>GRAND CHALLENGE</h3>";
    $html .= self::getGrandChallengeRegistrationTableHtml($eventPostID, $eventLocationID);
    $html .= self::getOptionalClassesRegistrationTableHtml($eventPostID, $eventLocationID, $userName);
    $html .= "</div>";

    return $html;
  }

  private static function getGrandChallengeRegistrationTableHtml($eventPostID, $eventLocationID)
  {
    $html = "<table id = 'grand challenge-registrationTable'>";
    $html .= "<tbody>";
    $html .= self::getSectionHeaderRowHtml($eventPostID, $eventLocationID);
    $grandChallengeAdRegistrationCount = RegistrationTablesController::getGrandChallengeRegistrationCount($eventPostID, $eventLocationID, "Ad");
    $grandChallengeU8RegistrationCount = RegistrationTablesController::getGrandChallengeRegistrationCount($eventPostID, $eventLocationID, "U8");
    $html .= self::getSectionChallengeRowHtml(EventProperties::GRANDCHALLENGE, $grandChallengeAdRegistrationCount, $grandChallengeU8RegistrationCount, $eventPostID, $eventLocationID);
    $html .= "</tbody>";
    $html .= "</table>";

    return $html;
  }

  private static function getOptionalClassesRegistrationTableHtml($eventPostID, $eventLocationID, $userName)
  {
    $html = "<table id = 'optional-registrationTable'>";
    $html .= "<tbody>";
    foreach (RegistrationTablesController::getShowOptionalClassesData($eventLocationID) as $classData) {
      if ($classData['class_name'] == 'Junior') {
        $html .= self::getJuniorRowHtml($classData, $eventPostID, $eventLocationID);
      } else {
        $html .= self::getOptionalClassRowHtml($classData, $eventPostID, $eventLocationID, $userName);
      }
    }
    $html .= "</tbody>";
    $html .= "</table>";

    return $html;
  }

  private static function getJuniorRowHtml($classData, $eventPostID, $eventLocationID)
  {
    $html = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell'>Junior</td></tr>";
    $html .= "<tr class = 'classRow' id = 'junior-tr'>";
    $html .= self::getJuniorRegistrationCellHtml($classData, $eventPostID, $eventLocationID);
    $html .= "<td class = 'classNameCell'>Junior</td>";
    $html .= "<td class='registrationInput-optionalClass'></td>";
    $html .= "<td></td>";
    $html .= "</tr>";

    return $html;
  }

  private static function getJuniorRegistrationCellHtml($classData, $eventPostID, $eventLocationID)
  {
    $registrationCount = 0;
    $html = "<td class = 'positionCell AA'>" . $classData['aa_index'] . "</td>";
    if (RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID)) {
      $html .= (time() > EventProperties::getEventDeadline($eventPostID)) ? "<td id = 'entries-count-AA'>(" . $registrationCount . ")</td>" : "<td></td>";
    } else {
      $html .= "<td class = 'registrationsDisabled'></td>";
    }

    return $html;
  }

  private static function getOptionalClassRowHtml($classData, $eventPostID, $eventLocationID, $userName)
  {
    $html = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell'>" . $classData['class_name'] . "</td></tr>";
    $html .= "<tr class = 'classRow' id = '" . $classData['class_name'] . "-tr'>";
    $html .= self::getOptionalClassRowCellHtml($classData, $eventPostID, $eventLocationID, $userName);
    $html .= "<td class = 'classNameCell'>" . $classData['class_name'] . "</td>";
    $html .= "<td class='registrationInput-optionalClass'></td>";
    $html .= "<td></td>";
    $html .= "</tr>";

    return $html;
  }

  //TODO: $registrationCount
  private static function getOptionalClassRowCellHtml($classData, $eventPostID, $eventLocationID, $userName)
  {
    $registrationCount = 0;
    $html = "<td class = 'positionCell AA'>" . $classData['aa_index'] . "</td>";
    if (RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID)) {
      $html .= (time() < EventProperties::getEventDeadline($eventPostID) && is_user_logged_in() && (EventUser::isMember($userName) || current_user_can('administrator'))) ? "<td id = '" . $classData['class_name'] . "&-&AA&-&RegistrationInput' class = 'registrationInput-optionalClass'><input type = 'number' min = '0' value = '" . $registrationCount . "'></input></td>" : "";
      $html .= (time() < EventProperties::getEventDeadline($eventPostID) && !is_user_logged_in()) ? "<td></td>" : "";
      $html .= (time() > EventProperties::getEventDeadline($eventPostID)) ? "<td class='entries-count-AA'>(" . $registrationCount . ")</td>" : "";
    } else {
      $html .= "<td class = 'registrationsDisabled'></td>";
    }

    return $html;
  }

  private static function getUpdateEntriesHtml($eventPostID, $eventLocationID, $userName)
  {
    global $wpdb;
    $allowOnlineRegistrations = RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID);
    $registrationDeadline = EventProperties::getEventDeadline($eventPostID);
    $html = "<div class='update-button-wrapper'>";
    $html .= ($allowOnlineRegistrations && time() < $registrationDeadline && is_user_logged_in() && (EventUser::isMember($userName) || current_user_can('administrator'))) ? "<button type ='button' class = 'registerClassesButton'>Update Entries</button>" : "";

    $html .= "<div style = " . (($allowOnlineRegistrations && is_user_logged_in() && time() < $registrationDeadline && $allowOnlineRegistrations && (current_user_can('administrator') || in_array(wp_get_current_user()->display_name, LocationSecretaries::getLocationSecretaryNames($eventLocationID)))) ? '' : 'visibility:hidden') . ">";
    //Get all user names
    //TODO: User Helper Class
    $users = (array) $wpdb->get_results("SELECT display_name, id FROM " . $wpdb->prefix . "users ORDER BY display_name;");
    $html .= "<select autocomplete = 'off' id = 'userSelectRegistration'>";
    foreach ($users as $user) {
      if (EventUser::isMember($user->display_name))
        $html .= "<option value = '" . $user->display_name . "' " . (($user->display_name == $userName) ? 'selected = selected' : '') . ">" . $user->display_name . "</option>";
    }
    $html .= "</select>";
    $html .= "</div>";
    $html .= "</div>";
    $html .= "<div id = 'registerModal' style = 'hidden'></div>";

    return $html;
  }

  public static function getUserRegistrationOverviewHtml($eventPostID, $userName)
  {
    $userRegistrations = RegistrationTablesController::getUserRegistrations($eventPostID, $userName);
    $userRegistrationOverviewHtml = "<h2>Registered Classes for " . $userName . "</h2>";
    $userRegistrationOverviewHtml .= "<ul>";
    foreach ($userRegistrations as $userClassRegistrationData) {
      $userRegistrationOverviewHtml .= "<li><span class='class-entered'>" . $userClassRegistrationData['class_index'] . "</span> " . $userClassRegistrationData['class_name'] . " " . $userClassRegistrationData['age'] . ": <span class='number-entered'>" . $userClassRegistrationData['registration_count'] . "</span></li>";
    }
    $userRegistrationOverviewHtml .= "</ul>";

    return $userRegistrationOverviewHtml;
  }
}
