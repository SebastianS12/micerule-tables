<?php

interface IRowService{
    public function prepareRowData(ShowEntry $entry, RowPlacementData $rowPlacementData);
}