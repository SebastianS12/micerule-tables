<?php

class FancierEntries {

  public function __construct($eventID){
    $this->eventID = $eventID;
    $this->registrationData = EventRegistrationData::create($eventID);
    $this->userPrizeData = new UserPrizeData($eventID);
  }

  public function getHtml(){
    $eventClasses = EventClasses::create(EventProperties::getEventLocationID($this->eventID));
    $eventOptionalSettings = EventOptionalSettings::create(EventProperties::getEventLocationID($this->eventID));
    $html = "<div class = 'fancierEntries content'>";
    $html .= "<div class = 'showStats'>";

    foreach($this->registrationData->userRegistrationData as $userName => $userRegistrationData){
      $html .= "<div class = 'fancier-entries'>";
      $html .= "<h3 class = 'fancier-name'>".$userName."</h3>";
      foreach($userRegistrationData->userRegistrations as $className => $ageRegistrationData){
        foreach($ageRegistrationData as $age => $registrationData){
          if($userRegistrationData->getUserClassRegistrationCount($className, $age) > 0){
              $html .= "<p class='single-entry'><span>".$eventClasses->getClassIndex($className, $age)." ".$className." ".$age.": </span><span>".$userRegistrationData->getUserClassRegistrationCount($className, $age)."</span></p>";
          }
        }
      }
      if($userRegistrationData->juvenileRegistrationCount > 0){
        $html .= "<p class='single-entry'><span>".$eventClasses->getClassIndex("juvenile", "AA")." Juvenile AA: </span><span>".$userRegistrationData->juvenileRegistrationCount."</span></p>";
      }
      $html .= "<p class='single-entry'><span>Total Entries:</span><span>".$userRegistrationData->getEntryCount()."</span></p>";
      $html .= "<p class='single-entry'><span>Prize Money:</span><span>£".number_format((float)$this->userPrizeData->getUserPrizeMoney($userName, $eventOptionalSettings->prizeMoney['firstPrize'], $eventOptionalSettings->prizeMoney['secondPrize'], $eventOptionalSettings->prizeMoney['thirdPrize']), 2, '.', '')."</span></p>";
      $html .= "</div>";
    }

    $html .= "</div>";
    $html .= "</div>";

    return $html;
  }
}
