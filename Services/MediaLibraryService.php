<?php

namespace Modules\Media\Services;

use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Illuminate\Database\Eloquent\Model;
use Modules\Media\Models\Media;
use Modules\Media\Models\MediaLibrary;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\FileBag;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Exception;


class MediaLibraryService
{


    protected array $extensionMap = [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'jfif'],
        'audio' => ['mp3', 'wav', 'ogg', 'flac'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'mkv'],
        'pdfs' => ['pdf'],
        'docs' => ['doc', 'docx'],
        'xls' => ['xls', 'xlsx'],
        'archives' => ['zip', 'tar', 'rar', 'gz'],
        'spreadsheets' => ['csv'],
        'presentations' => ['ppt', 'pptx', 'odp'],
        'logs' => ['log', 'txt'],
        'fonts' => ['ttf', 'otf', 'woff', 'woff2'],
    ];


    protected array $typeMap = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
        'sound' => ['mp3', 'wav', 'ogg', 'flac'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'mkv'],
        'pdf' => ['pdf'],
        'word' => ['doc', 'docx', 'rtf'],
        'excel' => ['xls', 'xlsx'],
    ];


    public function uploadFile(Model $model, UploadedFile|SymfonyUploadedFile $file, string $collection = 'uploads', ?string $disk = null, ?string $customName = null): Media {

        $disk ??= $this->determineDiskFromExtension($file->getClientOriginalName());
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid('file-') . '.' . $extension;
        $name = ucfirst($customName) ?? $filename;


        return $model->addMedia($file)
            ->usingName($name)
            ->usingFileName($filename)
            ->toMediaCollection($collection, $disk);

    }


    public function uploadFiles(Model $model, array|FileBag $files, string $collection = 'uploads', ?string $disk = null, array $customNames = []): array {

        $results = [];

        foreach ($files as $key => $file) {

            if (!$this->isValidUploadedFile($file)) {

                Log::warning('uploadFiles: Invalid uploaded file instance.', ['file' => $file]);

                continue;

            }


            $customName = $customNames[$key] ?? null;

            $results[] = $this->uploadFile($model, $file, $collection, $disk, $customName);

        }


        return $results;

    }


    public function addFiles(Model $model, MediaCollection|array $files, string $collection = 'uploads', string $disk = 'uploads'): ?array {


        if (empty($files)) {

            return null;

        }


        $results = [];


        foreach ($files as $file) {


            try {


                $path = $this->getFilePath($file);

                $filename = $this->getFileName($file);


                if (!$path || !file_exists($path)) {
      
                    Log::warning('File does not exist.', ['path' => $path]);

                    continue;

                }


                $media = $model->addMedia($path)
                    ->usingName($filename)
                    ->preservingOriginal()
                    ->toMediaCollection($collection, $disk);
                
                
           

                $results[] = $media;


                continue;


            } catch (Exception $e) {


                Log::error('Failed to attach file', [
                    'file' => $file,
                    'error' => $e->getMessage()
                ]);


            }

        }


        return $results;

    }


    public function removeAllFiles(Model $model, ?string $collection = null): bool
    {
        return $collection 
            ? $model->clearMediaCollection($collection) 
            : $model->media()->delete();
    }

/*
    public function getMedia(Model $model, string $collection = 'default'): MediaCollection
    {
        return $model->getMedia($collection);
    }
*/

    public function deleteMedia(Media $media): bool
    {
        return $media->delete();
    }


    public function determineDiskFromExtension(string $filename): ?string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        foreach ($this->extensionMap as $disk => $extensions) {
            if (in_array($extension, $extensions)) {
                return $disk;
            }
        }

        return null;
    }


    public function getFileType(string $ext): string
    {
        $ext = strtolower($ext);

        foreach ($this->typeMap as $type => $extensions) {
            if (in_array($ext, $extensions)) {
                return $type;
            }
        }

        return 'unknown';
    }


    public function getDownloadUrl(string $filename): string
    {
        return route('media.public.download', ['filename' => $filename]);
    }


    public function validateFileArray(array $file): bool
    {

        $requiredKeys = ['id', 'type', 'file_name', 'src'];

        if (empty($file) || !isset($file[0]) || !is_array($file[0])) {
            Log::error('Invalid file array structure.');
            return false;
        }

        $firstItem = $file[0];
        $missingKeys = array_diff($requiredKeys, array_keys($firstItem));

        if (!empty($missingKeys)) {
            Log::error('Missing required keys in file array.', ['missing_keys' => $missingKeys]);
            return false;
        }

        return true;
    }


    public function isValidMediaObject(array $file): bool
    {
        return isset($file['id']) && MediaLibrary::where('id', $file['id'])->exists();
    }


    protected function isValidUploadedFile(mixed $file): bool
    {
        return $file instanceof UploadedFile || $file instanceof SymfonyUploadedFile;
    }


    protected function getFilePath(mixed $file): ?string
    {

     

        if ($file instanceof MediaLibrary || $file instanceof Media ) {
           
            return $file->getPath();
        }

        if ($file instanceof UploadedFile) {
            return $file->getRealPath();
        }

        if (is_string($file)) {
            return $file;
        }

        Log::warning('Unsupported file type.', ['type' => gettype($file)]);

        return null;

    }


    protected function getFileName(mixed $file): string
    {
        if ($file instanceof MediaLibrary) {
            return $file->name;
        }

        if ($file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }

        if (is_string($file)) {
            return pathinfo($file, PATHINFO_BASENAME);
        }

        return 'unknown_' . uniqid();
    }

}