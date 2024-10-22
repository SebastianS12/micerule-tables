<?php

class ChallengeRowService{
    private $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function prepareChallengeRowData(ChallengeIndexModel $adChallengeIndexModel, ChallengeIndexModel $u8ChallengeIndexModel, Collection $adPlacements, Collection $u8Placements, Prize $prize): array{
        $challengeRowData = array();
        $challengeRowData['showBIS'] = (PlacementsService::placementExists($adPlacements, 1) && PlacementsService::placementExists($u8Placements, 1)) ? "flex" : "none";
        $challengeRowData['prizeID'] = $prize->value;
        $challengeRowData['ad'] = $this->getChallengeRowAgeData($adChallengeIndexModel, $u8ChallengeIndexModel,"Ad", $adPlacements);
        $challengeRowData['u8'] = $this->getChallengeRowAgeData($u8ChallengeIndexModel, $adChallengeIndexModel,"U8", $u8Placements);

        return $challengeRowData;
    }

    private function getChallengeRowAgeData(ChallengeIndexModel $challengeIndexModel, ChallengeIndexModel $oaChallengeIndexModel, string $age, Collection $placements): array{
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
            $entryModel = $placementModel->entry();
            $userRegistrationModel = $placementModel->registration();
            $placementData['penNumber'] = $entryModel->penNumber;
            $placementData['userName'] = $userRegistrationModel->userName;
            $placementData['varietyName'] = $entryModel->varietyName;
            $placementData['placement'] = $placementModel->placement;

            array_push($data['placements'], $placementData);
        }

        return $data;
    }

    private function getAward(Collection $placements): string{
        if(PlacementsService::placementExists($placements, 1)){
            $firstPlacePlacement = $placements->get("placement", 1);
            $award = $firstPlacePlacement->award();
            if(isset($award)){
                return $award->award->value;
            }
        }

        return "";
    }

    public function editAwards(PlacementsRepository $challengePlacementsRepository, AwardsRepository $awardsRepository, int $prizeID, int $bisChallengeIndexID, int $boaChallengeIndexID){
        $challengePlacements = $challengePlacementsRepository->getAll()->with(["award"], ["id"], ["challenge_placement_id"], [$awardsRepository])->groupBy("indexID");
        $bisChallengePlacements = $challengePlacements[$bisChallengeIndexID]->groupByUniqueKey("placement");
        $boaChallengePlacements = $challengePlacements[$boaChallengeIndexID]->groupByUniqueKey("placement");

        if(isset($bisChallengePlacements[1])){
            $this->addOrRemoveAward($awardsRepository, Prize::from($prizeID)->getAward(), $bisChallengePlacements[1], Award::BIS);
        }
        if(isset($boaChallengePlacements[1])){
            $this->addOrRemoveAward($awardsRepository, Prize::from($prizeID)->getAward(), $boaChallengePlacements[1], Award::BOA);
        }
    }

    private function addOrRemoveAward(AwardsRepository $awardsRepository, int $prizeID, PlacementModel $placementModel, Award $award){
        $awardModel = $placementModel->award();
        if(isset($awardModel)){
            $awardsRepository->removeAward($awardModel->id);
        }else{
            $awardsRepository->addAward($prizeID, $placementModel->id, $award->value);
        }
    }
}