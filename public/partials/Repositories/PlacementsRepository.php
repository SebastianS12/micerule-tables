<?php

class PlacementsRepository implements IRepository{
    private int $eventPostID;
    private $placementsDAO;

    public function __construct(int $eventPostID, IPlacementDAO $placementsDAO)
    {
        $this->eventPostID = $eventPostID;
        $this->placementsDAO = $placementsDAO;
    }

    public function getAll(Closure|null $constraintsClosure = null): Collection
    {
        $placementsQueryResult = $this->placementsDAO->getAll($this->eventPostID);

        $collection = new Collection();
        foreach($placementsQueryResult as $row){
            $placementModel = PlacementModel::createWithID($row['id'], $row['entry_id'], $row['index_id'], $row['placement'], $row['prize'], $row['printed']);
            $collection->add($placementModel);
        }

        return $collection;
    }


    public function getIndexPlacements(int $indexID): Collection
    {
        $placementData = $this->placementsDAO->getByIndexID($indexID);

        $collection = new Collection();
        foreach($placementData as $row){
            $placementModel = PlacementModel::createWithID($row['id'], $row['entry_id'], $row['index_id'], $row['placement'], $row['prize'], $row['printed']);
            $collection->add($placementModel);
        }

        return $collection;
    }

    public function getByID(int $placementID)
    {
        $placementData = $this->placementsDAO->getByID($placementID);
        if(isset($placementData))
            return PlacementModel::createWithID($placementID, $placementData['entry_id'], $placementData['index_id'], $placementData['placement'], $placementData['prize'], $placementData['printed']);
    }

    public function addPlacement(int $placement, int $indexID, int $entryID, Prize $prize){
        $this->placementsDAO->add($placement, $indexID, $entryID, $prize);
    }

    public function removePlacement(int $id){
        $this->placementsDAO->remove($id);
    }
}