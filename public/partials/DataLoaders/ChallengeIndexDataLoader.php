<?php

class ChallengeIndexDataLoader extends AbstractDataLoader{
    public function withAwards(PlacementsRepository $placementsRepository, AwardsRepository $awardsRepository): void
    {
        $this->collection = $this->collection->with(
            [ChallengePlacementModel::class, AwardModel::class],
            ["id", "id"],
            ["index_id", "challenge_placement_id"],
            [$placementsRepository, $awardsRepository]
        );
    }
}