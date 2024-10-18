<?php

class JuniorRegistrationRepository implements IRepository{
    private int $eventPostID;

    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getAll(Closure|null $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::REGISTRATIONS_JUNIOR->getAlias().".*"])
                                ->from(Table::REGISTRATIONS_JUNIOR)
                                ->join("INNER", Table::REGISTRATIONS, [Table::REGISTRATIONS_JUNIOR], ["id"], ["registration_id"])
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->build();

        global $wpdb;
        $registrationsQueryResult = $wpdb->get_results($query, ARRAY_A);

        $collection = new Collection();
        foreach($registrationsQueryResult as $row){
            $juniorRegistrationModel = JuniorRegistrationModel::createWithID($row['id'], $row['registration_order_id'], $row['registration_id']);
            $collection->add($juniorRegistrationModel);
        }

        return $collection;
    }
}