<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;
use setasign\Fpdi\Fpdi;

trait FileHelper
{
    public function fileUpload($file, $path, $deleteFileName = null): string
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $storage = Storage::disk('public');
        $storage->put($path .'/'. $filename, File::get($file));

        if ($deleteFileName && Storage::exists('public/' . $path.'/'. $deleteFileName)) {
            $storage->delete($path .'/'. $deleteFileName);
        }
        return $filename;
    }

    public function fileDelete($path, $deleteFileName = null): bool
    {
        $storage = Storage::disk('public');
        if ($deleteFileName && Storage::exists('public/' . $path.'/'. $deleteFileName)) {
            $storage->delete($path .'/'. $deleteFileName);
        }
        return true;
    }
}