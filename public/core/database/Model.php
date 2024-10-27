<?php

class Model{
    protected int $id;
    protected array $relations = [];
    protected array $attributes = [];

    public function __get($name)
    {
        if(property_exists($this, $name)){
            return $this->$name;
        }else if (array_key_exists($name, $this->relations)) {
            return $this->relations[$name];
        }else if(array_key_exists($name, $this->attributes)){
            return $this->attributes[$name];
        }else if(method_exists($this, $name)){
            return $this->$name();
        }else{
            throw new Exception("Property or relationship '$name' does not exist.");
        }
    }

    public function __set($name, $value)
    {
        if(property_exists($this, $name)){
            $this->$name = $value;
        }else if (isset($this->relations[$name])) {
            $this->relations[$name] = $value;
        }else if(isset($this->attributes[$name])){
            $this->attributes[$name] = $value;
        }else{
            throw new Exception("Property or relationship '$name' does not exist.");
        } 
    }

    public function __isset($name)
    {
        if (property_exists($this, $name)) {
            return isset($this->$name);
        } elseif (array_key_exists($name, $this->relations)) {
            return isset($this->relations[$name]);
        } elseif (array_key_exists($name, $this->attributes)) {
            return isset($this->attributes[$name]);
        }

        return false;
    }

    // Customize var_dump output
    public function __debugInfo(): array {
        // Get all properties of the current instance, including inherited properties
        $properties = [
            'ID' => $this->id,
            'Relations' => array_keys($this->relations),
            'Attributes' => array_keys($this->attributes),
        ];

        // Merge properties from subclass
        $subclassProperties = $this->getSubclassSpecificProperties();

        return array_merge($properties, $subclassProperties);
    }

    protected function getSubclassSpecificProperties(): array {
        $properties = [];
        // Use reflection to get properties of the subclass
        $reflect = new ReflectionClass($this);
        $parentReflect = new ReflectionClass(get_parent_class($this));

        // Get the properties defined in the parent class
        $parentProperties = array_map(fn($prop) => $prop->getName(), $parentReflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED));

        // Get properties of the current subclass and filter out parent properties
        foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED) as $property) {
            if (!$property->isStatic() && !in_array($property->getName(), $parentProperties)) {
                $property->setAccessible(true);
                $properties[$property->getName()] = $property->getValue($this);
            }
        }
        return $properties;
    }

    public function setRelation($name, $value){
        $this->relations[$name] = $value;
    }

    public function setAttribute($name, $value){
        $this->attributes[$name] = $value;
    }

    public function hasOne(string $relationModel, Table $relationTable, string $foreignKey): mixed{
        if(!isset($this->relations[$relationModel])){
            if(!LazyLoader::loadHasOne($this, $relationModel, $relationTable, $foreignKey));
        }

        return $this->relations[$relationModel]->first();
    }

    public function belongsToOne(string $relationModel, Table $relationTable, string $foreignKey): ?Model{
        if(!isset($this->relations[$relationModel])){
            if(!LazyLoader::loadBelongsToOne($this, $relationModel, $relationTable, $foreignKey)){
                return null;
            }
        }

        return $this->relations[$relationModel]->first();
    }

    public function hasMany(string $relationModel, Table $relationTable, string $foreignKey): Collection{
        if(!isset($this->relations[$relationModel])){
            if(!LazyLoader::loadHasMany($this, $relationModel, $relationTable, $foreignKey)){
                return new Collection();
            }
        }

        return $this->relations[$relationModel];
    }

    public function belongsToOneThrough(array $relationshipPath, array $relationTables, array $foreignKeys, int $currentDepth = 0): mixed{
        if($currentDepth == count($relationshipPath)) return $this;
    
        $relatedModel = $this->belongsToOne($relationshipPath[$currentDepth], $relationTables[$currentDepth], $foreignKeys[$currentDepth]);
        if(!isset($relatedModel)) return null;
    
        return $relatedModel->belongsToOneThrough($relationshipPath, $relationTables, $foreignKeys, $currentDepth + 1);
    }
}