<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Contracts\ImageInterface;

class ImageUpload implements ImageInterface
{
    public function upload($image): string
    {
        $timestamp = now()->timestamp; 
        $randomString = Str::random(10);
        $originalName = $image->getClientOriginalName();
        
        $filename = "{$timestamp}_{$randomString}_{$originalName}";
        
        $image->move('uploads/', $filename);
        
        return $filename;
    }
    
}





