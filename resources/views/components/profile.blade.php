<x-layout>

    @php
        extract($sharedData);
    @endphp

    <div class="container py-md-5 container--narrow">
        <h2>
            {{-- @if (isset($avatar)) --}}

            <img class="avatar-small" src="{{$avatar}}" />
            {{-- @endif --}}
            {{$username}}
          @auth
          @if (! $currentlyFollowing AND auth()->user()->username != $username)
          <form class="ml-2 d-inline" action="/create-follow/{{$username}}" method="GET">
            @csrf
            <button class="btn btn-primary btn-sm">Follow <i class="fas fa-user-plus"></i></button>
            <!-- <button class="btn btn-danger btn-sm">Stop Following <i class="fas fa-user-times"></i></button> -->
           </form>
          @endif
          @if ($currentlyFollowing)
          <form class="ml-2 d-inline" action="/remove-follow/{{$username}}" method="GET">
            @csrf
            {{-- <button class="btn btn-primary btn-sm">Follow <i class="fas fa-user-plus"></i></button> --}}
            <button class="btn btn-danger btn-sm">Stop Following <i class="fas fa-user-times"></i></button>
          </form>
          @endif
          @if (auth()->user()->username == $username )
          <a href="/manage-avatar" class="btn btn-secondary btn-sm">Manage Avatar</a>
          @endif
           @endauth
          </h2>

        <div class="profile-nav nav nav-tabs pt-2 mb-4">
          <a href="/profile/{{$username}}" class="profile-nav-link nav-item nav-link {{ Request::segment(3) =="" ? "active" : "" }} ">Posts: {{$postCount}}</a>
          <a href="/profile/{{$username}}/followers" class="profile-nav-link nav-item nav-link {{ Request::segment(3) =="followers" ? "active" : "" }}">Followers: 3</a>
          <a href="/profile/{{$username}}/following" class="profile-nav-link nav-item nav-link {{ Request::segment(3) =="following" ? "active" : "" }}">Following: 2</a>
        </div>

        <div class="profile-slot-content">
            {{$slot}}
        </div>


</x-layout>
