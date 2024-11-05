<?php

class AbsenteesViewModel{
    public array $absentees;

    public function __construct()
    {
        $this->absentees = array();
    }

    public function addJudge(string $judgeName): void
    {
        $this->absentees[$judgeName] = array();
    }

    public function addAbsentee(string $judgeName, int $classIndex, int $penNumber){
        if(!isset($this->absentees[$judgeName])){
            $this->absentees[$judgeName] = array();
        }
        if(!isset($this->absentees[$judgeName][$classIndex])){
            $this->absentees[$judgeName][$classIndex] = array();
        }

        $this->absentees[$judgeName][$classIndex][] = $penNumber;
    }
}