<div class="w-100">
    <div class="file-cards-container">
        @foreach ($files as $file)
            <div class="card file-card mb-2 border" 
                 wire:click="openPreview({{ $file['id'] }})" 
                 style="cursor: pointer;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <!-- File icon/thumbnail -->
                        <div class="me-3">
                            @if(isset($file['type']) && str_contains($file['type'], 'image'))
                                <img src="{{ $file['src'] ?? '' }}" 
                                     alt="{{ $file['file_name'] ?? '' }}" 
                                     class="rounded" 
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="file-icon-placeholder mr-4 d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; background-color: #f8f9fa; border-radius: 5px;">
                                    @if(isset($file['type']) && str_contains($file['type'], 'pdf'))
                                        <i class="fas fa-file-pdf fa-lg text-danger"></i>
                                    @elseif(isset($file['type']) && (str_contains($file['type'], 'word') || str_contains($file['type'], 'document')))
                                        <i class="fas fa-file-word fa-lg text-primary"></i>
                                    @elseif(isset($file['type']) && (str_contains($file['type'], 'excel') || str_contains($file['type'], 'spreadsheet')))
                                        <i class="fas fa-file-excel fa-lg text-success"></i>
                                    @elseif(isset($file['type']) && str_contains($file['type'], 'zip'))
                                        <i class="fas fa-file-archive fa-lg text-warning"></i>
                                    @else
                                        <i class="fas fa-file fa-lg text-secondary"></i>
                                    @endif
                                </div>
                            @endif
                        </div>
                        
                        <!-- File info -->
                        <div class="flex-grow-1">
                            <div class="fw-medium text-truncate" style="font-size: 0.95rem;">
                                {{ $file['name'] ?? 'Unnamed file' }}
                            </div>
                            <div class="text-muted small mt-1">
                                <div>
                                    <i class="fas fa-file-alt me-1"></i>
                                    {{ $file['file_name'] ?? '' }}
                                </div>
                                @if(isset($file['size']))
                                <div class="mt-1">
                                    <i class="fas fa-hdd me-1"></i>
                                    {{ $file['size'] }}
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Preview indicator -->
                        <div class="ms-3">
                            <span class="badge bg-light text-primary border border-primary px-3 py-1">
                                <i class="fas fa-external-link-alt me-1"></i> Åbn
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bootstrap Modal for Preview -->
    <div class="modal fade @if($dialog) show @endif" 
         id="filePreviewModal" 
         tabindex="-1" 
         role="dialog" 
         @if($dialog) style="display: block;" @endif>
        
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>
                        Forhåndsvisning
                        @if($currentFile)
                            <small class="text-muted ms-2">- {{ $currentFile['name'] ?? '' }}</small>
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('dialog', false)"></button>
                </div>
                
            
                <!-- Preview Content -->
                @if($currentFile)
                <div class="modal-body p-0" style="height: 70vh;">
                    <!-- TILFØJ: onload event til iframe -->
                    <iframe src="{{ $currentFile['src'] ?? '#' }}" 
                            onload="window.livewire.emit('iframeLoaded', '{{ $currentFile['id'] }}')"
                            class="w-100 h-100 border-0"
                            title="File preview: {{ $currentFile['name'] ?? '' }}"></iframe>
                </div>
                
                <!-- File Info Footer -->
                <div class="modal-footer">
                    <div class="file-info small text-muted">
                        <i class="fas fa-file me-1"></i>
                        {{ $currentFile['file_name'] ?? '' }}
                        @if(isset($currentFile['size']))
                            <span class="mx-2">•</span>
                            <i class="fas fa-hdd me-1"></i>
                            {{ $currentFile['size'] }}
                        @endif
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" wire:click="$set('dialog', false)">
                            <i class="fas fa-times me-1"></i> Luk
                        </button>
                        @if(isset($currentFile['src']))
                        <a href="{{ $currentFile['src'] }}" 
                           target="_blank" 
                           class="btn btn-primary ms-2"
                           download="{{ $currentFile['file_name'] ?? 'download' }}">
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Backdrop when modal is open -->
    @if($dialog)
    <div class="modal-backdrop fade show"></div>
    @endif
</div>

<style>
/* File cards styling */
.file-card {
    transition: none;
    border: 1px solid #dee2e6;
}

.file-card:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

/* File icon placeholder */
.file-icon-placeholder {
    border: 1px solid #e9ecef;
}

/* Ensure full width */
.w-100 {
    width: 100% !important;
}

/* Text truncation for long file names */
.text-truncate {
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Modal styling */
.modal-content {
    border-radius: 8px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .file-card .card-body {
        padding: 1rem;
    }
    
    .file-card .fw-medium {
        font-size: 0.9rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>