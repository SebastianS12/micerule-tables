<?php

class QueryJoinSub implements IQueryCombine{
    private string $joinType;
    private string $subTable;
    private string $alias;
    private array $onTableAliases;
    private array $joinAttributes;
    private array $onAttributes;

    public function __construct(string $joinType, string $subTable, string $alias, array $onTableAliases, array $joinAttributes, array $onAttributes)
    {
        $this->joinType = $joinType;
        $this->subTable = $subTable;
        $this->alias = $alias;
        $this->onTableAliases = $onTableAliases;
        $this->joinAttributes = $joinAttributes;
        $this->onAttributes = $onAttributes;
    }

    public function getQueryString(): string{
        if(count($this->onTableAliases) != count($this->joinAttributes) || count($this->onTableAliases) != count($this->onAttributes)){
            throw new InvalidArgumentException("Number of Join Arguments does not match.");
        }

        $queryString = $this->joinType." JOIN ";
        $queryString .= "(".$this->subTable.") ".$this->alias." ";
        $queryString .= "ON ";
        for($i = 0; $i < count($this->joinAttributes); $i++){
            $queryString .= $this->alias.".".$this->joinAttributes[$i]." = ".$this->onTableAliases[$i].".".$this->onAttributes[$i]." ";
            if($i < count($this->joinAttributes) - 1){
                $queryString .= "AND ";
            }
        }

        return $queryString;
    }
}