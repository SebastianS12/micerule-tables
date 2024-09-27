<?php

abstract class Placements
{
    public $placements;

    public function __construct($eventPostID, $age, $classCondition = "")
    {
        $this->placements = array(1 => null, 2 => null, 3 => null);
        $this->loadClassPlacementData($eventPostID, $age, $classCondition);
    }

    private function loadClassPlacementData($eventPostID, $className, $age)
    {
        $this->placements[1] = $this->getPlacement($eventPostID, $className, $age, 1);
        $this->placements[2] = $this->getPlacement($eventPostID, $className, $age, 2);
        $this->placements[3] = $this->getPlacement($eventPostID, $className, $age, 3);
    }

    public function entryHasPlacement($placement, $entryID)
    {
        return ($this->placements[$placement] != null && $this->placements[$placement] == $entryID);
    }

    public function entryInPlacements($entryID)
    {
        return in_array($entryID, $this->placements);
    }

    public function showPlacementCheck($checkPlacement, $entryID)
    {
        $showPlacement = $this->entryHasPlacement($checkPlacement, $entryID) || $this->placements[$checkPlacement] == null;
        foreach ($this->placements as $placement => $placementEntryID) {
            if ($placement != $checkPlacement)
                $showPlacement = $showPlacement && !$this->entryHasPlacement($placement, $entryID);
        }

        return $showPlacement;
    }

    public function higherPlacementEntryIsInSameClass($higherPlacement, $entryID)
    {
        $entrySameClass = false;
        if (isset($this->placements[$higherPlacement]))
            $entrySameClass = $this->getClassName($this->placements[$higherPlacement]) == $this->getClassName($entryID);

        return $entrySameClass;
    }

    public function isPlacementChecked($placement)
    {
        return isset($this->placements[$placement]);
    }

    public function noEntriesChecked(){
        return (!isset($this->placements[1]) && !isset($this->placements[2]) && !isset($this->placements[3]));
    }

    abstract protected function getPlacement($eventPostID, $age, $classCondition, $placement);
    abstract protected function getClassName($entryID);
}

class ClassPlacements extends Placements
{
    protected function getPlacement($eventPostID, $age, $classCondition, $placement)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT entry_id FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_class_placements PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                               WHERE event_post_id = " . $eventPostID . " AND class_name = '" . $classCondition . "' AND age = '" . $age . "' AND placement = " . $placement);
    }

    protected function getClassName($entryID)
    {
        return ""; //not needed
    }
}

class JuniorPlacements extends Placements
{
    protected function getPlacement($eventPostID, $age, $classCondition, $placement)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT entry_id FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_junior_placements PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id 
                               WHERE event_post_id = " . $eventPostID . " AND placement = " . $placement);
    }

    protected function getClassName($entryID)
    {
        return ""; //not needed
    }
}

class SectionPlacements extends Placements
{
    protected function getPlacement($eventPostID, $age, $classCondition, $placement)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT entry_id FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_section_placements PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id
                               INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                               WHERE event_post_id = " . $eventPostID . " AND section= '" . $classCondition . "' AND age = '" . $age . "' AND placement = " . $placement);
    }

    //TODO: Move to ShowEntry?
    protected function getClassName($entryID)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT class_name FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS
                               INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id
                               WHERE ENTRIES.id = " . $entryID);
    }
}

class GrandChallengePlacements extends Placements
{
    protected function getPlacement($eventPostID, $age, $classCondition, $placement)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT entry_id FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_grand_challenge_placements PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id
                               INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                               WHERE event_post_id = " . $eventPostID . " AND section != 'optional' AND age = '" . $age . "' AND placement = " . $placement);
    }

    //TODO: Move to ShowEntry?
    protected function getClassName($entryID)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT section FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id
                               INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_id = CLASSES.id
                               WHERE ENTRIES.id = " . $entryID);
    }
}


class PlacementModel{
    public $id;
    public $entryID;
    public $indexID;
    public $placement;
    public $printed;

    protected function __construct($entryID, $indexID, $placement, $printed){
        $this->entryID = $entryID;
        $this->indexID = $indexID;
        $this->placement = $placement;
        $this->printed = $printed;
    }

    public static function create($entryID, $indexID, $placement, $printed){
        $instance = new self($entryID, $indexID, $placement, $printed);
        return $instance;
    }

    public static function createWithID($id, $entryID, $indexID, $placement, $printed){
        $instance = new self($entryID, $indexID, $placement, $printed);
        $instance->id = $id;
        return $instance;
    }
}

class PlacementsRepository{
    private $placementsDAO;
    public function __construct(IPlacementDAO $placementsDAO)
    {
        $this->placementsDAO = $placementsDAO;
    }
    public function getAllPlacements(int $eventPostID, int $indexID)
    {
        $placementData = $this->placementsDAO->getAll($eventPostID, $indexID);

        $placements = array();
        foreach($placementData as $row){
            $placements[$row['placement']] = PlacementModel::createWithID($row['id'], $row['entry_id'], $row['index_id'], $row['placement'], $row['printed']);
        }

        return $placements;
    }

