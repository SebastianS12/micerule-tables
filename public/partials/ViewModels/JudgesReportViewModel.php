<?php

class JudgesReportViewModel{
    public string $showName;
    public string $date;
    public string $userName;
    public bool $canAdmin;
    public array $judgeGeneral;
    public array $classReports;
    public array $challengeReports;
    public array $optionalClassReports;

    public function __construct(string $showName, string $date, string $userName, bool $canAdmin)
    {
        $this->showName = $showName;
        $this->date = $date;
        $this->userName = $userName;
        $this->canAdmin = $canAdmin;
        $this->judgeGeneral = array();
        $this->classReports = array();
        $this->challengeReports = array();
        $this->optionalClassReports = array();
    }

    public function addJudgeComment(string $judge, int $judgeID, ?int $commentID, string $comment): void
    {
        if(!isset($this->judgeGeneral[$judge])){
            $this->judgeGeneral[$judge] = array();
        }

        $judgeData = array();
        $judgeData['judgeName'] = $judge;
        $judgeData['judgeID'] = $judgeID;
        $judgeData['commentID'] = $commentID;
        $judgeData['comment'] = $comment;
        $judgeData['sections'] = array();

        $this->judgeGeneral[$judge] = $judgeData;
    }

    public function addJudgeSection(string $judge, string $section): void
    {
        if(isset($this->judgeGeneral[$judge])){
            $this->judgeGeneral[$judge]['sections'][] = $section; 
        }
    }

    public function addClassReport(int $indexID, ?int $commentID, string $judge, int $classIndex, string $comment, string $section, string $className, string $age, int $entryCount): void
    {
        $this->initializeClassReportIfNotSet($judge, $section, $classIndex);

        $reportData = array();
        $reportData['indexID'] = $indexID;
        $reportData['commentID'] = $commentID;
        $reportData['comment'] = $comment;
        $reportData['index'] = $classIndex;
        $reportData['section'] = $section;
        $reportData['className'] = $className;
        $reportData['age'] = $age;
        $reportData['entryCount'] = $entryCount;
        $reportData['placements'] = array();

        $this->classReports[$judge][$section][$classIndex] = $reportData;
    }

    public function addPlacementReport(?int $reportID, string $judge, string $section, int $classIndex, int $placementID, int $placement, string $userName, ?string $gender, string $comment): void
    {
        $this->initializeClassReportIfNotSet($judge, $section, $classIndex);

        $placementReport = array();
        $placementReport['userName'] = $userName;
        $placementReport['placementID'] = $placementID;
        $placementReport['placement'] = $placement;
        $placementReport['gender'] = $gender;
        $placementReport['comment'] = $comment;
        $placementReport['reportID'] = $reportID;
        
        $this->classReports[$judge][$section][$classIndex]['placements'][$placement] = $placementReport;
    }

    private function initializeClassReportIfNotSet(string $judge, string $section, int $classIndex): void
    {
        if (!isset($this->classReports[$judge][$section][$classIndex])) {
            $this->classReports[$judge][$section][$classIndex] = array();
        }
    }

    public function addChallengeReport(string $judge, int $challengeIndex, string $section, string $challengeName, string $age, int $entryCount): void
    {
        $this->initializeChallengeReportIfNotSet($judge, $section, $challengeIndex);

        $reportData = array();
        $reportData['index'] = $challengeIndex;
        $reportData['section'] = $section;
        $reportData['className'] = $challengeName;
        $reportData['age'] = $age;
        $reportData['entryCount'] = $entryCount;
        $reportData['placements'] = array();

        $this->challengeReports[$judge][$section][$challengeIndex] = $reportData;
    }

    public function addChallengePlacementReport(string $judge, string $section, int $challengeIndex, string $fancierName, int $placement, string $varietyName): void
    {
        $this->initializeChallengeReportIfNotSet($judge, $section, $challengeIndex);

        $placementReport = array();
        $placementReport['userName'] = $fancierName;
        $placementReport['placement'] = $placement;
        $placementReport['varietyName'] = $varietyName;
        
        $this->challengeReports[$judge][$section][$challengeIndex]['placements'][$placement] = $placementReport;
    }

    private function initializeChallengeReportIfNotSet(string $judge, string $section, int $challengeIndex): void
    {
        if (!isset($this->challengeReports[$judge][$section][$challengeIndex])) {
            $this->challengeReports[$judge][$section][$challengeIndex] = array();
        }
    }

    public function addOptionalClassReport(int $indexID, ?int $commentID, int $classIndex, string $comment, string $section, string $className, string $age, int $entryCount): void
    {
        $this->initializeOptionalClassReportIfNotSet($classIndex);

        $reportData = array();
        $reportData['indexID'] = $indexID;
        $reportData['commentID'] = $commentID;
        $reportData['comment'] = $comment;
        $reportData['index'] = $classIndex;
        $reportData['section'] = $section;
        $reportData['className'] = $className;
        $reportData['age'] = $age;
        $reportData['entryCount'] = $entryCount;
        $reportData['placements'] = array();

        $this->optionalClassReports[$classIndex] = $reportData;
    }

    public function addOptionalClassPlacementReport(?int $reportID, int $classIndex, int $placementID, int $placement, string $userName, ?string $gender, string $comment): void
    {
        $this->initializeOptionalClassReportIfNotSet($classIndex);

        $placementReport = array();
        $placementReport['userName'] = $userName;
        $placementReport['placementID'] = $placementID;
        $placementReport['placement'] = $placement;
        $placementReport['gender'] = $gender;
        $placementReport['comment'] = $comment;
        $placementReport['reportID'] = $reportID;
        
        $this->classReports[$classIndex]['placements'][$placement] = $placementReport;
    }

    private function initializeOptionalClassReportIfNotSet(int $classIndex): void
    {
        if (!isset($this->optionalClassReports[$classIndex])) {
            $this->optionalClassReports[$classIndex] = array();
        }
    }
}