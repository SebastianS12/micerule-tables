<?php

class QueryWhere implements IQueryWhere{
    private string $operand1TableAlias;
    private string $operand1;
    private string $operator;
    private string $operand2;
    private string $logicalOperator;

    public function __construct(string $operand1TableAlias, string $operand1, string $operator, string $operand2, string $logicalOperator = "AND")
    {
        $this->operand1TableAlias = $operand1TableAlias;
        $this->operand1 = $operand1;
        $this->operator = $operator;
        $this->operand2 = $operand2;
        $this->logicalOperator = $logicalOperator;
    }

    public function getQueryString(): string{
        global $wpdb;
        $operand2Placeholder = is_numeric($this->operand2) ? "%d" : "%s";
        return $wpdb->prepare("{$this->operand1TableAlias}.{$this->operand1} {$this->operator} ".$operand2Placeholder."", $this->operand2);
    }

    public function getLogicalOperator(): string{
        return $this->logicalOperator;
    }
}