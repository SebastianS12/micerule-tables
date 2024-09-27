<?php

class UserRegistrationsRepository{
    public $eventPostID;

    public function __construct(int $eventPostID){
        $this->eventPostID = $eventPostID;
    }

    public function getAll(): array{
        $userRegistrations = array();

        global $wpdb;
        $userRegistrationData = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."micerule_show_user_registrations
                                                    WHERE event_post_id = ".$this->eventPostID, ARRAY_A);

        foreach($userRegistrationData as $row){
            $userRegistrations[$row['class_registration_id']] = UserRegistrationModel::createWithID($row['class_registration_id'], $row['event_post_id'], $row['user_name'], $row['class_id'], $row['age']);
        }

        return $userRegistrations;
    }

    public function getByID(int $id): UserRegistrationModel{
        global $wpdb;
        $userRegistrationData = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."micerule_show_user_registrations
                                                 WHERE class_registration_id = ".$id, ARRAY_A);

        return UserRegistrationModel::createWithID($userRegistrationData['class_registration_id'], $userRegistrationData['event_post_id'], $userRegistrationData['user_name'], $userRegistrationData['class_id'], $userRegistrationData['age']);
    }
}