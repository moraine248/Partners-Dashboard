<?php

namespace App\Services;

use App\Contracts\SaveInterface;

class Save implements SaveInterface
{
   public static function saveOrUpdate($modelClass, $attr): void
   {
        $id = $attr['id'] ?? null;
        $model = new $modelClass;
        $model->updateOrCreate(['id' => $id], $attr);
    }

}





