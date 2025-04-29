<form wire:submit="uploadavatar" action="/manage-avatar" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <input wire:loading.attr="disabled" wire:target="avatar" wire:model="avatar" type="file" name="avatar">
        @error('avatar')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    {{-- <button type="submit">sumbit</button> --}}
    <button wire:loading.attr="disabled" wire:target="avatar" class="btn btn-primary">Save</button>
</form>