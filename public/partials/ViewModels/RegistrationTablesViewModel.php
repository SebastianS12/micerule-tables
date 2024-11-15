<?php

class RegistrationTablesViewModel{
    public array $challengeData;
    public array $classData;
    public bool $allowOnlineRegistrations;
    public bool $beforeDeadline;
    public bool $isLoggedIn;
    public bool $isMember;
    public bool $isAdmin;

    public function __construct()
    {
        $this->challengeData = array();
        $this->classData = array();
    }
}