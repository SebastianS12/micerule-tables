<?php

class QueryJoin implements IQueryCombine{
    private String $joinType;
    private Table $joinTable;
    private array $onTables;
    private array $joinAttributes;
    private array $onAttributes;

    public function __construct(string $joinType, Table $joinTable, array $onTables, array $joinAttributes, array $onAttributes)
    {
        $this->joinType = $joinType;
        $this->joinTable = $joinTable;
        $this->onTables = $onTables;
        $this->joinAttributes = $joinAttributes;
        $this->onAttributes = $onAttributes;
    }

    public function getQueryString(): string{
        global $wpdb;
        if(count($this->onTables) != count($this->joinAttributes) || count($this->onTables) != count($this->onAttributes)){
            throw new InvalidArgumentException("Number of Join Arguments does not match.");
        }

        $queryString = $this->joinType." JOIN ";
        $queryString .= $wpdb->prefix.$this->joinTable->value." ".$this->joinTable->getAlias()." ";
        $queryString .= "ON ";
        for($i = 0; $i < count($this->joinAttributes); $i++){
            $queryString .= $this->joinTable->getAlias().".".$this->joinAttributes[$i]." = ".$this->onTables[$i]->getAlias().".".$this->onAttributes[$i]." ";
            if($i < count($this->joinAttributes) - 1){
                $queryString .= "AND ";
            }
        }

        return $queryString;
    }
}