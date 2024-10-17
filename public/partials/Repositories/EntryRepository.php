<?php

class EntryRepository implements IRepository{
    public $eventPostID;

    public function __construct(int $eventPostID){
        $this->eventPostID = $eventPostID;
    }
    
    public function getAll(): Collection{
        global $wpdb;
        $query = QueryBuilder::create()
                                ->select([Table::ENTRIES->getAlias().".*"])
                                ->from(Table::ENTRIES)
                                ->join("INNER", Table::REGISTRATIONS_ORDER, [Table::ENTRIES], ["id"], ["registration_order_id"])
                                ->join("INNER", Table::REGISTRATIONS, [Table::REGISTRATIONS_ORDER], ["id"], ["registration_id"])
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->build();

        $entryQueryResults = $wpdb->get_results($query, ARRAY_A);
        // $entryQueryResults = $wpdb->get_results("SELECT id, ENTRIES.class_registration_id, registration_order, pen_number, variety_name, absent, added, moved
        //                                  FROM ".$wpdb->prefix."micerule_show_entries ENTRIES
        //                                  INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS
        //                                  ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
        //                                  WHERE event_post_id = ".$this->eventPostID, ARRAY_A);

        $collection = new Collection();
        foreach($entryQueryResults as $row){
            $entry = EntryModel::createWithID($row['id'], $row['registration_order_id'], $row['pen_number'], $row['variety_name'], $row['absent'], $row['added'], $row['moved']);
            $collection->add($entry);
        }

        return $collection;
    }

    public function getByID(int $entryId): EntryModel|null{
        global $wpdb;
        $query = QueryBuilder::create()
                                ->select(["*"])
                                ->from(Table::ENTRIES)
                                ->where(Table::ENTRIES->getAlias(), "id", "=", $entryId)
                                ->build();

        $entryData = $wpdb->get_row($query, ARRAY_A);

        return EntryModel::createWithID($entryData['id'], $entryData['registration_order_id'], $entryData['pen_number'], $entryData['variety_name'], $entryData['absent'], $entryData['added'], $entryData['moved']);
    }

    public function saveEntry(EntryModel $entry): void{
        global $wpdb;
        if(isset($entry->id)){
            $wpdb->update($wpdb->prefix.Table::ENTRIES->value, array("registration_order_id" => $entry->registrationOrderID, "pen_number" => $entry->penNumber, "variety_name" => $entry->varietyName, "absent" => $entry->absent, "added" => $entry->added, "moved" => $entry->moved), array("id" => $entry->id));
        }else{
            $wpdb->insert($wpdb->prefix.Table::ENTRIES->value, array("registration_order_id" => $entry->registrationOrderID, "pen_number" => $entry->penNumber, "variety_name" => $entry->varietyName, "absent" => $entry->absent, "added" => $entry->added, "moved" => $entry->moved));
        }
    }

    public function deleteEntry(int $entryId): void
    {
        global $wpdb;
        $wpdb->delete($wpdb->prefix.Table::ENTRIES->value, array("id" => $entryId));
    }
}