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
            if(property_exists($item, $name)){
                $relationCollection->add($item->$name);
            }else if(isset($item->$name)){
                foreach($item->$name as &$relationItem){
                    $relationCollection->add($relationItem);
                }
            }
        }
        
        return $relationCollection;
    }

    public function add(mixed &$item): void{
        $this->items[] = $item;
    }

    public function concat(Collection $otherCollection): Collection
    {
        $this->items = array_merge($this->items, $otherCollection->items);
        return $this;
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

    public function removeLast(): void {
        array_pop($this->items);
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

    public function sortBy(string $attribute, array $relations = []): Collection
    {
        $sortedCollection = new Collection();


        
        return $sortedCollection;
    }

    public function get(string $attribute, mixed $condition): mixed{
        foreach($this as &$collectionItem){
            if($collectionItem->$attribute == $condition){
                return $collectionItem;
            }
        }

        return null;
    }

    public function where(string $attribute, mixed $condition): Collection
    {
        $collection = new Collection();

        foreach($this as &$collectionItem){
            if($collectionItem->$attribute == $condition){
                $collection->add($collectionItem);
            }
        }

        return $collection;
    }

    public function whereNot(string $attribute, mixed $condition): Collection
    {
        $collection = new Collection();

        foreach($this as &$collectionItem){
            if($collectionItem->$attribute != $condition){
                $collection->add($collectionItem);
            }
        }

        return $collection;
    }

    //TODO: Move to Hydrator, have it only called by Data Loaders
    /**
     * @param string[] $childClasses
     * @param string[] $fromKeys
     * @param string[] $relationKeys      
     * @param IRepository[] $repositories  // Array of objects implementing RepositoryInterface
     */
    public function with(array $relationClasses, array $fromKeys, array $relationKeys, array $repositories): Collection{
        // if (count($relationKeys) < 1) {
        //     throw new InvalidArgumentException("Expected 2 or more relation keys.");
        // }
        // if (count($repositories) !== count($relationKeys)) {
        //     throw new InvalidArgumentException("Number of repositories does not match the number of relations.");
        // }

        $repositoryData = [];
        foreach($repositories as $index => $repository){
            $repositoryData[$index] = array();
            foreach($repository->getAll() as $item){
                $relationAttribute = $relationKeys[$index];
                $repositoryData[$index][$item->$relationAttribute][] = $item;
            }
        }

        return $this->hydrateModels($this, $relationClasses, $fromKeys, $repositoryData, 0);
    }

    private function hydrateModels(Collection $collection, array $relationClasses, array $fromKeys, array $repositoryData, int $index){
        if($index == count($fromKeys)) return $collection;
        
        $relationAttribute = $fromKeys[$index];
        $relationClass = $relationClasses[$index];
        foreach($collection as &$collectionItem){
            if(!isset($collectionItem->$relationClass)) $collectionItem->setRelation($relationClass, new Collection());
            $itemClass = get_class($collectionItem);
            if (isset($repositoryData[$index][$collectionItem->$relationAttribute])) {
                $currentCollection = new Collection();
                foreach($repositoryData[$index][$collectionItem->$relationAttribute] as &$relationItem){
                    //set inverse relation
                    if(!isset($relationItem->$itemClass)){
                        $relationItem->setRelation($itemClass, new Collection());
                    }
                    $currentCollection->add($relationItem);
                    $collectionItem->$relationClass->add($relationItem);
                    $relationItem->$itemClass->add($collectionItem);
                }
                $this->hydrateModels($currentCollection, $relationClasses, $fromKeys, $repositoryData, $index + 1);
            }
        }

        return $collection;
    }
}