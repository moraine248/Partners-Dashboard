<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;


class CloudinaryHelper
{
    /**
     * Upload the screenshot to Cloudinary, resize it, and return the URL.
     *
     * @param string $name        The name of the image file.
     * @param string $image  The base64 encoded screenshot.
     * @return string|null        The URL of the resized image on Cloudinary, or null if the upload fails.
     */
    public static function uploadAndResizeScreenshot(string $name, string $image, int $width, int $height): ?string
    {
        $dataUri = 'data:image/png;base64,' . $image;

        try {
            // Replace these with your actual Cloudinary credentials
            $cloudName = env('CLOUDINARY_CLOUD_NAME');
            $apiKey = env('CLOUDINARY_API_KEY');
            $apiSecret = env('CLOUDINARY_API_SECRET');

            // Configure Cloudinary with your credentials
            Configuration::instance([
                'cloud' => [
                  'cloud_name' => $cloudName, 
                  'api_key' => $apiKey, 
                  'api_secret' => $apiSecret],
                'url' => [
                  'secure' => true]]);

            // Upload the screenshot to Cloudinary.
            $uploadApi = new UploadApi();
            $uploadResult = $uploadApi->upload($dataUri, [
                'folder' => 'screenshots', // Optionally, set a folder in Cloudinary
                'public_id' => $name,
                'transformation' => [
                    [
                        'width' => 320,
                        'height' => 180,
                        'crop' => 'fit',
                    ],
                ],
            ]);

            // Get the URL of the resized image.
            return $uploadResult['secure_url'] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
