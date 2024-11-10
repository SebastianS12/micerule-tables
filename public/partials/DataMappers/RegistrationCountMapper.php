<?php

class RegistrationCountMapper{
    public static function mapRegistrationCountsToClassIndices(Collection &$classIndexCollection, Collection &$registrationCountCollection): void
    {
        ModelHydrator::mapAttribute(
            $classIndexCollection, 
            $registrationCountCollection, 
            "registrationCount", 
            "class_index", 
            "index_number", 
            "entry_count", 
            0
        );
    }

    public static function mapRegistrationCountsToChallengeIndices(Collection &$challengeIndexCollection, Collection &$registrationCountCollection): void
    {
        ModelHydrator::mapAttribute(
            $challengeIndexCollection, 
            $registrationCountCollection, 
            "registrationCount", 
            "challenge_index", 
            "index_number", 
            "entry_count", 
            0
        );
    }
}