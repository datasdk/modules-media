<?php

namespace Modules\Media\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\Media\Models\Media;
use Modules\Media\Models\Folder;

class MediaManager extends Component
{
    use WithFileUploads;

    public $collections = [];
    public $selectedCollection = null;

    public $folders = [];
    public $selectedFolder = null;

    public $images = [];

    public $uploadFiles = [];
    public $isUploading = false;
    public $totalFiles = 0;
    public $uploadedCount = 0;

    public $previewImage = null;
    public $loading = false;

    public function mount()
    {
        $this->fetchCollections();
        $this->fetchFolders();
        $this->fetchImages();
    }

    public function fetchCollections()
    {
        // Eksempel: hent fra Media collection tabel
        $this->collections = Media::distinct('collection_name')->pluck('collection_name')->toArray();
        if(!$this->selectedCollection && count($this->collections) > 0){
            $this->selectedCollection = $this->collections[0];
        }
    }

    public function fetchFolders()
    {
        $this->folders = Folder::all()->toArray();
    }

    public function fetchImages()
    {
        $this->loading = true;
        $query = Media::query();
        if ($this->selectedCollection) {
            $query->where('collection_name', $this->selectedCollection);
        }
        if ($this->selectedFolder) {
            $query->where('folder_id', $this->selectedFolder['id']);
        }
        $this->images = $query->orderBy('created_at','desc')->get()->toArray();
        $this->loading = false;
    }

    public function updatedUploadFiles()
    {
        if (!$this->uploadFiles) return;

        $this->isUploading = true;
        $this->totalFiles = count($this->uploadFiles);
        $this->uploadedCount = 0;

        foreach($this->uploadFiles as $file){
            $media = Media::createFromUpload($file, $this->selectedCollection, $this->selectedFolder['id'] ?? null);
            $this->images[] = $media->toArray();
            $this->uploadedCount++;
        }

        $this->uploadFiles = [];
        $this->isUploading = false;
    }

    public function deleteImage($id)
    {
        $image = Media::find($id);
        if($image){
            $image->delete();
            $this->images = collect($this->images)->reject(fn($i)=>$i['id']==$id)->values()->toArray();
        }
    }

    public function preview($id)
    {
        $this->previewImage = collect($this->images)->firstWhere('id',$id);
    }

    public function selectFolder($folderId)
    {
        $this->selectedFolder = collect($this->folders)->firstWhere('id', $folderId);
        $this->fetchImages();
    }

    public function moveImage($imageId, $folderId)
    {
        $image = Media::find($imageId);
        if($image){
            $image->folder_id = $folderId;
            $image->save();
            $this->images = collect($this->images)->map(function($i) use ($image){
                if($i['id'] == $image->id) $i['folder_id'] = $image->folder_id;
                return $i;
            })->toArray();
        }
    }

    public function render()
    {
        return view('media::livewire.media-manager');
    }
}
