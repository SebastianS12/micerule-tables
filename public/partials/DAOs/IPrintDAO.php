<?php

interface IPrintDAO{
    public function updatePrinted(int $id, bool $printed);
}