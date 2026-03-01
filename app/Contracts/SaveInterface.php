<?php
namespace App\Contracts;

interface SaveInterface
{
    public static function saveOrUpdate($modelClass, $request): void;
}

?>
