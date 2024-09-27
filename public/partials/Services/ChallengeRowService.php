<?php

class ChallengeRowService{
    private $eventPostID;
    private $challengePlacementsRepository;
    private $challengeIndexRepository;
    private $entryRepository;
    private $registrationsRepository;
    private $awardsRepository;

    public function __construct(int $eventPostID, PlacementsRepository $challengePlacementsRepository, ChallengeIndexRepository $challengeModelRepository, EntryRepository $entryRepository, UserRegistrationsRepository $registrationsRepository, AwardsRepository $awardsRepository)
    {
        $this->eventPostID = $eventPostID;
        $this->challengePlacementsRepository = $challengePlacementsRepository;
        $this->challengeIndexRepository = $challengeModelRepository;
        $this->entryRepository = $entryRepository;
        $this->registrationsRepository = $registrationsRepository;
        $this->awardsRepository = $awardsRepository;
    }

    public function getChallengeRowData(string $challengeName, Prize $prize): array{
        $adChallengeIndexModel = $this->challengeIndexRepository->getChallengeIndexModel($challengeName, "Ad");
        $adChallengePlacements = $this->challengePlacementsRepository->getAllPlacements($this->eventPostID, $adChallengeIndexModel->id);
        $u8ChallengeIndexModel = $this->challengeIndexRepository->getChallengeIndexModel($challengeName, "U8");
        $u8ChallengePlacements = $this->challengePlacementsRepository->getAllPlacements($this->eventPostID, $u8ChallengeIndexModel->id);
        $challengeRowData = array();
        $challengeRowData['showBIS'] = (PlacementsService::placementExists($adChallengePlacements, 1) && PlacementsService::placementExists($u8ChallengePlacements, 1)) ? "flex" : "none";
        $challengeRowData['prizeID'] = $prize->value;
        $challengeRowData['ad'] = $this->getChallengeRowAgeData($adChallengeIndexModel, $u8ChallengeIndexModel,"Ad", $adChallengePlacements);
        $challengeRowData['u8'] = $this->getChallengeRowAgeData($u8ChallengeIndexModel, $adChallengeIndexModel,"U8", $u8ChallengePlacements);

        return $challengeRowData;
    }

    private function getChallengeRowAgeData(ChallengeIndexModel $challengeIndexModel, ChallengeIndexModel $oaChallengeIndexModel, string $age, array $placements): array{
        $data = array();
        $data['age'] = $age;
        $data['challengeIndexID'] = $challengeIndexModel->id;
        $data['oaChallengeIndexID'] = $oaChallengeIndexModel->id;
        $data['challengeIndex'] = $challengeIndexModel->challengeIndex;
        $data['challengeName'] = $challengeIndexModel->challengeName;
        $data['section'] = $challengeIndexModel->section;
        $award = $this->getAward($placements);
        $data['bisChecked'] = ($award == "BIS" || $award == "BOA") ? "checked" : "";
        $data['bisDisabled'] = ($award == "BOA") ? "disabled" : "";
        $data['placements'] = array();
        foreach($placements as $placementModel){
            $placementData = array();
            $entry = $this->entryRepository->getByID($placementModel->entryID);
            $userRegistration = $this->registrationsRepository->getByID($entry->classRegistrationID);
            $placementData['penNumber'] = $entry->penNumber;
            $placementData['userName'] = $userRegistration->userName;
            $placementData['varietyName'] = $entry->varietyName;
            $placementData['placement'] = $placementModel->placement;

            array_push($data['placements'], $placementData);
        }

        return $data;
    }

    private function getAward(array $placements){
        $awardData = array();
        if(PlacementsService::placementExists($placements, 1)){
            $awardData = $this->awardsRepository->getByPlacementID($placements[1]->id);
        }
        $award = (isset($awardData['award'])) ? $awardData['award'] : "";

        return $award;
    }

    public function editAwards(int $prizeID, int $bisChallengeIndexID, int $boaChallengeIndexID){
        $bisChallengePlacements = $this->challengePlacementsRepository->getAllPlacements($this->eventPostID, $bisChallengeIndexID);
        $boaChallengePlacements = $this->challengePlacementsRepository->getAllPlacements($this->eventPostID, $boaChallengeIndexID);

        $this->addOrRemoveAward($prizeID, $bisChallengePlacements[1]->id, "BIS");
        $this->addOrRemoveAward($prizeID, $boaChallengePlacements[1]->id, "BOA");
    }

    private function addOrRemoveAward(int $prizeID, int $placementID, string $award){
        $awardData = $this->awardsRepository->getByPlacementID($placementID);
        if(isset($awardData)){
            $this->awardsRepository->removeAward($awardData['id']);
        }else{
            $this->awardsRepository->addAward($prizeID, $placementID, $award);
        }
    }
}