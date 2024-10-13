<?php

class PlacementsRepository implements IRepository{
    private int $eventPostID;
    private $placementsDAO;

    public function __construct(int $eventPostID, IPlacementDAO $placementsDAO)
    {
        $this->eventPostID = $eventPostID;
        $this->placementsDAO = $placementsDAO;
    }

    public function getAll(): Collection
    {
        $placementsQueryResult = $this->placementsDAO->getAll($this->eventPostID);

        $collection = new Collection();
        foreach($placementsQueryResult as $row){
            $placementModel = PlacementModel::createWithID($row['id'], $row['entry_id'], $row['index_id'], $row['placement'], $row['prize'], $row['printed']);
            $collection->add($placementModel);
        }

        return $collection;
    }


    public function getAllPlacements(int $eventPostID, int $indexID)
    {
        $placementData = $this->placementsDAO->getAll($eventPostID, $indexID);

        $placements = array();
        foreach($placementData as $row){
            $placements[$row['placement']] = PlacementModel::createWithID($row['id'], $row['entry_id'], $row['index_id'], $row['placement'], $row['prize'], $row['printed']);
        }

        return $placements;
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