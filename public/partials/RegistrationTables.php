<?php

class RegistrationTables {

  public function __construct($eventID, $userName){
    $this->eventID = $eventID;
    $this->locationID = EventProperties::getEventLocationID($eventID);
    $this->eventClasses = EventClasses::create($this->locationID);
    $this->eventRegistrationData = EventRegistrationData::create($eventID);
    $this->userName = $userName;
    $this->userRegistrationData = $this->eventRegistrationData->getUserRegistrationData($userName);
    $this->eventDeadline = EventProperties::getEventDeadline($eventID);
    $this->eventOptionalSettings = EventOptionalSettings::create($this->locationID);
    $this->previousSectionCount = 0;
  }

  public function getHtml(){
    $html = "<div id='registrationTables'>";
    $html .= "<div class='classes-wrapper'>";
    $html .= $this->getSectionRegistrationTableHtml();
    $html .= $this->getOptionalRegistrationTableHtml();
    $html .= "</div>";
    $html .= $this->getUpdateEntriesHtml();
    $html .= "</div>";

    return $html;
  }

  private function getSectionRegistrationTableHtml(){
    $sectionNames = EventProperties::SECTIONNAMES;
    $challengeNames = EventProperties::CHALLENGENAMES;
    $html = "";

    $grandChallengeAdRegistrationCount = 0;
    $grandChallengeU8RegistrationCount = 0;
    foreach($sectionNames as $index => $sectionName){
      $html .= "<div class='show-section'>";
      $html .= "<h3 class='schedule-title'>".$sectionName."</h3>";
      $sectionName = strtolower($sectionName);
      $html .= "<table id = '".$sectionName."-registrationTable'>";
      $html .= "<tbody>";
      $html .= $this->getSectionHeaderRowHtml();

      $sectionAdRegistrationCount = 0;
      $sectionU8RegistrationCount = 0;
        foreach($this->eventClasses->getSectionClasses($sectionName) as $position => $className){
          $classAdRegistrationCount = $this->eventRegistrationData->getClassRegistrationData($className)->getRegistrationCount("Ad");
          $sectionAdRegistrationCount += $classAdRegistrationCount;

          $classU8RegistrationCount = $this->eventRegistrationData->getClassRegistrationData($className)->getRegistrationCount("U8");
          $sectionU8RegistrationCount += $classU8RegistrationCount;

          $html .= $this->getClassRowHtml($sectionName, $className, $classAdRegistrationCount, $classU8RegistrationCount, $position);
        }
        $this->previousSectionCount += $this->eventClasses->getSectionClassCount($sectionName);

      $grandChallengeAdRegistrationCount += $sectionAdRegistrationCount;
      $grandChallengeU8RegistrationCount += $sectionU8RegistrationCount;

      //add challenge row
      $html .= $this->getSectionChallengeRowHtml($challengeNames[$sectionName], $sectionAdRegistrationCount, $sectionU8RegistrationCount);
      $this->previousSectionCount++;

      $html .= "</tbody>";
      $html .= "</table>";
      $html .= "</div>";
    }

    return $html;
  }

  private function getSectionHeaderRowHtml(){
    $html  = "<tr class = 'headerRow'>";
    $html .= "<td class = 'headerCell Ad'>Ad</td>";
    if($this->eventOptionalSettings->allowOnlineRegistrations){
      $html .= (time() > strtotime($this->eventDeadline)) ? "<td class='entries-count-Ad'>Entries</td><td></td><td class='entries-count-U8'>Entries</td>" : "";
      $html .= (time() < strtotime($this->eventDeadline)) ? "<td class='test' colspan='3'></td>" : "";
    }else{
      $html .= "<td class = 'registrationsDisabled'></td><td class = 'registrationsDisabled'></td><td class = 'registrationsDisabled'></td>";
    }
    $html .= "<td class = 'headerCell U8'>u8</td>";
    $html .= "</tr>";

    return $html;
  }

  private function getClassRowHtml($sectionName, $className, $classAdRegistrationCount, $classU8RegistrationCount, $position){
    $html  = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell'>".$className."</td></tr>";
    $html .= "<tr class = 'classRow' id = '".$className."-tr'>";
    $html .= $this->getClassRowAdCellHtml($sectionName, $className, $classAdRegistrationCount, $position);
    $html .= "<td class = 'classNameCell'><span>".$className."</span></td>";
    $html .= $this->getClassRowU8CellHtml($sectionName, $className, $classU8RegistrationCount, $position);
    $html .= "</tr>";

    return $html;
  }

