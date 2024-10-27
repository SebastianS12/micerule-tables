<?php

class BreedModel extends Model{
    public string $name;
    public string $colour;
    public string $css_class;
    public string $section;
    public string $icon_url;

    private function __construct(string $name, string $colour, string $css_class, string $section, string $icon_url)
    {
        $this->name = $name;
        $this->colour = $colour;
        $this->css_class = $css_class;
        $this->section = strtolower($section); //TODO: section enum, adjust for breed save
        $this->icon_url = $icon_url;
    }

    public static function create(string $name, string $colour, string $css_class, string $section, string $icon_url): BreedModel
    {
        return new self($name, $colour, $css_class, $section, $icon_url);
    }

    public static function createWithID(int $id, string $name, string $colour, string $css_class, string $section, string $icon_url): BreedModel
    {
        $instance = self::create($name, $colour, $css_class, $section, $icon_url);
        $instance->id = $id;
        return $instance;
    }
}