    public function getByID(int $placementID)
    {
        $placementData = $this->placementsDAO->getByID($placementID);
        if(isset($placementData))
            return PlacementModel::createWithID($placementID, $placementData['entry_id'], $placementData['index_id'], $placementData['placement'], $placementData['printed']);
    }

    public function addPlacement(int $placement, int $indexID, int $entryID, Prize $prize){
        $this->placementsDAO->add($placement, $indexID, $entryID, $prize);
    }

    public function removePlacement(int $id){
        $this->placementsDAO->remove($id);
    }
}

interface IPlacementDAO{
    public function getAll(int $eventPostID, int $indexID);
    public function getByID(int $id);
    public function add(int $placement, int $indexID, int $entryID, Prize $prize);
    public function remove(int $id);
}

class ClassPlacementDAO implements IPlacementDAO, IPrintDAO{
    private $placementsTable;
    private $wpdb;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->placementsTable = $this->wpdb->prefix."micerule_show_class_placements";
    }

    public function getAll(int $eventPostID, int $indexID)
    {
        return $this->wpdb->get_results("SELECT PLACEMENTS.id, entry_id, index_id, placement, printed FROM ".$this->wpdb->prefix."micerule_show_user_registrations REGISTRATIONS 
                                        INNER JOIN ".$this->wpdb->prefix."micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                                        INNER JOIN ".$this->placementsTable." PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id 
                                        WHERE event_post_id = ".$eventPostID." AND index_id = ".$indexID." 
                                        ORDER BY placement", ARRAY_A);
    }

    public function getByID(int $id)
    {
        return $this->wpdb->get_row("SELECT * FROM ".$this->placementsTable." WHERE id = ".$id, ARRAY_A);
    }

    public function add(int $placement, int $indexID, int $entryID, Prize $prize)
    {
        $this->wpdb->insert($this->placementsTable, array('entry_id' => $entryID, 'index_id' => $indexID, 'prize' => $prize->value,'placement' => $placement, 'printed' => false));
    }

    public function remove(int $id)
    {
        $this->wpdb->delete($this->placementsTable, array('id' => $id));
    }

    public function updatePrinted(int $id, bool $printed)
    {
        $this->wpdb->update($this->placementsTable, array('printed' => $printed), array('id' => $id));
    }
}

class JuniorPlacementDAO implements IPlacementDAO{
    private $placementsTable;
    private $wpdb;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->placementsTable = $this->wpdb->prefix."micerule_show_junior_placements";
    }

    public function getAll(int $eventPostID, int $indexID)
    {
        return $this->wpdb->get_results("SELECT PLACEMENTS.id, entry_id, index_id, placement, printed FROM ".$this->wpdb->prefix."micerule_show_user_registrations REGISTRATIONS 
                                        INNER JOIN ".$this->wpdb->prefix."micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                                        INNER JOIN ".$this->placementsTable." PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id 
                                        WHERE event_post_id = ".$eventPostID." AND index_id = ".$indexID." 
                                        ORDER BY placement", ARRAY_A);
    }

    public function getByID(int $id)
    {
        return $this->wpdb->get_row("SELECT * FROM ".$this->placementsTable." WHERE id = ".$id, ARRAY_A);
    }

    public function add(int $placement, int $indexID, int $entryID, Prize $prize)
    {
        $this->wpdb->insert($this->placementsTable, array('entry_id' => $entryID, 'index_id' => $indexID, 'placement' => $placement, 'printed' => false));
    }

    public function remove(int $id)
    {
        $this->wpdb->delete($this->placementsTable, array('id' => $id));
    }
}

class ChallengePlacementDAO implements IPlacementDAO, IPrintDAO{
    private $placementsTable;
    private $wpdb;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->placementsTable = $this->wpdb->prefix."micerule_show_challenge_placements";
    }

    public function getAll(int $eventPostID, int $indexID)
    {
        return $this->wpdb->get_results("SELECT PLACEMENTS.id, entry_id, index_id, placement, printed FROM ".$this->wpdb->prefix."micerule_show_user_registrations REGISTRATIONS 
                                        INNER JOIN ".$this->wpdb->prefix."micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
                                        INNER JOIN ".$this->placementsTable." PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id 
                                        WHERE event_post_id = ".$eventPostID." AND index_id = ".$indexID." 
                                        ORDER BY placement", ARRAY_A);
    }

    public function getByID(int $id)
    {
        return $this->wpdb->get_row("SELECT * FROM ".$this->placementsTable." WHERE id = ".$id, ARRAY_A);
    }

    public function add(int $placement, int $indexID, int $entryID, Prize $prize)
    {
        $this->wpdb->insert($this->placementsTable, array('entry_id' => $entryID, 'index_id' => $indexID, 'prize'=> $prize->value, 'placement' => $placement, 'printed' => false));
    }

    public function remove(int $id)
    {
        $this->wpdb->delete($this->placementsTable, array('id' => $id));
    }

    public function updatePrinted(int $id, bool $printed)
    {
        $this->wpdb->update($this->placementsTable, array('printed' => $printed), array('id' => $id));
    }
}
