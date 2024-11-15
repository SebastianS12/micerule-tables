<?php

class LazyLoader{

    public static function loadBelongsToOne(Model &$model, string $relationClass, Table $relationTable, string $foreignKey): bool
    {
        $relationQuery = QueryBuilder::create()
                                        ->select(["*"])
                                        ->from($relationTable)
                                        ->where($relationTable->getAlias(), "id", "=", $model->{$foreignKey})
                                        ->limit(1);

        return self::loadRelations($model, $relationClass, $relationQuery);
    }

    public static function loadHasOne(Model &$model, string $relationClass, Table $relationTable, string $foreignKey): bool
    {
        $relationQuery = QueryBuilder::create()
                                        ->select(["*"])
                                        ->from($relationTable)
                                        ->where($relationTable->getAlias(), $foreignKey, "=", $model->id)
                                        ->limit(1);

        return self::loadRelations($model, $relationClass, $relationQuery);
    }

    public static function loadHasMany(Model &$model, string $relationClass, Table $relationTable, string $foreignKey): bool
    {
        $relationQuery = QueryBuilder::create()
                                        ->select(["*"])
                                        ->from($relationTable)
                                        ->where($relationTable->getAlias(), $foreignKey, "=", $model->id);

        return self::loadRelations($model, $relationClass, $relationQuery);
    }

    private static function loadRelations(Model &$model, string $relationClass, QueryBuilder $relationQuery): bool
    {
        echo("LazyLoad");
        global $wpdb;
        $relationQueryResults = $wpdb->get_results($relationQuery->build(), ARRAY_A);
        $modelClass = get_class($model);
        if($relationQueryResults){
            foreach($relationQueryResults as $row){
                $relationModel = $relationClass::createWithID(...$row);
                if(!isset($relationModel->$modelClass)) $relationModel->setRelation($modelClass, new Collection());
                if(!isset($model->$relationClass))$model->setRelation($relationClass, new Collection());
                $model->{$relationClass}->add($relationModel);
                $relationModel->{$modelClass}->add($model);
            }

            return true;
        }

        return false;
    }
}