<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait FileUploadTrait
{
    public function uploadFile(UploadedFile $file, ?string $oldPath = null, ?string $path = 'uploads'): ?string
    {
        if (! $file->isValid()) {
            return null;
        }

        $ignorePath = ['/defaults/avatar.png'];

        if ($oldPath && ! in_array($oldPath, $ignorePath)) {
            $this->deleteFile($oldPath);
        }

        $folderPath = public_path($path);
        File::ensureDirectoryExists($folderPath);

        $filename = Str::uuid(20).'.'.$file->getClientOriginalExtension();

        $file->move($folderPath, $filename);

        $filepath = $path.'/'.$filename;

        return $filepath;
    }

    /**
     * Delete a file created by this trait without touching bundled public assets.
     */
    public function deleteFile(?string $path): bool
    {
        if (! filled($path) || Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return false;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', urldecode((string) parse_url($path, PHP_URL_PATH))), '/');

        if (! Str::startsWith($normalizedPath, 'uploads/')) {
            return false;
        }

        $uploadsRoot = realpath(public_path('uploads'));
        $filePath = realpath(public_path($normalizedPath));

        if ($uploadsRoot === false || $filePath === false) {
            return false;
        }

        $uploadsPrefix = rtrim(str_replace('\\', '/', $uploadsRoot), '/').'/';
        $normalizedFilePath = str_replace('\\', '/', $filePath);

        if (! Str::startsWith(Str::lower($normalizedFilePath), Str::lower($uploadsPrefix))) {
            return false;
        }

        return File::isFile($filePath) && File::delete($filePath);
    }
}
