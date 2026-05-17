<?php

namespace Modules\Media\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Media\Models\Media;

class ImageSelect extends Component
{
    use WithFileUploads;

    public $selectedImages = []; // Array med Media-objekter
    public $availableImages = [];
    public $uploadFiles = [];
    public $single = true;
    public $previewImage = null;
    public $collection = 'media';

    public $loading = false;
    public $isUploading = false;
    public $totalFiles = 0;
    public $uploadedCount = 0;

    public function mount($value = [], $single = true)
    {
        $this->single = $single;
        $this->selectedImages = [];

        // Load initial IDs hvis $value er givet
        if ($value) {
            $images = Media::whereIn('id', (array)$value)->get();
            $this->selectedImages = $images->toArray();
        }

        $this->fetchImages();
    }

    public function fetchImages()
    {
        $this->loading = true;
        $this->availableImages = Media::where('collection_name', $this->collection)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        $this->loading = false;
    }

    public function toggleSelect($id)
    {
        $image = collect($this->availableImages)->firstWhere('id', $id);
        if (!$image) return;

        $exists = collect($this->selectedImages)->firstWhere('id', $id);

        if ($this->single) {
            $this->selectedImages = $exists ? [] : [$image];
        } else {
            if ($exists) {
                $this->selectedImages = collect($this->selectedImages)->reject(fn($i) => $i['id']==$id)->values()->toArray();
            } else {
                $this->selectedImages[] = $image;
            }
        }

        $this->emitSelected();
    }

    public function removeImage($id)
    {
        $this->selectedImages = collect($this->selectedImages)->reject(fn($i) => $i['id']==$id)->values()->toArray();
        $this->emitSelected();
    }

    public function emitSelected()
    {
        $ids = collect($this->selectedImages)->pluck('id')->toArray();
        if ($this->single) {
            $ids = $ids[0] ?? null;
        }
        $this->emitUp('input', $ids);
    }

    public function preview($id)
    {
        $this->previewImage = collect($this->availableImages)->firstWhere('id', $id);
    }

    public function updatedUploadFiles()
    {
        foreach ($this->uploadFiles as $file) {
            $media = Media::createFromUpload($file, $this->collection);
            $this->availableImages[] = $media->toArray();
        }
        $this->uploadFiles = [];
    }

    public function render()
    {
        return view('media::livewire.image-select');
    }
}
