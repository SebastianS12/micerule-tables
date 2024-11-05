<?php

enum Section : string{
    case SELF = "selfs";
    case TAN = "tans";
    case MARKED = "marked";
    case SATIN = "satins";
    case AOVS = "aovs";
    case GRAND_CHALLENGE = "grand challenge";
    case OPTIONAL = "optional";

    public static function standardClasses(): array {
        return array_filter(
            Section::cases(),
            fn($section) => !in_array($section, [Section::GRAND_CHALLENGE, Section::OPTIONAL], true)
        );
    }

    public function getDisplayString(): string
    {
        return match($this) {
            self::SELF => 'Self',
            self::TAN => 'Tan',
            self::MARKED => 'Marked',
            self::SATIN => 'Satin',
            self::AOVS => 'AOV',
            self::GRAND_CHALLENGE => 'Grand Challenge',
            self::OPTIONAL => 'Optional',
        };
    }

    public function getDisplayStringPlural(): string
    {
        return match($this) {
            self::SELF => 'Selfs',
            self::TAN => 'Tans',
            self::MARKED => 'Marked',
            self::SATIN => 'Satins',
            self::AOVS => 'AOVs',
            self::GRAND_CHALLENGE => 'Grand Challenge',
            self::OPTIONAL => 'Optional',
        };
    }

    public function getChallengeName(): string
    {
        return match($this) {
            self::SELF => 'SELF CHALLENGE',
            self::TAN => 'TAN CHALLENGE',
            self::MARKED => 'MARKED CHALLENGE',
            self::SATIN => 'SATING CHALLENGE',
            self::AOVS => 'AOV CHALLENGE',
            self::GRAND_CHALLENGE => 'GRAND CHALLENGE',
            self::OPTIONAL => 'Optional',
        };
    }
}