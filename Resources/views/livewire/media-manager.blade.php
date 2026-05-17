<div>
    <!-- Collection Dropdown -->
    <div class="mb-3">
        <select class="form-select" wire:model="selectedCollection" wire:change="fetchImages">
            @foreach($collections as $collection)
                <option value="{{ $collection }}">{{ $collection }}</option>
            @endforeach
        </select>
    </div>

    <!-- Folder Selector -->
    <div class="mb-3">
        <select class="form-select" wire:model="selectedFolder" wire:change="fetchImages">
            <option value="">-- Vælg folder --</option>
            @foreach($folders as $folder)
                <option value="{{ $folder['id'] }}">{{ str_repeat('--', $folder['depth'] ?? 0) . ' ' . $folder['name'] }}</option>
            @endforeach
        </select>
    </div>

    <!-- Drop Zone -->
    <div class="border border-dashed p-3 text-center mb-3" 
         style="cursor:pointer;" 
         wire:click="$refs.fileInput.click()" 
         wire:dragover.prevent
         wire:drop.prevent="$emit('handleDrop')">
        <p>Træk billeder her eller klik for at vælge filer</p>
        <input type="file" wire:model="uploadFiles" multiple accept="image/*" style="display:none" x-ref="fileInput">
    </div>

    <!-- Upload progress -->
    @if($isUploading)
        <div class="progress mb-3">
            <div class="progress-bar" role="progressbar" style="width: {{ ($uploadedCount/$totalFiles)*100 }}%">
                {{ $uploadedCount }}/{{ $totalFiles }}
            </div>
        </div>
    @endif

    <!-- Image list -->
    <div class="d-flex flex-wrap gap-2">
        @forelse($images as $image)
            <div class="position-relative" style="width:100px; height:100px;">
                <img src="{{ route('media.show',['filename'=>$image['file_name']]) }}" class="img-fluid border rounded" style="object-fit:cover; cursor:pointer" wire:click="preview({{ $image['id'] }})">
                <button class="btn btn-sm btn-danger position-absolute top-0 end-0" wire:click="deleteImage({{ $image['id'] }})">&times;</button>
            </div>
        @empty
            <p>Ingen billeder fundet</p>
        @endforelse
    </div>

    <!-- Preview modal -->
    @if($previewImage)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $previewImage['name'] ?? 'Preview' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('previewImage', null)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ route('media.show',['filename'=>$previewImage['file_name']]) }}" class="img-fluid">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('previewImage', null)">Luk</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
