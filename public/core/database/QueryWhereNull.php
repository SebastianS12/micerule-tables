<?php

class QueryWhereNull implements IQueryWhere{
    private string $operand1TableAlias;
    private string $operand1;
    private string $logicalOperator;

    public function __construct(string $operand1TableAlias, string $operand1, string $logicalOperator = "AND")
    {
        $this->operand1TableAlias = $operand1TableAlias;
        $this->operand1 = $operand1;
        $this->logicalOperator = $logicalOperator;
    }

    public function getQueryString(): string{
        return "{$this->operand1TableAlias}.{$this->operand1} IS NULL";
    }

    public function getLogicalOperator(): string{
        return $this->logicalOperator;
    }
}