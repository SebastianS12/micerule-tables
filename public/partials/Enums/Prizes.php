<?php

enum Prize: int{
    case STANDARD = 0;
    case JUNIOR = 1;
    case SECTION = 2;
    case GRANDCHALLENGE = 3;
    case SECTION_AWARD = 4;
    case GC_AWARD = 5;

    public function getAward(): int
    {
        return match($this) {
            self::STANDARD => 0,
            self::JUNIOR => 1,
            self::SECTION => 4,
            self::GRANDCHALLENGE => 5,
            self::SECTION_AWARD => 4,
            self::GC_AWARD => 5,
        };
    }
}