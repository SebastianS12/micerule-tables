<?php

class EventOptionalSettings implements JsonSerializable{

  public function __construct($allowOnlineRegistrations = false, $registrationFee = "", $unstandardised = false, $junior = false, $auction = false, $firstPrize = "", $secondPrize = "", $thirdPrize = ""){
    $this->allowOnlineRegistrations = $allowOnlineRegistrations;
    $this->registrationFee = $registrationFee;
    $this->optionalClasses = array();
    $this->optionalClasses['unstandardised'] = $unstandardised;
    $this->optionalClasses['junior'] = $junior;
    $this->optionalClasses['auction'] = $auction;
    $this->prizeMoney['firstPrize'] = $firstPrize;
    $this->prizeMoney['secondPrize'] = $secondPrize;
    $this->prizeMoney['thirdPrize'] = $thirdPrize;
  }

  public static function createFromJson($json){
    $jsonObject = json_decode($json);
    return new EventOptionalSettings($jsonObject->allowOnlineRegistrations, $jsonObject->registrationFee, $jsonObject->optionalClasses->unstandardised, $jsonObject->optionalClasses->junior, $jsonObject->optionalClasses->auction, $jsonObject->prizeMoney->firstPrize, $jsonObject->prizeMoney->secondPrize, $jsonObject->prizeMoney->thirdPrize);
  }

  public static function create($locationID){
    $optionalSettings = new EventOptionalSettings();
    $optionalSettingsJson = get_post_meta($locationID, 'micerule_data_location_optional_settings', true);
    if($optionalSettingsJson != "")
      $optionalSettings = EventOptionalSettings::createFromJson($optionalSettingsJson);

    return $optionalSettings;
  }

  public function jsonSerialize(){
    return get_object_vars($this);
  }
}
