<?php
namespace App\Contracts;

interface InsertTripInterface
{
    public static function insert(array $trips): void;
}

?>
