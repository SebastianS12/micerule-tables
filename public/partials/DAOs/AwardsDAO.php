<?php

class AwardsPlacementDAO implements IPrintDAO{
    private $awardsTable;
    private $wpdb;
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->awardsTable = $this->wpdb->prefix."micerule_show_challenge_awards";
    }

    public function updatePrinted(int $id, bool $printed)
    {
        $this->wpdb->update($this->awardsTable, array('printed' => $printed), array('id' => $id));
    }
}