<?php

class JudgeFormatter
{
    public static function getJudgesString(Collection $judgeCollection): string
    {
        $judgesString = "";

        foreach($judgeCollection as $judgeModel){
            $judgesString .= $judgeModel->judge_name . ", ";
        }

        return rtrim($judgesString, ", ");
    }

    public static function getJudgeName(EntryClassModel|ChallengeIndexModel $entryClassModel): string
    {
        $judgeSection = $entryClassModel->judgeSection();
        $judgeModel = (isset($judgeSection)) ? $judgeSection->judge() : null;
        $judgeName = isset($judgeModel) ? $judgeModel->judge_name : "";
        return $judgeName;
    }
}
