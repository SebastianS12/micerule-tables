<?php

interface IRepository{
    public function getAll(Closure|null $constraintsClosure = null): Collection;
}