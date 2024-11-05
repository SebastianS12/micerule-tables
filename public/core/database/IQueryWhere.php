<?php

interface IQueryWhere{
    public function getQueryString(): string;
    public function getLogicalOperator(): string;
}