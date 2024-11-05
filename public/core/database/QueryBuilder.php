<?php

class QueryBuilder {
    private array $select;
    private Table $from;
    private array $combines;
    private array $where;
    private array $groupBy;
    private array $orderBy;
    private int $limit;

    private function __construct()
    {
        $this->select = array();
        $this->combines = array();
        $this->where = array();
        $this->groupBy = array();
        $this->orderBy = array();
        $this->limit = 0;
    }

    public static function create(): QueryBuilder{
        return new self();
    }

    public function select(array $attributes): QueryBuilder{
        foreach($attributes as $attribute){
            $this->select[] = $attribute;
        }

        return $this;
    }

    public function from(Table $fromTable): QueryBuilder{
        $this->from = $fromTable;
        return $this;
    }

    public function join(string $joinType, Table $joinTable, array $onTables, array $joinAttributes, array $onAttributes): QueryBuilder{
        $this->combines[] = new QueryJoin($joinType, $joinTable, $onTables, $joinAttributes, $onAttributes);
        return $this;
    }

    public function joinSub(string $joinType, string $subTable, string $alias, array $onTableAliases, array $joinAttributes, array $onAttributes): QueryBuilder{
        $this->combines[] = new QueryJoinSub($joinType, $subTable, $alias, $onTableAliases, $joinAttributes, $onAttributes);
        return $this;
    }

    public function where(string $operand1TableAlias, string $operand1, string $operator, string $operand2, string $logicalOperator = "AND"): QueryBuilder{
        $this->where[] = new QueryWhere($operand1TableAlias, $operand1, $operator, $operand2, $logicalOperator);
        return $this;
    }

    public function whereNull(string $operand1TableAlias, string $operand1, string $logicalOperator = "AND"): QueryBuilder{
        $this->where[] = new QueryWhereNull($operand1TableAlias, $operand1, $logicalOperator);
        return $this;
    }

    public function whereNested(Closure $closure, string $logicalOperator = "AND"){
        $queryWhereNested = new QueryWhereNested($logicalOperator);
        $closure($queryWhereNested);
        $this->where[] = $queryWhereNested;
        return $this;
    }

    public function groupBy(string $attributeTableAlias, string $groupBy): QueryBuilder{
        $this->groupBy[] = $attributeTableAlias.".".$groupBy;
        return $this;
    }

    public function orderBy(string $attributeTableAlias, string $groupByAttribute, string $orderType = "ASC"): QueryBuilder{
        $this->orderBy[] = $attributeTableAlias.".".$groupByAttribute." ".$orderType;
        return $this;
    }

    public function orderByField(string $column, array $fields): QueryBuilder
    {
        $fieldString = implode(',', array_map(function($section) {
            return "'$section'";
        }, array_map('esc_sql', $fields)));
        $this->orderBy[] = "FIELD(".$column.",".$fieldString.")";
        return $this;
    }

    public function limit(int $limit): QueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    public function build(): string{
        global $wpdb;
        $queryString = "SELECT ";

        foreach($this->select as $attribute){
            $queryString .= $attribute.",";
        }
        $queryString = rtrim($queryString, ',');

        $queryString .= " FROM ".$wpdb->prefix.$this->from->value." ".$this->from->getAlias()." ";

        foreach($this->combines as $combine){
            $queryString .= $combine->getQueryString()." ";
        }

        if(count($this->where) > 0){
            $queryString .= "WHERE ";
            for($i = 0; $i < count($this->where); $i++){
                if($i >0){
                    $queryString .= $this->where[$i]->getLogicalOperator()." ";
                }
                $queryString .= "(".$this->where[$i]->getQueryString().") ";
            }
        }

        if(count($this->groupBy) > 0){
            $queryString .= "GROUP BY ";
            foreach($this->groupBy as $attribute){
                $queryString .= $attribute.",";
            }
            $queryString = rtrim($queryString, ',');
        }

        if(count($this->orderBy) > 0){
            $queryString .= "ORDER BY ";
            foreach($this->orderBy as $attribute){
                $queryString .= $attribute.",";
            }
            $queryString = rtrim($queryString, ',');
        }

        if($this->limit > 0){
            $queryString .= "LIMIT ".$this->limit;
        }

        return $queryString;
    }
}