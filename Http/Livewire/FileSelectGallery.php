<?php

namespace Modules\Media\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Media\Models\Media;

class FileSelectGallery extends Component
{
    use WithFileUploads;

    public $value = [];
    public $collection = 'media';
    public $single = false;

    public $selectedFiles = [];
    public $availableFiles = [];
    public $isUploading = false;
    public $uploadFiles = [];
    public $uploadedCount = 0;

    public $selectModal = false;

    public function mount($value = [])
    {
        $this->value = $value;
        $this->loadAvailableFiles();
    }

    public function loadAvailableFiles()
    {
        $this->availableFiles = Media::where('collection_name', $this->collection)
            ->orderBy('created_at','desc')
            ->get()
            ->map(function ($file) {
                $file->isImage = str_starts_with($file->mime_type, 'image/');
                $file->isPDF   = $file->mime_type === 'application/pdf';
                return $file;
            })->toArray();

        $this->selectedFiles = collect($this->availableFiles)
            ->whereIn('id', $this->value)
            ->values()
            ->toArray();
    }

    public function toggleSelect($fileId)
    {
        $file = collect($this->availableFiles)->firstWhere('id', $fileId);
        if (!$file) return;

        $exists = collect($this->selectedFiles)->firstWhere('id', $file['id']);

        if ($exists) {
            $this->selectedFiles = collect($this->selectedFiles)
                ->reject(fn($f) => $f['id'] == $file['id'])
                ->values()
                ->toArray();
        } else {
            if ($this->single) {
                $this->selectedFiles = [$file];
            } else {
                $this->selectedFiles[] = $file;
            }
        }

        $this->emitValue();
    }

    public function emitValue()
    {
        $ids = collect($this->selectedFiles)->pluck('id')->toArray();
        $this->emitUp('input', $ids);
    }

    public function openSelectModal()
    {
        $this->selectModal = true;
        $this->loadAvailableFiles();
    }

    public function upload()
    {
        if (!$this->uploadFiles) return;

        $this->isUploading = true;
        $this->uploadedCount = 0;

        foreach ($this->uploadFiles as $upload) {
            $path = $upload->store('media', 'public');

            $file = Media::create([
                'name' => $upload->getClientOriginalName(),
                'file_name' => $path,
                'collection_name' => $this->collection,
                'mime_type' => $upload->getMimeType(),
                'size' => $upload->getSize(),
            ]);

            $file->isImage = str_starts_with($file->mime_type, 'image/');
            $file->isPDF = $file->mime_type === 'application/pdf';

            $this->availableFiles = array_merge([$file->toArray()], $this->availableFiles);
            $this->uploadedCount++;
        }

        $this->isUploading = false;
        $this->uploadFiles = [];
    }

    public function render()
    {
        return view('media::livewire.file-select-gallery');
    }
}
