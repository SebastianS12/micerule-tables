<?php

class EntryBookViewModel{
    public bool $pastDeadline;
    public array $grandChallengeData;
    public array $challengeData;
    public array $classData;

    public function __construct()
    {
        $this->challengeData = array();
        $this->classData = array();
    }
}