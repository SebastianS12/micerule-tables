<?php

class UserRegistrationModel{
    public int $id;
    public int $eventPostID;
    public string $userName;
    public int $classID;
    public string $age;
    public EntryClassModel $showClass;

    private function __construct($eventPostID, $userName, $classID, $age)
    {
        $this->eventPostID = $eventPostID;
        $this->userName = $userName;
        $this->classID = $classID;
        $this->age = $age;
    }

    public static function create($eventPostID, $userName, $classID, $age){
        return new self($eventPostID, $userName, $classID, $age);
    }

    public static function createWithID($id, $eventPostID, $userName, $classID, $age){
        $instance = self::create($eventPostID, $userName, $classID, $age);
        $instance->id = $id;
        return $instance;
    }

    public function getClassName(): string{
        if(!(isset($this->showClass)))
            return "";

        return $this->showClass->className;
    }

    public function getSectionName(): string{
        if(!(isset($this->showClass)))
            return "";

        return $this->showClass->sectionName;
    }
}