<div>
    <!-- Knappen til modal -->
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="collapse" data-bs-target="#imageSelectCollapse">
        Vælg billede...
    </button>

    <!-- Selected images -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach($selectedImages as $image)
            <div class="position-relative">
                <img src="{{ route('media.show', ['filename' => $image['file_name']]) }}" width="60" height="60" class="border rounded" style="object-fit:cover; cursor:pointer" wire:click="preview({{ $image['id'] }})">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" wire:click="removeImage({{ $image['id'] }})">&times;</button>
            </div>
        @endforeach
    </div>

    <!-- Collapse for available images -->
    <div class="collapse" id="imageSelectCollapse">
        <div class="card card-body">
            <div class="mb-3">
                <input type="file" wire:model="uploadFiles" multiple class="form-control">
            </div>

            @if($loading)
                <div>Loader billeder...</div>
            @else
                <div class="d-flex flex-wrap gap-2">
                    @foreach($availableImages as $image)
                        <div class="position-relative border rounded p-1" style="width:80px; height:80px; cursor:pointer" wire:click="toggleSelect({{ $image['id'] }})">
                            <img src="{{ route('media.show', ['filename' => $image['file_name']]) }}" class="w-100 h-100" style="object-fit:cover">
                            @if(collect($selectedImages)->contains('id', $image['id']))
                                <span class="position-absolute top-0 start-0 badge bg-primary">✓</span>
                            @endif
                            <button type="button" class="btn btn-sm btn-secondary position-absolute bottom-0 start-0" wire:click.stop="preview({{ $image['id'] }})">Vis</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Preview modal -->
    @if($previewImage)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $previewImage['name'] ?? 'Preview' }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('previewImage', null)"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ route('media.show', ['filename' => $previewImage['file_name']]) }}" class="img-fluid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('previewImage', null)">Luk</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
