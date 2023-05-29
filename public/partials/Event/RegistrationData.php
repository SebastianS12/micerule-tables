<?php

class RegistrationData implements JsonSerializable {
  public $classRegistrations; //associative array: keys are class names and values are objects of ClassRegistrationData
  public $userRegistrations; //associative array: keys are user names and values are objects of UserRegistrationData;

  public function __construct(){
    $this->classRegistrations = array();
    $this->userRegistrations = array();
  }
}


class ClassRegistrationData implements JsonSerializable {
  public $registeredUsers;
  public $classAgeRegistrationsCount;

  public function __construct(){
    $this->registeredUsers = array();
    $this->classAgeRegistrationsCount = array();
  }

  public function addUser($userName, $userClassRegistrations){
    array_push($this->registeredUsers, $userName);
    //TODO: increase classAdRegistrationCount
    $classAdRegistrationCount += 0;
  }

  public function editRegistrationsCount($)
}


class UserRegistrationData implements JsonSerializable {
  public $className;
  public $classRegistrations;

  public function __construct($className) {
    $this->className = $className;
    $this->classRegistrations = array();
  }

  public function editRegistrationCount($age){
    $this->classRegistrations[$age] = 0;
  }

  public function getClassAgeRegistrationCount($age){
    $registrationCount = 0;
    if(isset($this->classRegistrations[$age]))
      $registrationCount = $this->classRegistrations[$age];

    return $registrationCount;
  }

  public function getRegistrationCount(){
    return array_sum($this->classRegistrations);
  }
}
