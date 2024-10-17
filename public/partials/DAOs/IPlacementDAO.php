<?php

interface IPlacementDAO{
    public function getAll(int $eventPostID): array|null;
    public function getByIndexID(int $indexID): array|null;
    public function getByID(int $id);
    public function add(int $placement, int $indexID, int $entryID, Prize $prize);
    public function remove(int $id);
}