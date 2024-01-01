<?php

namespace App\Services;

use App\Enums\Errors\CommonError;
use Illuminate\Support\Facades\Storage;

class FileServices
{
    private static $putLocation = "public/images";

    public static function uploadFile($file, $location = "")
    {
        try {
            $putFile = $location ? $location : self::$putLocation;
            return Storage::put($putFile, $file);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function deleteFile($fileLocation)
    {
        try {
            if(Storage::exists($fileLocation)) {
                Storage::delete($fileLocation);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
