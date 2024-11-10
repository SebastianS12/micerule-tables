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

    public static function mapExistingCollections(Collection $parentCollection, Collection $childCollection, string $childClass, string $parentMappingKey, string $childMappingKey){
        $childCollection = $childCollection->groupBy($childMappingKey);
        foreach($parentCollection as &$parentItem){
            $itemClass = get_class($parentItem);
            if(!isset($parentItem->$childClass)) $parentItem->setRelation($childClass, new Collection());
            if(isset($childCollection[$parentItem->$parentMappingKey])){
                foreach($childCollection[$parentItem->$parentMappingKey] as &$childItem){
                    if(!isset($childItem->$itemClass)) $childItem->setRelation($itemClass, new Collection());
                    $parentItem->$childClass->add($childItem);
                    $childItem->$itemClass->add($parentItem);
                }
            }
        }
    }
}