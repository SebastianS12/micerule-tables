<?php

class ClassPlacementDAO implements IPlacementDAO, IPrintDAO{
    private $placementsTable;
    private $wpdb;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->placementsTable = $this->wpdb->prefix."micerule_show_class_placements";
    }

    public function getAll(int $eventPostID): array|null
    {
        $query = QueryBuilder::create()
                                ->select([Table::CLASS_PLACEMENTS->getAlias().".*"])
                                ->from(Table::REGISTRATIONS)
                                ->join("INNER", Table::REGISTRATIONS_ORDER, [Table::REGISTRATIONS], ["registration_id"], ["id"])
                                ->join("INNER", Table::ENTRIES, [Table::REGISTRATIONS_ORDER], ["registration_order_id"], ["id"])
                                ->join("INNER", Table::CLASS_PLACEMENTS, [Table::ENTRIES], ["entry_id"], ["id"])
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $eventPostID)
                                ->build();

        // return $this->wpdb->get_results("SELECT PLACEMENTS.id, entry_id, index_id, placement, printed FROM ".$this->wpdb->prefix."micerule_show_user_registrations REGISTRATIONS 
        //                                 INNER JOIN ".$this->wpdb->prefix."micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
        //                                 INNER JOIN ".$this->placementsTable." PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id 
        //                                 WHERE event_post_id = ".$eventPostID." AND index_id = ".$indexID." 
        //                                 ORDER BY placement", ARRAY_A);
        return $this->wpdb->get_results($query, ARRAY_A);
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

    public function getAll(int $eventPostID): array|null
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
        $this->placementsTable = $this->wpdb->prefix.Table::CHALLENGE_PLACEMENTS->value;
    }

    public function getAll(int $eventPostID): array|null
    {
        $query = QueryBuilder::create()
                                ->select([Table::CLASS_PLACEMENTS->getAlias().".*"])
                                ->from(Table::REGISTRATIONS)
                                ->join("INNER", Table::REGISTRATIONS_ORDER, [Table::REGISTRATIONS], ["registration_id"], ["id"])
                                ->join("INNER", Table::ENTRIES, [Table::REGISTRATIONS_ORDER], ["registration_order_id"], ["id"])
                                ->join("INNER", Table::CLASS_PLACEMENTS, [Table::ENTRIES], ["entry_id"], ["id"])
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $eventPostID)
                                ->build();

        // return $this->wpdb->get_results("SELECT PLACEMENTS.id, entry_id, index_id, placement, printed FROM ".$this->wpdb->prefix."micerule_show_user_registrations REGISTRATIONS 
        //                                 INNER JOIN ".$this->wpdb->prefix."micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id 
        //                                 INNER JOIN ".$this->placementsTable." PLACEMENTS ON ENTRIES.id = PLACEMENTS.entry_id 
        //                                 WHERE event_post_id = ".$eventPostID." AND index_id = ".$indexID." 
        //                                 ORDER BY placement", ARRAY_A);
        return $this->wpdb->get_results($query, ARRAY_A);
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