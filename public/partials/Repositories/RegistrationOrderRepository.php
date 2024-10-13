<?php

class RegistrationOrderRepository implements IRepository{
    private int $eventPostID;
    public function __construct(int $eventPostID)
    {
        $this->eventPostID = $eventPostID;
    }

    public function getAll(): Collection
    {
        $query = QueryBuilder::create()
                                ->select([Table::REGISTRATIONS_ORDER->getAlias().".*"])
                                ->from(Table::REGISTRATIONS_ORDER)
                                ->join("INNER", Table::REGISTRATIONS, [Table::REGISTRATIONS_ORDER], ["id"], ["registration_id"])
                                ->where(Table::REGISTRATIONS->getAlias(), "event_post_id", "=", $this->eventPostID)
                                ->orderBy(Table::REGISTRATIONS_ORDER->getAlias(), "created_at")
                                ->build();

        global $wpdb;
        $registrationOrderQueryResults = $wpdb->get_results($query, OBJECT);
        $collection = new Collection();
        foreach($registrationOrderQueryResults as $registrationOrderRow){
            $registrationOrder = RegistrationOrderModel::createWithID($registrationOrderRow->id, $registrationOrderRow->registration_id, $registrationOrderRow->created_at);
            $collection->add($registrationOrder);
        }

        return $collection;
    }

    public function addRegistration(int $registrationID, string $createdAt): int{
        global $wpdb;
        $wpdb->insert($wpdb->prefix.Table::REGISTRATIONS_ORDER->value, array("registration_id" => $registrationID, "created_at" => $createdAt));
        return $wpdb->insert_id;
    }

    public function addJuniorRegistration(int $registrationOrderID): void{
        global $wpdb;
        $wpdb->insert($wpdb->prefix.Table::REGISTRATIONS_JUNIOR->value, array("id" => $registrationOrderID));
    }

    public function removeRegistration(int $registrationOrderID): void{
        global $wpdb;
        $wpdb->delete($wpdb->prefix.Table::REGISTRATIONS_ORDER->value, array("id" => $registrationOrderID));
    }
}