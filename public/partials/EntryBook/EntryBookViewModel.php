<?php

class EntryBookViewModel{
    public bool $pastDeadline;
    public array $grandChallengeData;
    public array $challengeData;
    public array $classData;
    public array $optionalClassData;

    public function __construct()
    {
        $this->grandChallengeData = array();
        $this->challengeData = array();
        $this->classData = array();
        $this->optionalClassData = array();
    }
}