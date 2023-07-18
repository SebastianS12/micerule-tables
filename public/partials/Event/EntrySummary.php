<?php

class EntrySummary implements IAdminTab {
  private $eventID;
  private $entryData;

  public function __construct($eventID){
    $this->eventID = $eventID;
    $entryDataFactory = new AdminTabDataFactory($this->eventID);
    $this->entryData = $entryDataFactory->getUserEntryData();
  }


  function getHtml(){
    $eventOptionalSettings = EventOptionalSettings::create(EventProperties::getEventLocationID($this->eventID));
    $registrationFee = $eventOptionalSettings->registrationFee;

    $html = "<div class = 'entrySummary content'>";
    foreach($this->entryData->userEntries as $userName => $userEntryData){
      $checkBoxState = ($this->entryData->allAbsent($userName)) ? "checked" : "";
      $html .= "<div class='fancier-entry-summary'>
                  <div class='set-absent'>
                    <input type = 'checkbox' id = 'setAllAbsent'  class = 'setAllAbsent' name = 'setAllAbsent' ".$checkBoxState.">
                    <label for = 'setAllAbsent'>Set all absent</label>
                  </div>
                  <div class='table-wrapper'>
                  <table>
                    <thead class='header-wrapper'>
                      <tr>
                        <th colspan=3><p>".$userName."</p></th>
                      </tr>
                      <tr>
                        <th class='js-pen-no'>№</th>
                        <th class='js-notes'>Class</th>
                      </tr>
                    </thead>
                    <tbody>";

      foreach($userEntryData as $entryData){
        $html .= "<tr>
                    <td class='js-pen-no'>".$entryData->penNumber."</td>
                    <td class='js-notes'>".$entryData->classIndex." | ".$entryData->className." ".$entryData->age."</td>
                  </tr>";
      }
      $html .= "<tr><td colspan = 2>Entry Fee: £".$this->entryData->getUserEntryFee($userName, floatval($registrationFee))."</td></tr>";

      $html .= "    </tbody>
                  </table>
                </div>
              </div>";
    }

    $html .= "  </div>";

    return $html;
  }
}
