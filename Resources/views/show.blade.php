@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Fil detaljer</div>

                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">ID:</dt>
                        <dd class="col-sm-9">{{ $media->id }}</dd>

                        <dt class="col-sm-3">Filnavn:</dt>
                        <dd class="col-sm-9">{{ $media->file_name }}</dd>

                        <dt class="col-sm-3">Filtype:</dt>
                        <dd class="col-sm-9">{{ $media->mime_type }}</dd>

                        <dt class="col-sm-3">Størrelse:</dt>
                        <dd class="col-sm-9">{{ number_format($media->size / 1024, 2) }} KB</dd>

                        <dt class="col-sm-3">Samling:</dt>
                        <dd class="col-sm-9">{{ $media->collection_name }}</dd>

                        <dt class="col-sm-3">Disk:</dt>
                        <dd class="col-sm-9">{{ $media->disk }}</dd>

                        <dt class="col-sm-3">Oprettet:</dt>
                        <dd class="col-sm-9">{{ $media->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-3">Forhåndsvisning:</dt>
                        <dd class="col-sm-9">
                            @if(str_starts_with($media->mime_type, 'image/'))
                                <img src="{{ $media->getPublicUrl() }}" alt="{{ $media->file_name }}" 
                                     style="max-width: 200px; max-height: 200px;">
                            @else
                                <i class="fas fa-file" style="font-size: 48px;"></i>
                            @endif
                        </dd>
                    </dl>

                    <div class="mt-3">
                        <a href="{{ route('media.index') }}" class="btn btn-secondary">Tilbage</a>
                        <a href="{{ route('media.edit', $media->id) }}" class="btn btn-warning">Rediger</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection