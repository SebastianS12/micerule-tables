<?php

class JudgesService{
    private JudgesRepository $judgesRepository;

    public function __construct(JudgesRepository $judgesRepository)
    {
        $this->judgesRepository = $judgesRepository;
    }

    public function getJudgesNames(int $eventPostID): array{
        $judgesNames = array();
        foreach($this->judgesRepository->getAll($eventPostID) as $judgeData){
            $judgesNames[] = $judgeData['judge_name'];
        }

        return array_unique($judgesNames);
    }

    public function getJudgesNamesString(int $eventPostID): string{
        $judgesNames = $this->getJudgesNames($eventPostID);
        return implode(", ", $judgesNames);
    }
}