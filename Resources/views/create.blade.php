@extends('layouts.app')

@section('actions')
<a href="{{ route('media.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left mr-2"></i> Tilbage til oversigt
</a>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upload fil</h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            @foreach($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <!-- File Upload -->
                        <div class="form-group">
                            <label for="file">Vælg fil</label>
                            <div class="custom-file">
                                <input type="file" 
                                       class="custom-file-input" 
                                       id="file" 
                                       name="file" 
                                       accept="image/*,.pdf,.doc,.docx,.txt" 
                                       required
                                       onchange="updateFileName()">
                                <label class="custom-file-label" for="file" id="fileLabel">
                                    Vælg en fil
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Understøttede formater: Billeder (jpg, png, gif), PDF, Word, tekstfiler
                            </small>
                        </div>

                        <!-- Collection Selection -->
                        <div class="form-group">
                            <label for="collection">Samling</label>
                            <select class="form-control" id="collection" name="collection">
                                <option value="uploads" selected>uploads</option>
                                <option value="documents">Dokumenter</option>
                                <option value="images">Billeder</option>
                                <option value="temp">Midlertidige</option>
                            </select>
                            <small class="form-text text-muted">
                                Vælg hvor filen skal gemmes
                            </small>
                        </div>

                        <!-- File Preview (optional) -->
                        <div class="form-group" id="previewContainer" style="display: none;">
                            <label>Forhåndsvisning</label>
                            <div class="mt-2">
                                <img id="imagePreview" class="img-thumbnail" style="max-width: 200px; display: none;">
                                <div id="filePreview" class="p-2 border rounded" style="display: none;">
                                    <i class="fas fa-file fa-3x"></i>
                                    <div id="fileNamePreview" class="mt-2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-upload mr-2"></i> Upload fil
                            </button>
                            <a href="{{ route('media.index') }}" class="btn btn-outline-secondary ml-2">
                                Annuller
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file');
    const imagePreview = document.getElementById('imagePreview');
    const filePreview = document.getElementById('filePreview');
    const fileNamePreview = document.getElementById('fileNamePreview');
    const previewContainer = document.getElementById('previewContainer');
    const submitBtn = document.getElementById('submitBtn');
    const fileLabel = document.getElementById('fileLabel');
    
    // Update file name label
    window.updateFileName = function() {
        if (fileInput.files.length > 0) {
            const fileName = fileInput.files[0].name;
            fileLabel.textContent = fileName;
            
            // Show preview
            previewContainer.style.display = 'block';
            
            // Check if it's an image
            if (fileInput.files[0].type.startsWith('image/')) {
                // Create preview for images
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    filePreview.style.display = 'none';
                };
                reader.readAsDataURL(fileInput.files[0]);
            } else {
                // Show file icon for non-images
                imagePreview.style.display = 'none';
                filePreview.style.display = 'block';
                fileNamePreview.textContent = fileName;
            }
        } else {
            fileLabel.textContent = 'Vælg en fil';
            previewContainer.style.display = 'none';
        }
    };
    
    // Form submission handling
    const uploadForm = document.getElementById('uploadForm');
    uploadForm.addEventListener('submit', function(e) {
        // Simple validation
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Vælg venligst en fil at uploade');
            return;
        }
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploader...';
    });
    
    // Bootstrap file input enhancement
    $(document).ready(function() {
        bsCustomFileInput.init();
    });
});
</script>
@endsection