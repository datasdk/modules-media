@php
    $hasShowRoute = Route::has('media.public.show');
    $hasDownloadRoute = Route::has('media.public.download');
@endphp

@if($hasShowRoute || $hasDownloadRoute)
    <!-- Åbn i Modal Knap -->
    <button type="button" 
            class="btn btn-sm btn-outline-primary" 
            title="Åbn fil" 
            data-bs-toggle="modal" 
            data-bs-target="#mediaModal{{ $media->id }}">
        <i class="fas fa-external-link-alt"></i> Åbn
    </button>

    <!-- Download Knap -->
    @if($hasDownloadRoute)
        <a href="{{ route('media.public.download', ['filename' => $media->file_name]) }}" 
           class="btn btn-sm btn-outline-secondary ms-1" 
           title="Download fil"
           download>
            <i class="fas fa-download"></i> Download
        </a>
    @endif

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="mediaModal{{ $media->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">
                        <i class="fas fa-file me-2"></i>
                        {{ $media->file_name }}
                        <small class="text-muted ms-2">
                            {{ $media->size }} bytes
                        </small>
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Luk"></button>
                </div>
                
                <div class="modal-body p-0" style="min-height: 500px;">
                    <iframe src="{{ $hasShowRoute ? route('media.public.show', ['filename' => $media->file_name]) : '#' }}" 
                            style="width: 100%; height: 70vh; border: none;"
                            title="Visning af {{ $media->file_name }}"
                            allowfullscreen>
                    </iframe>
                </div>
                
                <div class="modal-footer">
                    <div class="me-auto">
                        @if($hasShowRoute)
                            <a href="{{ route('media.public.show', ['filename' => $media->file_name]) }}" 
                               target="_blank" 
                               class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-external-link-alt me-1"></i>Åbn i browser
                            </a>
                        @endif
                        
                        @if($hasDownloadRoute)
                            <a href="{{ route('media.public.download', ['filename' => $media->file_name]) }}" 
                               class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download me-1"></i>Download fil
                            </a>
                        @endif
                    </div>
                    
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Luk</button>
                </div>
            </div>
        </div>
    </div>
@endif
