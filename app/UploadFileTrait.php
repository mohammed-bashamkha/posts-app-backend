<?php

namespace App;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait UploadFileTrait
{
    protected function uploadFile(
        UploadedFile $file,
        string $folder,
        string $disk = 'public'
    ): string {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($folder, $filename, $disk);
    }
}
