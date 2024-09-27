<?php

class EntryRepository{
    public $eventPostID;

    public function __construct(int $eventPostID){
        $this->eventPostID = $eventPostID;
    }
    
    public function getAll(): array{
        $entries = array();

        global $wpdb;
        $entryData = $wpdb->get_results("SELECT id, ENTRIES.class_registration_id, registration_order, pen_number, variety_name, absent, added, moved
                                         FROM ".$wpdb->prefix."micerule_show_entries ENTRIES
                                         INNER JOIN ".$wpdb->prefix."micerule_show_user_registrations REGISTRATIONS
                                         ON ENTRIES.class_registration_id = REGISTRATIONS.class_registration_id
                                         WHERE event_post_id = ".$this->eventPostID, ARRAY_A);

        foreach($entryData as $entryRow){
            $entries[$entryRow['id']] = EntryModel::createWithID($entryRow['id'], $entryRow['class_registration_id'], $entryRow['registration_order'], $entryRow['pen_number'], $entryRow['variety_name'], $entryRow['absent'], $entryRow['added'], $entryRow['moved']);
        }

        return $entries;
    }

    public function getByID($id): EntryModel{
        global $wpdb;
        $entryData = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix."micerule_show_entries
                                         WHERE id = ".$id, ARRAY_A);

        return EntryModel::createWithID($entryData['id'], $entryData['class_registration_id'], $entryData['registration_order'], $entryData['pen_number'], $entryData['variety_name'], $entryData['absent'], $entryData['added'], $entryData['moved']);
    }
}