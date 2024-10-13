<?php

class UserRegistrationsRepository implements IRepository{
    public $eventPostID;

    public function __construct(int $eventPostID){
        $this->eventPostID = $eventPostID;
    }

    public function getAll(): Collection
    {
        global $wpdb;
        $userRegistrationData = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."micerule_show_user_registrations
                                                    WHERE event_post_id = ".$this->eventPostID, ARRAY_A);

        $collection = new Collection();                                            
        foreach($userRegistrationData as $row){
            $userRegistrationModel = UserRegistrationModel::createWithID($row['id'], $row['event_post_id'], $row['user_name'], $row['class_index_id']);
            $collection->add($userRegistrationModel);
        }

        return $collection;
    }

    public function getUserRegistrations(string $userName): Collection
    {
        $query = QueryBuilder::create()
                                ->select(["*"])
                                ->from(Table::REGISTRATIONS)
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->where(Table::REGISTRATIONS->getAlias(), "user_name", "=", $userName)
                                ->build();
        global $wpdb;
        $userRegistrationsQueryResults = $wpdb->get_results($query, OBJECT);

        $collection = new Collection();
        foreach($userRegistrationsQueryResults as $row){
            $userRegistrationModel = UserRegistrationModel::createWithID($row->id, $row->event_post_id, $row->user_name, $row->class_index_id);
            $collection->add($userRegistrationModel);
        }

        return $collection;
    }

    public function addRegistration(int $eventPostID, string $userName, int $indexID): int{
        global $wpdb;
        $wpdb->insert($wpdb->prefix.Table::REGISTRATIONS->value, array('event_post_id' =>$eventPostID, 'user_name' => $userName, 'class_index_id' => $indexID));
        return $wpdb->insert_id;
    }

    public function removeRegistration(int $id): void{
        global $wpdb;
        $wpdb->delete($wpdb->prefix.Table::REGISTRATIONS->value, array("id" => $id));
    }
}