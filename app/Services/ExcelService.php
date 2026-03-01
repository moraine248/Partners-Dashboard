<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel as LaravelExcel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ExcelService
{
    /**
     * Import data from an Excel file into a given model.
     *
     * @param UploadedFile $file
     * @param string $modelClassName
     * @return array
     */
    public function import(UploadedFile $file, $modelClassName)
    {
        try {
            // Use reflection to fetch the fillable fields from the model
            $model = new $modelClassName;
            $fillableFields = $this->getFillableFields($model);

            $results = LaravelExcel::filter('chunk')
                ->selectSheetsByIndex(0)
                ->load($file)
                ->chunk(100, function ($chunk) use ($modelClassName, $fillableFields) {
                    foreach ($chunk->toArray() as $row) {
                        $model = new $modelClassName;

                        // Filter the row data based on fillable fields
                        $filteredData = array_intersect_key($row, array_flip($fillableFields));

                        // Fill the model attributes
                        $model->fill($filteredData)->save();
                    }
                });

            return ['success' => 'Import successful'];
        } catch (\Exception $e) {
            return ['error' => 'Failed to import the file'];
        }
    }

    /**
     * Get the fillable fields from a model.
     *
     * @param mixed $model
     * @return array
     */
    private function getFillableFields($model)
    {
        return $model->getFillable();
    }
}
