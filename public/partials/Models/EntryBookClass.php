<?php

class EntryBookClass{
    private EntryClassModel $entryClassModel;
    private ClassIndexModel $indexModel;
    private array $entries;

    public function __construct(EntryClassModel $entryClassModel, ClassIndexModel $indexModel, array $entries)
    {
        $this->entryClassModel = $entryClassModel;
        $this->indexModel = $indexModel;
        $this->entries = array();
    }
}