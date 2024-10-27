<?php

class BreedsRepository implements IRepository{

    public function getAll(?Closure $constraintsClosure = null): Collection
    {
        $query = QueryBuilder::create()
                                ->select(["*"])
                                ->from(Table::BREEDS)
                                ->build();

        global $wpdb;
        $breedsQueryResult = $wpdb->get_results($query, ARRAY_A);

        $collection = new Collection();
        foreach($breedsQueryResult as $row){
            $breedModel = BreedModel::createWithID($row['id'], $row['name'], $row['colour'], $row['css_class'], $row['section'], $row['icon_url']);
            $collection->add($breedModel);
        }

        return $collection;
    }
    
    public function getSectionBreedNames(string $section): array|null{
        global $wpdb;
        return $wpdb->get_col("SELECT name FROM ".$wpdb->prefix."micerule_breeds WHERE section = '".$section."'");
    }
}