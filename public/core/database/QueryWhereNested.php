<?php

class QueryWhereNested implements IQueryWhere{
    private array $where;
    private string $logicalOperator;

    public function __construct(string $logicalOperator)
    {
        $this->where = array();
        $this->logicalOperator = $logicalOperator;
    }

    public function where(string $operand1TableAlias, string $operand1, string $operator, string $operand2, string $logicalOperator = "AND"): QueryWhereNested{
        $this->where[] = new QueryWhere($operand1TableAlias, $operand1, $operator, $operand2, $logicalOperator);
        return $this;
    }

    public function whereNull(string $operand1TableAlias, string $operand1, string $logicalOperator = "AND"): QueryWhereNested{
        $this->where[] = new QueryWhereNull($operand1TableAlias, $operand1, $logicalOperator);
        return $this;
    }

    //TODO: duplicate logic with queryBuilder, maybe rework querybuilder to produce separate strings eg. queryBuilder->getWhereClause()
    public function getQueryString(): string{
        $queryString = "";
        for($i = 0; $i < count($this->where); $i++){
            if($i >0){
                $queryString .= $this->where[$i]->getLogicalOperator()." ";
            }
            $queryString .= $this->where[$i]->getQueryString()." ";
        }
        return $queryString;
    }

    public function getLogicalOperator(): string{
        return $this->logicalOperator;
    }
}