<?php

class RegistrationDataLoader extends AbstractDataLoader{
    public function withEntries(RegistrationOrderRepository $registrationOrderRepository, EntryRepository $entryRepository): void
    {
        if(!isset($this->collection)){
            throw new UnexpectedValueException("Collection is not loaded");
        }

        $this->collection->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class],
            ["id", "id"],
            ["registration_id", "registration_order_id"],
            [$registrationOrderRepository, $entryRepository]
        );
    }

    public function withClassPlacements(PlacementsRepository $classPlacementsRepository): void
    {
        if(!isset($this->collection)){
            throw new UnexpectedValueException("Collection is not loaded");
        }

        $this->collection->{RegistrationOrderModel::class}->{EntryModel::class}->with(
            [ClassPlacementModel::class], 
            ["id"], 
            ["entry_id"], 
            [$classPlacementsRepository]
        );
    }

    public function withChallengePlacements(PlacementsRepository $challengePlacementsRepository): void
    {
        if(!isset($this->collection)){
            throw new UnexpectedValueException("Collection is not loaded");
        }

        $this->collection->{RegistrationOrderModel::class}->{EntryModel::class}->with(
            [ChallengePlacementModel::class], 
            ["id"], 
            ["entry_id"], 
            [$challengePlacementsRepository]
        );
    }
}