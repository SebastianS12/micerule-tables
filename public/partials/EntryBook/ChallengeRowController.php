<?php

class ChallengeRowController{
    private $challengeRowService;

    public function __construct(ChallengeRowService $challengeRowService)
    {
        $this->challengeRowService = $challengeRowService;
    }

    public function prepareChallengeRowData(string $challengeName, Prize $prize): array{
        return $this->challengeRowService->getChallengeRowData($challengeName, $prize);
    }

    public function editAwards(int $prizeID, int $bisChallengeIndexID, int $boaChallengeIndexID){
        $this->challengeRowService->editAwards($prizeID, $bisChallengeIndexID, $boaChallengeIndexID);
    }
}