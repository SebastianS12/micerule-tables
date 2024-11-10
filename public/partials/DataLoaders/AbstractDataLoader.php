<?php

abstract class AbstractDataLoader{
    protected Collection $collection;

    public function load(IRepository $repository, ?Closure $constraintClosure = null): Collection
    {
        $this->collection = $repository->getAll($constraintClosure);
        return $this->collection;
    }

    public function getCollection(): Collection
    {
        return $this->collection;
    }
}