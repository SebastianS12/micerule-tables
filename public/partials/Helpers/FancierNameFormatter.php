<?php

class FancierNameFormatter{
    public static function getShowReportFancierName(string $fancierName): string
    {
        $formattedName = $fancierName;
        if(count(explode(" ", $fancierName, 2)) == 2){
            $firstName = explode(" ", $fancierName)[0];
            $surName = explode(" ", $fancierName)[1];

            $formattedName = $firstName[0]." ".$surName;
        }
        return $formattedName;
    }
}