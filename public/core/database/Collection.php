<?php

class Collection implements ArrayAccess, IteratorAggregate, Countable{
    public array $items;

    public function __construct()
    {
        $this->items = array();
    }

    public static function createFromArray(array $items): Collection{
        $collection = new Collection();
        foreach($items as $item){
            $collection->add($item);
        }
        return $collection;
    }

    public function __get($name)
    {
        $relationCollection = new Collection();
        foreach($this->items as &$item){
            foreach($item->$name as &$relationItem){
                $relationCollection->add($relationItem);
            }
        }
        return $relationCollection;
    }

    public function add(mixed &$item): void{
        $this->items[] = $item;
    }

    public function offsetExists($key): bool {
        return isset($this->items[$key]);
    }

    public function offsetGet($key): mixed {
        return $this->items[$key] ?? null;
    }

    public function offsetSet($key, $value): void {
        if ($key === null) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    public function offsetUnset($key): void {
        unset($this->items[$key]);
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function first(): mixed{
        if(count($this->items) == 0) return null;
        return $this->items[0];
    }

    public function last(): mixed{
        return end($this->items);
    }

    public function groupBy(string $attribute): Collection{
        $groupedCollection = new Collection();
        foreach($this as &$collectionItem){
            if(!isset($groupedCollection[$collectionItem->$attribute])){
                $groupedCollection[$collectionItem->$attribute] = new Collection();
            }
        
            $groupedCollection[$collectionItem->$attribute]->add($collectionItem);    
        }

        return $groupedCollection;
    }

    public function groupByUniqueKey(string $key): Collection{
        $groupedCollection = new Collection();
        foreach($this as $collectionItem){        
            $groupedCollection[$collectionItem->$key] = $collectionItem;    
        }

        return $groupedCollection;
    }

    public function get(string $attribute, mixed $condition): mixed{
        foreach($this as &$collectionItem){
            if($collectionItem->$attribute == $condition){
                return $collectionItem;
            }
        }

        return null;
    }

    /**
     * @param string[] $propertyNames
     * @param string[] $relationKeys      
     * @param IRepository[] $repositories  // Array of objects implementing RepositoryInterface
     */
    public function with(array $propertyNames, array $fromKeys, array $relationKeys, array $repositories): Collection{
        if (count($relationKeys) < 1) {
            throw new InvalidArgumentException("Expected 2 or more relation keys.");
        }
        if (count($repositories) !== count($relationKeys)) {
            throw new InvalidArgumentException("Number of repositories does not match the number of relations.");
        }

        $repositoryData = [];
        foreach($repositories as $index => $repository){
            $repositoryData[$index] = array();
            foreach($repository->getAll() as $item){
                $relationAttribute = $relationKeys[$index];
                $repositoryData[$index][$item->$relationAttribute][] = $item;
            }
        }

        return $this->hydrateModels($this, $fromKeys, $propertyNames, $repositoryData, 0);
    }

    private function hydrateModels(Collection $collection, array $fromKeys, array $propertyNames, array $repositoryData, int $index){
        if($index == count($propertyNames)) return $collection;
        
        $relationAttribute = $fromKeys[$index];
        $relation = $propertyNames[$index];
        foreach($collection as &$collectionItem){
            if(!isset($collectionItem->$relation)) $collectionItem->setRelation($relation, new Collection());
            $itemClass = get_class($collectionItem);
            if (isset($repositoryData[$index][$collectionItem->$relationAttribute])) {
                foreach($repositoryData[$index][$collectionItem->$relationAttribute] as &$relationItem){
                    //set inverse relation
                    if(!isset($relationItem->$itemClass)){
                        $relationItem->setInverseRelation($itemClass, new Collection());
                    }
                    $relationItem->$itemClass->add($collectionItem);
                    $collectionItem->$relation->add($relationItem);
                }
            }
            $this->hydrateModels($collectionItem->$relation, $fromKeys, $propertyNames, $repositoryData, $index + 1);
        }

        return $collection;
    }
}