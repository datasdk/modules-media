@if(str_starts_with($media->mime_type, 'image/'))

    <img src="{{ $media->getThumbUrl() }}" 
         alt="{{ $media->file_name }}" 
         style="max-width: 50px; max-height: 50px; object-fit: cover;">

@else

    @php
        $fileIcon = 'fa-file'; // Default icon
        $fileColor = '#6c757d'; // Default color (gray)
        
        // Check MIME type or file extension for specific icons
        if (str_contains($media->mime_type, 'pdf')) {
            $fileIcon = 'fa-file-pdf';
            $fileColor = '#dc3545'; // Red
        } elseif (str_contains($media->mime_type, 'word') || 
                  str_contains($media->mime_type, 'document') ||
                  in_array(pathinfo($media->file_name, PATHINFO_EXTENSION), ['doc', 'docx', 'odt'])) {
            $fileIcon = 'fa-file-word';
            $fileColor = '#2b579a'; // Blue
        } elseif (str_contains($media->mime_type, 'excel') || 
                  str_contains($media->mime_type, 'spreadsheet') ||
                  in_array(pathinfo($media->file_name, PATHINFO_EXTENSION), ['xls', 'xlsx', 'csv'])) {
            $fileIcon = 'fa-file-excel';
            $fileColor = '#217346'; // Green
        } elseif (str_contains($media->mime_type, 'powerpoint') || 
                  str_contains($media->mime_type, 'presentation') ||
                  in_array(pathinfo($media->file_name, PATHINFO_EXTENSION), ['ppt', 'pptx'])) {
            $fileIcon = 'fa-file-powerpoint';
            $fileColor = '#d24726'; // Orange
        } elseif (str_contains($media->mime_type, 'zip') || 
                  str_contains($media->mime_type, 'compressed') ||
                  in_array(pathinfo($media->file_name, PATHINFO_EXTENSION), ['zip', 'rar', '7z', 'tar', 'gz'])) {
            $fileIcon = 'fa-file-archive';
            $fileColor = '#795548'; // Brown
        } elseif (str_contains($media->mime_type, 'audio')) {
            $fileIcon = 'fa-file-audio';
            $fileColor = '#9c27b0'; // Purple
        } elseif (str_contains($media->mime_type, 'video')) {
            $fileIcon = 'fa-file-video';
            $fileColor = '#ff9800'; // Orange
        } elseif (str_contains($media->mime_type, 'text') || 
                  in_array(pathinfo($media->file_name, PATHINFO_EXTENSION), ['txt', 'md', 'log'])) {
            $fileIcon = 'fa-file-alt';
            $fileColor = '#2196f3'; // Blue
        } elseif (str_contains($media->mime_type, 'code') || 
                  in_array(pathinfo($media->file_name, PATHINFO_EXTENSION), ['js', 'php', 'html', 'css', 'json', 'xml'])) {
            $fileIcon = 'fa-file-code';
            $fileColor = '#f44336'; // Red
        } elseif (str_contains($media->file_name, '.env') || 
                  str_contains($media->file_name, 'config')) {
            $fileIcon = 'fa-cog';
            $fileColor = '#607d8b'; // Blue gray
        }
    @endphp
    
    <div style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; 
                background: #f8f9fa; border-radius: 4px; position: relative;"
         title="{{ $media->file_name }} ({{ $media->mime_type }})">
        <i class="fas {{ $fileIcon }}" style="font-size: 24px; color: {{ $fileColor }};"></i>
        
        <!-- File extension badge -->
        @php
            $extension = strtoupper(pathinfo($media->file_name, PATHINFO_EXTENSION));
            if (strlen($extension) > 0 && strlen($extension) <= 5) {
        @endphp
            <div style="position: absolute; bottom: 2px; right: 2px; background: rgba(0,0,0,0.7); 
                        color: white; font-size: 8px; padding: 1px 3px; border-radius: 2px;">
                {{ $extension }}
            </div>
        @php
            }
        @endphp
    </div>
@endif