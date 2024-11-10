<?php

class JudgeMapper{
    public static function mapJudgesSectionsToClasses(Collection &$classCollection, Collection &$judgeSectionCollection): void
    {
        ModelHydrator::mapExistingCollections(
            $classCollection, 
            $judgeSectionCollection,
            JudgeSectionModel::class, 
            "section", 
            "section"
        );
    }
}