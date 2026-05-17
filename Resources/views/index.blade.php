@extends('layouts.app')

@section('actions')
<a href="{{ route('media.create') }}" class="btn btn-primary">Upload fil</a>
@endsection

@section('content')
<livewire:table 
    :config="Modules\Media\Tables\MediaTable::class" 
/>
@endsection