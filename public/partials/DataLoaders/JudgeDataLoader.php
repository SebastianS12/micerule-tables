<?php

class JudgeDataLoader extends AbstractDataLoader{
    public function withSections(JudgesSectionsRepository $judgesSectionsRepository): void
    {
        $this->collection = $this->collection->with([JudgeSectionModel::class], ['id'], ['judge_id'], [$judgesSectionsRepository]);
    }

    public function withComments(GeneralCommentRepository $generalCommentRepository): void
    {
        $this->collection = $this->collection->with([GeneralComment::class], ['id'], ['judge_id'], [$generalCommentRepository]);
    }
}