  private function getClassRowAdCellHtml($sectionName, $className, $classAdRegistrationCount, $position){
    $classIndex = $this->eventClasses->getClassIndex($className, "Ad");
    $html = "<td class = 'positionCell Ad'>".$classIndex."</td>";
    if($this->eventOptionalSettings->allowOnlineRegistrations){
      $html .= (time() > strtotime($this->eventDeadline)) ? "<td class='entries-count-Ad'>(".$classAdRegistrationCount.")</td>" : "";
      $html .= (time() < strtotime($this->eventDeadline) && is_user_logged_in() && (EventUser::isMember($this->userName) || current_user_can('administrator'))) ? "<td id = '".$className."&-&".$classIndex."&-&Ad&-&RegistrationInput' class = 'registrationInput'><input type = 'number' min = '0' value = '".$this->userRegistrationData->getUserClassRegistrationCount($className, "Ad")."'></input></td>" : "";
      $html .= (time() < strtotime($this->eventDeadline) && !is_user_logged_in()) ? "<td></td>" : "";
    }else{
      $html .= "<td class = 'registrationsDisabled Ad-Registrations'></td>";
    }

    return $html;
  }

  private function getClassRowU8CellHtml($sectionName, $className, $classU8RegistrationCount, $position){
    $classIndex = $this->eventClasses->getClassIndex($className, "U8");//(2 * ($position + $this->previousSectionCount) + 2);
    $html = "";
    if($this->eventOptionalSettings->allowOnlineRegistrations){
      $html .= (time() < strtotime($this->eventDeadline) && is_user_logged_in() && (EventUser::isMember($this->userName) || current_user_can('administrator'))) ? "<td id = '".$className."&-&".$classIndex."&-&U8&-&RegistrationInput' class = 'registrationInput'><input type = 'number' min = '0' value = '".$this->userRegistrationData->getUserClassRegistrationCount($className, "U8")."'></input></td>" : "";
      $html .= (time() < strtotime($this->eventDeadline) && !is_user_logged_in()) ? "<td></td>" : "";
      $html .= (time() > strtotime($this->eventDeadline)) ? "<td class='entries-count-U8'>(".$classU8RegistrationCount.")</td>" : "";
    }else{
      $html .= "<td class = 'registrationsDisabled U8Registrations'></td>";
    }
    $html .= "<td class = 'positionCell U8'>".$classIndex."</td>";

    return $html;
  }

  private function getSectionChallengeRowHtml($challengeName, $sectionAdRegistrationCount, $sectionU8RegistrationCount){
    $html  = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell challenge'>".$challengeName."</td></tr>";
    $html .= "<tr class = 'classRow challenge' id = '".$challengeName."-tr'>";
    $html .= "<td class = 'positionCell ad'>".$this->eventClasses->getClassIndex($challengeName, "Ad")."</td>";
    $html .= (time() > strtotime($this->eventDeadline) && $this->eventOptionalSettings->allowOnlineRegistrations) ? "<td class='entries-count-Ad' id = '".$challengeName."&-&Ad'>(".$sectionAdRegistrationCount.")</td>" : "<td class = 'registrationsDisabled'></td>";
    $html .= "<td class = 'classNameCell challenge'><span>".$challengeName."</span></td>";
    $html .= (time() > strtotime($this->eventDeadline) && $this->eventOptionalSettings->allowOnlineRegistrations) ? "<td class='entries-count-U8' id = '".$challengeName."&-&U8'>(".$sectionU8RegistrationCount.")</td>" : "<td class = 'registrationsDisabled'></td>";
    $html .= "<td class = 'positionCell u8'>".$this->eventClasses->getClassIndex($challengeName, "U8")."</td>";
    $html .= "</tr>";

    return $html;
  }

  private function getOptionalRegistrationTableHtml(){
    $html = "<div class='show-section'>";
    $html .= "<h3 class='schedule-title'>GRAND CHALLENGE</h3>";
    $html .= $this->getGrandChallengeRegistrationTableHtml();
    $html .= $this->getOptionalClassesRegistrationTableHtml();
    $html .= "</div>";

    return $html;
  }

  private function getGrandChallengeRegistrationTableHtml(){
    $html = "<table id = 'grand challenge-registrationTable'>";
    $html .= "<tbody>";
    $html .= $this->getSectionHeaderRowHtml();
    $html .= $this->getSectionChallengeRowHtml("GRAND CHALLENGE", $this->eventRegistrationData->getGrandChallengeRegistrationCount("Ad"), $this->eventRegistrationData->getGrandChallengeRegistrationCount("U8"));
    $this->previousSectionCount++;
    $html .= "</tbody>";
    $html .= "</table>";

    return $html;
  }

  private function getOptionalClassesRegistrationTableHtml(){
    //count +1 from here on
    $this->previousSectionCount = $this->previousSectionCount * 2;

    $html = "<table id = 'optional-registrationTable'>";
    $html .= "<tbody>";
    foreach($this->eventClasses->optionalClasses as $position => $className){
        if($className == 'juvenile'){
          $html .= $this->getJuvenileRowHtml();
        }else{
          $html .= $this->getOptionalClassRowHtml($className);
        }
    }
    $html .= "</tbody>";
    $html .= "</table>";

    return $html;
  }

