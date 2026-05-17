<?php

namespace Modules\Media\Http\Livewire;

use Livewire\Component;
use Modules\Media\Models\Folder;

class FolderSelect extends Component
{
    public $folders = [];
    public $selectedFolder = null;

    public $newFolderName;

    public function mount($folders = [])
    {
        $this->folders = $folders;
        if (!empty($folders)) {
            $this->selectedFolder = $folders[0];
            $this->emitFolder();
        }
    }

    public function emitFolder()
    {
        $this->emitUp('folderSelected', $this->selectedFolder);
    }

    public function createFolder()
    {
        if (!$this->newFolderName) {
            $this->dispatchBrowserEvent('toast', ['message' => 'Indtast et navn til mappen', 'type' => 'error']);
            return;
        }

        $folder = Folder::create([
            'name' => $this->newFolderName,
            'parent_id' => $this->selectedFolder['id'] ?? null
        ]);

        $this->folders[] = $folder->toArray();
        $this->selectedFolder = $folder->toArray();

        $this->emitUp('folderCreated', $folder);
        $this->emitFolder();

        $this->newFolderName = null;

        $this->dispatchBrowserEvent('toast', ['message' => 'Mappe oprettet', 'type' => 'success']);
    }

    public function updatedSelectedFolder($value)
    {
        $this->emitFolder();
    }

    public function moveImage($imageId, $folderId)
    {
        $this->emitUp('moveImage', ['imageId' => $imageId, 'folderId' => $folderId]);
    }

    public function render()
    {
        return view('media::livewire.folder-select');
    }
}
