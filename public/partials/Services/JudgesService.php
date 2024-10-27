<?php

class JudgesService{
    private JudgesRepository $judgesRepository;
    private JudgesSectionsRepository $judgesSectionsRepository;

    public function __construct(JudgesRepository $judgesRepository, JudgesSectionsRepository $judgesSectionsRepository)
    {
        $this->judgesRepository = $judgesRepository;
        $this->judgesSectionsRepository = $judgesSectionsRepository;
    }

    public function saveEventJudges(int $eventPostID, ?array $eventJudgesData): void
    {
        if(!isset($eventJudgesData)) return;

        $judgeCollection = $this->judgesRepository->getAll()->with(['sections'], ['id'], ['judge_id'], [$this->judgesSectionsRepository])->groupByUniqueKey("judge_no");

        foreach ($eventJudgesData as $judgeNo => $judgeData) {
            $judgeModel = $judgeCollection[$judgeNo];
            if (isset($judgeData['name']) && $judgeData['name'] != ""){
                if(!isset($judgeModel)){
                    $judgeModel = JudgeModel::create($eventPostID, $judgeNo, $judgeData['name']);
                }else{
                    $judgeModel->judge_name = $judgeData['name'];
                }
                $this->saveJudgeData($judgeModel, $judgeData);
            }else{
                if(isset($judgeModel)){
                    $this->judgesRepository->remove($judgeModel);
                }
            }
        }
    }

    private function saveJudgeData(JudgeModel $judgeModel, array $judgeData)
    {
            $judgeId = $this->judgesRepository->save($judgeModel);

            $judgeSections = $judgeModel->sections()->groupByUniqueKey("section");
            foreach(EventProperties::SECTIONNAMES as $sectionName){
                $judgeSectionModel = $judgeSections[strtolower($sectionName)];
                if(isset($judgeData['sections'][strtolower($sectionName)]) && $judgeData['sections'][strtolower($sectionName)] == "on"){
                    if(!isset($judgeSectionModel)){
                        $judgeSectionModel = JudgeSectionModel::create($judgeId, strtolower($sectionName));
                    }
                    $this->judgesSectionsRepository->save($judgeSectionModel);
                }else{
                    if(isset($judgeSectionModel)){
                        $this->judgesSectionsRepository->remove($judgeSectionModel);
                    }
                }
            }
        
            // if (isset($judgeData['partnership']) && $judgeData['partnership'] != "")
            //     self::saveJudgePartner($eventPostID, $judgeNo, $judgeData['partnership']);
            // else
            //     self::deleteJudgePartner($eventPostID, $judgeNo);
    }

    public function getJudgesString(Collection $judgeCollection): string
    {
        $judgesString = "";

        foreach($judgeCollection as $judgeModel){
            $judgesString .= $judgeModel->judge_name.", ";
        }
        rtrim($judgesString, ", ");

        return $judgesString;
    }
}