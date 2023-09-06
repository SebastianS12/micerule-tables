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
                               INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_name = CLASSES.class_name AND REGISTRATIONS.location_id = CLASSES.location_id
                               WHERE event_post_id = " . $eventPostID . " AND section= '" . $classCondition . "' AND age = '" . $age . "' AND placement = " . $placement);
    }

    //TODO: Move to ShowEntry?
    protected function getClassName($entryID)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT class_name FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
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
                               INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_name = CLASSES.class_name AND REGISTRATIONS.location_id = CLASSES.location_id
                               WHERE event_post_id = " . $eventPostID . " AND section != 'optional' AND age = '" . $age . "' AND placement = " . $placement);
    }

    //TODO: Move to ShowEntry?
    protected function getClassName($entryID)
    {
        global $wpdb;
        return $wpdb->get_var("SELECT section FROM " . $wpdb->prefix . "micerule_show_user_registrations REGISTRATIONS 
                               INNER JOIN " . $wpdb->prefix . "micerule_show_entries ENTRIES ON REGISTRATIONS.class_registration_id = ENTRIES.class_registration_id
                               INNER JOIN " . $wpdb->prefix . "micerule_show_classes CLASSES ON REGISTRATIONS.class_name = CLASSES.class_name AND REGISTRATIONS.location_id = CLASSES.location_id
                               WHERE ENTRIES.id = " . $entryID);
    }
}
