<?php
namespace App\Contracts;

interface ImageInterface
{
    public function upload($request): string;
}

?>
