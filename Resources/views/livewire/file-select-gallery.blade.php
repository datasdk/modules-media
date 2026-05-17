<div>
    <!-- Valgte filer -->
    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach($selectedFiles as $file)
            <div class="position-relative" style="cursor:pointer;" wire:click="toggleSelect({{ $file['id'] }})">
                @if($file['isImage'])
                    <img src="{{ asset('storage/'.$file['file_name']) }}" width="60" height="60" class="border rounded">
                @elseif($file['isPDF'])
                    <img src="/Modules/Media/images/pdf-icon.png" width="60" height="60" class="border rounded">
                @endif
                <span class="position-absolute top-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:12px;">✓</span>
            </div>
        @endforeach
    </div>

    <!-- Upload / Vælg filer -->
    <button type="button" class="btn btn-primary mb-3" wire:click="$set('selectModal', true)">Vælg / Upload filer...</button>

    <!-- Modal -->
    <div class="modal fade @if($selectModal) show @endif" tabindex="-1" style="@if($selectModal) display:block; @endif">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vælg filer</h5>
                    <button type="button" class="btn-close" wire:click="$set('selectModal', false)"></button>
                </div>
                <div class="modal-body">
                    <input type="file" multiple wire:model="uploadFiles" class="form-control mb-2">
                    @if($isUploading)
                        <div>Uploader {{$uploadedCount}}/{{ count($uploadFiles) }} filer...</div>
                    @endif

                    <div class="row g-2 mt-2">
                        @foreach($availableFiles as $file)
                            <div class="col-3 border p-1 position-relative" style="cursor:pointer;" wire:click="toggleSelect({{ $file['id'] }})">
                                @if($file['isImage'])
                                    <img src="{{ asset('storage/'.$file['file_name']) }}" class="w-100 h-50 object-fit-cover">
                                @elseif($file['isPDF'])
                                    <img src="/Modules/Media/images/pdf-icon.png" class="w-100 h-50 object-fit-cover">
                                @endif
                                @if(collect($selectedFiles)->firstWhere('id', $file['id']))
                                    <span class="position-absolute top-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:20px; height:20px; font-size:12px;">✓</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('selectModal', false)">Luk</button>
                </div>
            </div>
        </div>
    </div>

    @if($selectModal)
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
