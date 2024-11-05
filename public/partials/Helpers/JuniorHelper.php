<?php

class JuniorHelper{
    public static function addJunior(int $locationID, string $userName, ShowOptionsService $showOptionsService): bool
    {
        $showOptions = $showOptionsService->getShowOptions(new ShowOptionsRepository(), $locationID);
        return EventUser::isJuniorMember($userName) && $showOptions->optionalClasses['junior'];
    }
}