@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Rediger fil</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('media.update', $media->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nuværende fil:</label>
                            <p>{{ $media->file_name }}</p>
                        </div>

                        <div class="form-group mb-3">
                            <label for="file">Vælg ny fil</label>
                            <input type="file" class="form-control" id="file" name="file">
                            <small class="text-muted">Efterlad tom for at beholde eksisterende fil</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="collection">Samling</label>
                            <input type="text" class="form-control" id="collection" name="collection" 
                                   value="{{ old('collection', $media->collection_name) }}">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Opdater</button>
                            <a href="{{ route('media.show', $media->id) }}" class="btn btn-secondary">Annuller</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection