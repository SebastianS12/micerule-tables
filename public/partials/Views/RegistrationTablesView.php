<?php

class RegistrationTablesView
{
  public static function getRegistrationTablesHtml(int $eventID, string $userName)
  {
    $eventLocationID = LocationHelper::getIDFromEventPostID($eventID);
    $registrationTablesController = new RegistrationTablesController(new RegistrationTablesService(new ChallengeIndexRepository($eventLocationID), new ShowClassesRepository($eventLocationID), new ClassIndexRepository($eventLocationID), new RegistrationCountRepository($eventID, $eventLocationID)));
    $html = "<div id='registrationTables'>";
    $viewModel = $registrationTablesController->prepareViewModel($eventID, $eventLocationID, $userName);
    $html .= "<div class='classes-wrapper'>";
    $html .= self::getSectionRegistrationTableHtml($viewModel);
    $html .= self::getOptionalRegistrationTableHtml($viewModel);
    $html .= "</div>";
    $html .= self::getUpdateEntriesHtml($eventID, $eventLocationID, $userName);
    $html .= "</div>";

    return $html;
  }

  private static function getSectionRegistrationTableHtml(RegistrationTablesViewModel $viewModel)
  {
    $html = "";
    foreach (EventProperties::SECTIONNAMES as $sectionName) {
      $html .= "<div class='show-section'>";
      $html .= "<h3 class='schedule-title'>" . $sectionName . "</h3>";
      $sectionName = strtolower($sectionName);
      $html .= "<table id = '" . $sectionName . "-registrationTable'>";
      $html .= "<tbody>";
      $html .= self::getSectionHeaderRowHtml($viewModel);

      //TODO: remove isset condition, don't rely on EventProperties::SectionNames -> turn viewModel array into Collection and use where()
      if(isset($viewModel->classData[$sectionName])){
        foreach ($viewModel->classData[strtolower($sectionName)] as $className => $classData) {
          $html .= self::getClassRowHtml($classData, $className, $viewModel);
        }
  
        $challengeName = EventProperties::getChallengeName($sectionName);
        $html .= self::getSectionChallengeRowHtml($challengeName, $viewModel->challengeData[$challengeName], $viewModel);
      }

      $html .= "</tbody>";
      $html .= "</table>";
      $html .= "</div>";
    }

    return $html;
  }

  private static function getSectionHeaderRowHtml(RegistrationTablesViewModel $viewModel)
  {
    $html  = "<tr class = 'headerRow'>";
    $html .= "<td class = 'headerCell Ad'>Ad</td>";
    if ($viewModel->allowOnlineRegistrations) {
      $html .= (!$viewModel->beforeDeadline) ? "<td class='entries-count-Ad'>Entries</td><td></td><td class='entries-count-U8'>Entries</td>" : "<td class='test' colspan='3'></td>";
    } else {
      $html .= "<td class = 'registrationsDisabled' colspan='3'></td>";
    }
    $html .= "<td class = 'headerCell U8'>u8</td>";
    $html .= "</tr>";

    return $html;
  }

  private static function getClassRowHtml(array $classData, string $className, RegistrationTablesViewModel $viewModel)
  {
    $html  = "<tr class='classRowMobile'><td  colspan='7' class = 'classNameCell'>" . $className . "</td></tr>";
    $html .= "<tr class = 'classRow' id = '" . $className . "-tr'>";
    $html .= self::getClassRowAdCellHtml($classData, $className, $viewModel);
    $html .= "<td class = 'classNameCell'><span>" . $className . "</span></td>";
    $html .= self::getClassRowU8CellHtml($classData, $className, $viewModel);
    $html .= "</tr>";

    return $html;
  }

  private static function getClassRowAdCellHtml(array $classData, string $className, RegistrationTablesViewModel $viewModel)
  {
    $html = "<td class = 'positionCell Ad'>" . $classData['Ad']['index_number'] . "</td>";
    if ($viewModel->allowOnlineRegistrations) {
      $html .= ($viewModel->showRegistrationCount) ? "<td class='entries-count-Ad'>(" . $classData['Ad']['entry_count'] .")</td>" : "";
      $html .= ($viewModel->showRegistrationInput) ? "<td id = '" . $className . "&-&Ad&-&RegistrationInput' class = 'registrationInput' data-class-index = ".$classData["Ad"]["index_number"]."><input type = 'number' min = '0' value = '" . $classData["Ad"]["entry_count"] . "'></input></td>" : "";
      $html .= (!$viewModel->showRegistrationCount && !$viewModel->showRegistrationInput) ? "<td></td>" : "";
    }else{
      $html .= "<td></td>";
    }

    return $html;
  }

  private static function getClassRowU8CellHtml(array $classData, string $className, RegistrationTablesViewModel $viewModel)
  {
    $html = "";
    if ($viewModel->allowOnlineRegistrations) {
      $html .= (!$viewModel->showRegistrationCount && !$viewModel->showRegistrationInput) ? "<td></td>" : "";
      $html .= ($viewModel->showRegistrationInput) ? "<td id = '" . $className . "&-&U8&-&RegistrationInput' class = 'registrationInput' data-class-index = ".$classData["U8"]["index_number"]."><input type = 'number' min = '0' value = '" . $classData['U8']['entry_count'] . "'></input></td>" : "";
      $html .= ($viewModel->showRegistrationCount) ? "<td class='entries-count-U8'>(" . $classData['U8']['entry_count'] . ")</td>" : "";
    }else{
      $html .= "<td></td>";
    }
    $html .= "<td class = 'positionCell U8'>" . $classData['U8']['index_number'] . "</td>";

    return $html;
  }

  private static function getSectionChallengeRowHtml(string $challengeName, array $challengeData, RegistrationTablesViewModel $viewModel)
  {
    $html  = "<tr class='classRowMobile'><td  colspan='7f' class = 'classNameCell challenge'>" . $challengeName . "</td></tr>";
    $html .= "<tr class = 'classRow challenge' id = '" . $challengeName . "-tr'>";
    $html .= "<td class = 'positionCell ad'>" . $challengeData["Ad"]->challenge_index . "</td>";
    if($viewModel->allowOnlineRegistrations){
      $html .= ($viewModel->showRegistrationCount) ? "<td class='entries-count-Ad' id = '" . $challengeName . "&-&Ad'>(" . $challengeData['Ad']->registrationCount . ")</td>" : "<td></td>";
      $html .= "<td class = 'classNameCell challenge'><span>" . $challengeName . "</span></td>";
      $html .= ($viewModel->showRegistrationCount) ? "<td class='entries-count-U8' id = '" . $challengeName . "&-&U8'>(" . $challengeData['U8']->registrationCount . ")</td>" : "<td></td>";
    }else{
      $html .= "<td class = 'classNameCell challenge' colspan='3'><span>" . $challengeName . "</span></td>";
    }
    $html .= "<td class = 'positionCell u8'>" . $challengeData["U8"]->challenge_index . "</td>";
    $html .= "</tr>";

    return $html;
  }

  private static function getOptionalRegistrationTableHtml(RegistrationTablesViewModel $viewModel)
  {
    $html = "<div class='show-section'>";
    $html .= "<h3 class='schedule-title'>GRAND CHALLENGE</h3>";
    $html .= self::getGrandChallengeRegistrationTableHtml($viewModel);
    $html .= self::getOptionalClassesRegistrationTableHtml($viewModel);
    $html .= "</div>";

    return $html;
  }

  private static function getGrandChallengeRegistrationTableHtml(RegistrationTablesViewModel $viewModel)
  {
    $html = "<table id = 'grand challenge-registrationTable'>";
    $html .= "<tbody>";
    $html .= self::getSectionHeaderRowHtml($viewModel);
    if(isset($viewModel->challengeData[EventProperties::GRANDCHALLENGE])){
      $html .= self::getSectionChallengeRowHtml(EventProperties::GRANDCHALLENGE, $viewModel->challengeData[EventProperties::GRANDCHALLENGE], $viewModel);
    }
    $html .= "</tbody>";
    $html .= "</table>";

    return $html;
  }

  private static function getOptionalClassesRegistrationTableHtml(RegistrationTablesViewModel $viewModel)
  {
    $html = "<table id = 'optional-registrationTable'>";
    $html .= "<tbody>";
    if(isset($viewModel->classData['optional'])){
      foreach ($viewModel->classData['optional'] as $className => $classData) {
        if ($className == 'Junior') {
          $html .= self::getJuniorRowHtml($classData, $viewModel);
        } else {
          $html .= self::getOptionalClassRowHtml($classData, $className, $viewModel);
        }
      }
    }
    $html .= "</tbody>";
    $html .= "</table>";

    return $html;
  }

  private static function getJuniorRowHtml(array $classData, RegistrationTablesViewModel $viewModel)
  {
    $html = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell'>Junior</td></tr>";
    $html .= "<tr class = 'classRow' id = 'junior-tr'>";
    $html .= self::getJuniorRegistrationCellHtml($classData, $viewModel);
    $html .= "<td class = 'classNameCell'>Junior</td>";
    $html .= "<td class='registrationInput-optionalClass'></td>";
    $html .= "<td></td>";
    $html .= "</tr>";

    return $html;
  }

