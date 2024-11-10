<?php

class ShowReportPostViewModel{
    public string $postTitle;
    public array $judges;
    public array $classReports;
    public array $challengeReports;
    public array $grandChallengeReport;
    public array $juniorReport;

    public function __construct(string $postTitle)
    {
        $this->postTitle = $postTitle;
        $this->judges = array();
        $this->classReports = array();
        $this->challengeReports = array();
        $this->grandChallengeReport = array();
        $this->juniorReport = array();
    }

    public function addJudge(string $judge): void
    {
        $this->judges[$judge] = array();
        $this->judges[$judge]['comment'] = "";
        $this->judges[$judge]['sections'] = array();
    }

    public function addJudgeComment(string $judge, string $comment): void
    {
        if(!isset($this->judges[$judge])) $this->addJudge($judge);
        $this->judges[$judge]['comment'] = $comment;
    }

    public function addJudgeSection(string $judge, string $section): void
    {
        if(!isset($this->judges[$judge])) $this->addJudge($judge);
        $this->judges[$judge]['sections'][] = $section;
    }

    public function addClassReport(string $judge, string $section, string $className, int $classIndex, string $age, int $entryCount, string $comment): void
    {
        $this->initializeClassReportIfNotSet($judge, $section, $classIndex);

        $reportData = array();
        $reportData['className'] = $className;
        $reportData['age'] = $age;
        $reportData['entryCount'] = $entryCount;
        $reportData['comment'] = $comment;
        $reportData['placements'] = array();

        $this->classReports[$judge][$section][$classIndex] = $reportData;
    }

    private function initializeClassReportIfNotSet(string $judge, string $section, int $classIndex): void
    {
        if (!isset($this->classReports[$judge][$section][$classIndex])) {
            $this->classReports[$judge][$section][$classIndex] = array();
        }
    }

    public function addPlacementReport(string $judge, string $section, int $classIndex, int $placement, string $varietyName, string $userName, ?string $gender, string $comment): void
    {
        $this->initializeClassReportIfNotSet($judge, $section, $classIndex);

        $placementReport = array();
        $placementReport['userName'] = $userName;
        $placementReport['placement'] = $placement;
        $placementReport['varietyName'] = $varietyName;
        $placementReport['gender'] = $gender;
        $placementReport['comment'] = $comment;
        
        $this->classReports[$judge][$section][$classIndex]['placements'][$placement] = $placementReport;
    }

    public function addSectionChallengeReport(string $judge, string $section, int $challengeIndex, $challengeName, string $age, int $entryCount): void
    {
        $this->initializeSectionChallengeReportIfNotSet($judge, $section, $challengeIndex);

        $challengeReport = array();
        $challengeReport['challengeName'] = $challengeName;
        $challengeReport['age'] = $age;
        $challengeReport['entryCount'] = $entryCount;
        $challengeReport['placements'] = array();

        $this->challengeReports[$judge][$section][$challengeIndex] = $challengeReport;
    }

    public function addChallengePlacementReport(string $judge, string $section, int $challengeIndex, int $placement, string $userName, string $varietyName): void
    {
        $this->initializeClassReportIfNotSet($judge, $section, $challengeIndex);

        $placementReport = array();
        $placementReport['userName'] = $userName;
        $placementReport['placement'] = $placement;
        $placementReport['varietyName'] = $varietyName;
        
        $this->challengeReports[$judge][$section][$challengeIndex]['placements'][$placement] = $placementReport;
    }

    private function initializeSectionChallengeReportIfNotSet(string $judge, string $section, int $challengeIndex): void
    {
        if (!isset($this->classReports[$judge][$section][$challengeIndex])) {
            $this->challengeReports[$judge][$section][$challengeIndex] = array();
        }
    }

    public function addGrandChallengeReport(string $judge, int $challengeIndex,string $challengeName, string $age, int $entryCount): void
    {
        $challengeReport = array();
        $challengeReport['judgeName'] = $judge;
        $challengeReport['challengeName'] = $challengeName;
        $challengeReport['age'] = $age;
        $challengeReport['entryCount'] = $entryCount;
        $challengeReport['placements'] = array();

        $this->grandChallengeReport[$challengeIndex] = $challengeReport;
    }

    public function addGrandChallengePlacementReport(int $challengeIndex, int $placement, string $userName, string $varietyName): void
    {

        $placementReport = array();
        $placementReport['userName'] = $userName;
        $placementReport['placement'] = $placement;
        $placementReport['varietyName'] = $varietyName;
        
        $this->grandChallengeReport[$challengeIndex]['placements'][$placement] = $placementReport;
    }

    public function addJuniorClassReport(string $className, int $classIndex, string $age, int $entryCount, string $comment): void
    {
        $reportData = array();
        $reportData['className'] = $className;
        $reportData['age'] = $age;
        $reportData['entryCount'] = $entryCount;
        $reportData['comment'] = $comment;
        $reportData['placements'] = array();

        $this->juniorReport[$classIndex] = $reportData;
    }

    public function addJuniorPlacementReport(int $classIndex, int $placement, string $userName, ?string $gender, string $comment, string $className): void
    {
        $placementReport = array();
        $placementReport['userName'] = $userName;
        $placementReport['placement'] = $placement;
        $placementReport['varietyName'] = $className;
        $placementReport['gender'] = $gender;
        $placementReport['comment'] = $comment;
        
        $this->juniorReport[$classIndex]['placements'][$placement] = $placementReport;
    }
}