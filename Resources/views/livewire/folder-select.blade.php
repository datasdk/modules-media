<div class="mb-4">

    <div class="flex gap-2 mb-2">
        <select wire:model="selectedFolder" class="form-select">
            @foreach($folders as $folder)
                @php
                    $prefix = str_repeat('— ', $folder['depth'] ?? 0);
                @endphp
                <option value="{{ $folder['id'] }}">{{ $prefix }}{{ $folder['name'] }}</option>
            @endforeach
        </select>

        <input type="text" wire:model="newFolderName" placeholder="Ny mappe navn" class="form-input">
        <button type="button" class="btn btn-primary" wire:click="createFolder">Opret ny mappe</button>
    </div>

    {{-- Liste til drag & drop (valgfri) --}}
    <ul class="list-group mt-2">
        @foreach($folders as $folder)
            <li class="list-group-item"
                draggable="true"
                wire:dragstart.prevent="$emit('dragImageStart', {{ $folder['id'] }})"
                wire:drop.prevent="$emit('moveImage', {{ $folder['id'] }})">
                {{ str_repeat('— ', $folder['depth'] ?? 0) }} {{ $folder['name'] }}
            </li>
        @endforeach
    </ul>

</div>
