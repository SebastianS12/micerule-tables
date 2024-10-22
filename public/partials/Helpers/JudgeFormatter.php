<?php

class JudgeFormatter
{
    public static function getJudgesString(Collection $judgeCollection): string
    {
        $judgesString = "";

        foreach($judgeCollection as $judgeModel){
            $judgesString .= $judgeModel->judgeName . ", ";
        }

        return rtrim($judgesString, ", ");
    }
}
