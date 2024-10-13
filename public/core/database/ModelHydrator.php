<?php

class ModelHydrator{
    public static function mapAttribute(Collection|null $toCollection, Collection|null $fromCollection, string $attributeName, string|int $toMappingKey, string|int $fromMappingKey, string $targetAttribute, mixed $default){
        if(isset($toCollection) && isset($fromCollection)){
            $fromCollectionMap = self::CollectionToMap($fromCollection, $fromMappingKey);
            foreach($toCollection as &$collectionItem){
                if(isset($fromCollectionMap[$collectionItem->$toMappingKey])){
                    $collectionItem->setAttribute($attributeName, $fromCollectionMap[$collectionItem->$toMappingKey]->$targetAttribute);
                }else{
                    $collectionItem->setAttribute($attributeName, $default);
                }
            }
        }
    }

    private static function CollectionToMap(Collection|null $collection, string|int $key): array{
        $map = array();
        foreach($collection as $collectionItem){
            $map[$collectionItem->$key] = $collectionItem;
        }

        return $map;
    }

    public static function mapExistingCollections(Collection $parentCollection, string $relation, Collection $childCollection, string $parentMappingKey, string $childMappingKey){
        $childCollection = $childCollection->groupBy($childMappingKey);
        foreach($parentCollection as &$parentItem){
            $itemClass = get_class($parentItem);
            if(!isset($parentItem->$relation)) $parentItem->setRelation($relation, new Collection());
            if(isset($childCollection[$parentItem->$parentMappingKey])){
                $parentItem->$relation($relation, $childCollection[$parentItem->$parentMappingKey]);

                //inverse relation
                foreach($childCollection[$parentItem->$parentMappingKey] as &$childItem){
                    if(!isset($relationItem->$itemClass)){
                        $childItem->setInverseRelation($itemClass, new Collection());
                    }
                    $childItem->$itemClass->add($parentItem);
                }
            }
            
        }
    }
}