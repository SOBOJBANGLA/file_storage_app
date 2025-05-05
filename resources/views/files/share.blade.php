@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2 class="mb-4">Share File: {{ $file->name }}</h2>
        </div>
    </div>

    {{-- Error Message --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Share Form --}}
    <form action="{{ route('files.storeShare', $file) }}" method="POST" class="mb-4">
        @csrf
        <div class="mb-3">
            <label for="user_id" class="form-label">Select User</label>
            <select name="user_id" id="user_id" class="form-select" required aria-label="Select user to share the file with">
                <option value="">Choose a user...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="permission" class="form-label">Permission</label>
            <select name="permission" id="permission" class="form-select" required aria-label="Select permission level">
                <option value="view">View</option>
                <option value="edit">Edit</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Share</button>
    </form>

    {{-- Shared Users List --}}
    <div>
        <h4>Shared With</h4>
        <ul class="list-group">
            @forelse ($shares as $share)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $share->user->name }}</strong>
                        <br>
                        <small>Permission: {{ ucfirst($share->permission) }}</small>
                    </div>
                    <form action="{{ route('files.unshare', [$file, $share]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove access for this user?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Remove</button>
                    </form>
                </li>
            @empty
                <li class="list-group-item">No users shared with.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
