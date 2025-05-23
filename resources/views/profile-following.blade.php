<x-profile :sharedData="$sharedData" doctitle="Who {{ $sharedData['username'] }} Followers">
    <div class="list-group">
      @foreach ($following as $follow)
        <a wire:navigate href="/profile/{{ $follow->userBeingFollowed->username }}" class="list-group-item list-group-item-action">
          <img class="avatar-tiny" src="{{ $follow->userBeingFollowed->avatar }}" />
           {{ $follow->userBeingFollowed->username }}
        </a>
      @endforeach
    </div>
  </x-profile>