  private function getJuvenileRowHtml(){
    $html = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell'>Juvenile</td></tr>";
    $html .= "<tr class = 'classRow' id = 'juvenile-tr'>";
    $html .= $this->getJuvenileRegistrationCellHtml();
    $html .= "<td class = 'classNameCell'>Juvenile</td>";
    $html .= "<td class='registrationInput-optionalClass'></td>";
    $html .= "<td></td>";
    $html .= "</tr>";

    return $html;
  }

  private function getJuvenileRegistrationCellHtml(){
    $html = "<td class = 'positionCell AA'>".$this->eventClasses->getClassIndex("juvenile", "AA")."</td>";
    if($this->eventOptionalSettings->allowOnlineRegistrations){
      $html .= (time() > strtotime($this->eventDeadline)) ? "<td id = 'entries-count-AA'>(".$this->eventRegistrationData->getJuvenileRegistrationCount().")</td>" : "<td></td>";
    }else{
      $html .= "<td class = 'registrationsDisabled'></td>";
    }

    return $html;
  }

  private function getOptionalClassRowHtml($className){
    $html = "<tr class='classRowMobile'><td  colspan='5' class = 'classNameCell'>".$className."</td></tr>";
    $html .= "<tr class = 'classRow' id = '".$className."-tr'>";
    $html .= $this->getOptionalClassRowCellHtml($className);
    $html .= "<td class = 'classNameCell'>".$className."</td>";
    $html .= "<td class='registrationInput-optionalClass'></td>";
    $html .= "<td></td>";
    $html .= "</tr>";

    return $html;
  }

  private function getOptionalClassRowCellHtml($className){
    $classIndex = $this->eventClasses->getClassIndex($className, "AA");
    $html = "<td class = 'positionCell AA'>".$classIndex."</td>";
    if($this->eventOptionalSettings->allowOnlineRegistrations){
      $html .= (time() < strtotime($this->eventDeadline) && is_user_logged_in() && (EventUser::isMember($this->userName) || current_user_can('administrator'))) ? "<td id = '".$className."&-&".$classIndex."&-&AA&-&RegistrationInput' class = 'registrationInput-optionalClass'><input type = 'number' min = '0' value = '".$this->userRegistrationData->getUserClassRegistrationCount($className, "AA")."'></input></td>" : "";
      $html .= (time() < strtotime($this->eventDeadline) && !is_user_logged_in()) ? "<td></td>" : "";
      $html .= (time() > strtotime($this->eventDeadline)) ? "<td class='entries-count-AA'>(".$this->eventRegistrationData->getOptionalClassRegistrationData($className)->getRegistrationCount("AA").")</td>" : "";
    }else{
      $html .= "<td class = 'registrationsDisabled'></td>";
    }

    return $html;
  }

  private function getUpdateEntriesHtml(){
    global $wpdb;

    $html = "<div class='update-button-wrapper'>";
    $html .= (time() < strtotime($this->eventDeadline) && is_user_logged_in() && (EventUser::isMember($this->userName) || current_user_can('administrator'))) ? "<button type ='button' class = 'registerClassesButton'>Update Entries</button>" : "";

    $html .= "<div style = ".((is_user_logged_in() && time() < strtotime($this->eventDeadline) && $this->eventOptionalSettings->allowOnlineRegistrations && (current_user_can('administrator') || in_array(wp_get_current_user()->display_name, EventProperties::getLocationSecretaries($this->locationID)['name']) )) ? '' : 'visibility:hidden').">";
    //Get all user names
    $users = (array) $wpdb->get_results("SELECT display_name, id FROM " .$wpdb->prefix."users ORDER BY display_name;");
    $html .= "<select autocomplete = 'off' id = 'userSelectRegistration'>";
    foreach($users as $user){
      if(EventUser::isMember($user->display_name))
        $html .= "<option value = '".$user->display_name."' ".(($user->display_name == $this->userName) ? 'selected = selected' : '').">".$user->display_name."</option>";
    }
    $html .= "</select>";
    $html .= "</div>";
    $html .= "</div>";
    $html .= "<div id = 'registerModal' style = 'hidden'></div>";

    return $html;
  }

  private function isUserMember($user){
    $memberShipRoles = array('pms_subscription_plan_6551', 'pms_subscription_plan_6549', 'pms_subscription_plan_13315', 'pms_subscription_plan_13314', 'pms_subscription_plan_13309', 'pms_subscription_plan_13305', 'pms_subscription_plan_13317', 'pms_subscription_plan_13307',
    'pms_subscription_plan_13322', 'pms_subscription_plan_13320', 'pms_subscription_plan_13319', 'pms_subscription_plan_13318', 'pms_subscription_plan_13311', 'pms_subscription_plan_13306', 'pms_subscription_plan_13313', 'pms_subscription_plan_13312');
    $user = get_user_by('id', $user->id);
    $userRoles = $user->roles;

    return (count(array_intersect($memberShipRoles, $userRoles)) > 0);
  }
}
