<?php

namespace Modules\Media\Http\Livewire;

use Livewire\Component;

class FilePreview extends Component
{
    public $files = [];
    public $dialog = false;
    public $currentFile = null;
    public $loading = false; // Ændret til false som standard

    protected $listeners = [
        'iframeLoaded' => 'handleIframeLoaded'
    ];

    public function mount($files = [])
    {
        $this->files = $files;
    }

    public function openPreview($fileId)
    {
        $this->loading = true;
        $this->currentFile = null;

        // Find filen ud fra ID
        $file = collect($this->files)->firstWhere('id', $fileId);

        if ($file) {
            $this->currentFile = $file;
            $this->dialog = true;
            
            // Auto-loading timeout - hvis iframe ikke trigger onload
            $this->dispatchBrowserEvent('start-iframe-timeout', [
                'fileId' => $fileId,
                'timeout' => 5000 // 5 sekunder timeout
            ]);
        }
    }

    public function handleIframeLoaded()
    {
        $this->loading = false;
    }

    // Alternativ: Brug et JavaScript event
    public function iframeLoaded()
    {
        $this->loading = false;
    }

    public function getImageLink($file)
    {
        $fileType = pathinfo($file['file_name'] ?? '', PATHINFO_EXTENSION);
        return '/Modules/Media/images/' . strtolower($fileType) . '-icon.png';
    }

    public function render()
    {
        return view('media::livewire.file-preview');
    }
}