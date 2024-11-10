<?php

class ShowClassDataLoader extends AbstractDataLoader{
    public function withEntries(ClassIndexRepository $classIndexRepository, UserRegistrationsRepository $registrationsRepository, RegistrationOrderRepository $registrationOrderRepository, EntryRepository $entryRepository): void
    {
        if(!isset($this->collection)){
            throw new UnexpectedValueException("Collection is not loaded");
        }

        $this->collection->with(
            [ClassIndexModel::class, UserRegistrationModel::class, RegistrationOrderModel::class, EntryModel::class],
            ["id", "id", "id", "id"],
            ["class_id", "class_index_id", "registration_id", "registration_order_id"],
            [$classIndexRepository, $registrationsRepository, $registrationOrderRepository, $entryRepository]
        );
    }

    public function withClassPlacements(PlacementsRepository $classPlacementsRepository): void
    {
        if(!isset($this->collection)){
            throw new UnexpectedValueException("Collection is not loaded");
        }

        $this->collection->{ClassIndexModel::class}->with(
            [ClassPlacementModel::class], 
            ["id"], 
            ["index_id"], 
            [$classPlacementsRepository]
        );
    }

    public function withClassComments(ClassCommentsRepository $classCommentsRepository): void
    {
        if(!isset($this->collection)){
            throw new UnexpectedValueException("Collection is not loaded");
        }

        $this->collection->{ClassIndexModel::class}->with(
            [ClassComment::class], 
            ['id'], 
            ['class_index_id'], 
            [$classCommentsRepository]
        );
    }

    public function withClassPlacementReports(PlacementReportsRepository $placementReportsRepository): void
    {
        if(!isset($this->collection)){
            throw new UnexpectedValueException("Collection is not loaded");
        }

        $this->collection->{ClassIndexModel::class}->{ClassPlacementModel::class}->with(
            [PlacementReport::class], 
            ['id'], 
            ['placement_id'], 
            [$placementReportsRepository]
        );
    }
}