  private static function getJuniorRegistrationCellHtml(array $classData, RegistrationTablesViewModel $viewModel)
  {
    $html = "<td class = 'positionCell AA'>" . $classData['AA']['index_number'] . "</td>";
    $html .= ($viewModel->showRegistrationCount || $viewModel->showRegistrationInput) ? "<td id = 'entries-count-AA'>(" . $classData['AA']['entry_count'] . ")</td>" : "<td></td>";

    return $html;
  }

  private static function getOptionalClassRowHtml(array $classData, string $className, RegistrationTablesViewModel $viewModel)
  {
    $html = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell'>" . $className . "</td></tr>";
    $html .= "<tr class = 'classRow' id = '" . $className . "-tr'>";
    $html .= self::getOptionalClassRowCellHtml($classData, $className, $viewModel);
    $html .= "<td class = 'classNameCell'>" . $className . "</td>";
    $html .= "<td class='registrationInput-optionalClass'></td>";
    $html .= "<td></td>";
    $html .= "</tr>";

    return $html;
  }

  private static function getOptionalClassRowCellHtml(array $classData, string $className, RegistrationTablesViewModel $viewModel)
  {
    $html = "<td class = 'positionCell AA'>" . $classData['AA']['index_number'] . "</td>";
    if ($viewModel->allowOnlineRegistrations) {
      $html .= ($viewModel->showRegistrationCount) ? "<td class='entries-count-AA'>(" . $classData['AA']['entry_count'] . ")</td>" : "";
      $html .= ($viewModel->showRegistrationInput) ? "<td id = '" . $className . "&-&AA&-&RegistrationInput' class = 'registrationInput' data-class-index = ".$classData["AA"]["index_number"]."><input type = 'number' min = '0' value = '" . $classData['AA']['entry_count'] . "'></input></td>" : "";
      $html .= (!$viewModel->showRegistrationCount && !$viewModel->showRegistrationInput) ? "<td></td>" : "";
    }else{
      $html .= "<td></td>";
    }

    return $html;
  }

  //TODO: View
  private static function getUpdateEntriesHtml($eventPostID, $eventLocationID, $userName)
  {
    global $wpdb;
    $allowOnlineRegistrations = RegistrationTablesController::getAllowOnlineRegistrations($eventLocationID);
    $registrationDeadline = EventDeadlineService::getEventDeadline($eventPostID);
    $html = "<div class='update-button-wrapper'>";
    $html .= ($allowOnlineRegistrations && (time() < $registrationDeadline && is_user_logged_in()) || PermissionHelper::isLocSecOrAdmin($eventLocationID)) ? "<button type ='button' class = 'registerClassesButton'>Update Entries</button>" : "";

    $html .= "<div style = " . (($allowOnlineRegistrations && PermissionHelper::isLocSecOrAdmin($eventLocationID)) ? '' : 'visibility:hidden') . ">";
    //Get all user names
    //TODO: User Helper Class
    $users = (array) $wpdb->get_results("SELECT display_name, id FROM " . $wpdb->prefix . "users ORDER BY display_name;");
    $html .= "<select autocomplete = 'off' id = 'userSelectRegistration'>";
    foreach ($users as $user) {
      if (EventUser::isMember($user->display_name))
        $html .= "<option value = '" . html_entity_decode($user->display_name) . "' " . ((html_entity_decode($user->display_name) == $userName) ? 'selected = selected' : '') . ">" . html_entity_decode($user->display_name) . "</option>";
    }
    $html .= "</select>";
    $html .= "</div>";
    $html .= "</div>";
    $html .= "<div id = 'registerModal' style = 'hidden'></div>";

    return $html;
  }

  public static function getUserRegistrationOverviewHtml(string $userName, array $registrations): string
  {
    $userRegistrationOverviewHtml = "<h2>Registered Classes for " . $userName . "</h2>";
    $userRegistrationOverviewHtml .= "<ul>";
    foreach ($registrations as $userClassRegistrationData) {
      $userRegistrationOverviewHtml .= "<li><span class='class-entered'>" . $userClassRegistrationData['classIndex'] . "</span> " . $userClassRegistrationData['className'] . " " . $userClassRegistrationData['age'] . ": <span class='number-entered'>" . $userClassRegistrationData['registrationCount'] . "</span></li>";
    }
    $userRegistrationOverviewHtml .= "</ul>";

    return $userRegistrationOverviewHtml;
  }
}
