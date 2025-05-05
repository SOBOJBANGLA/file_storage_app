@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Share Folder: {{ $folder->name }}</h4>
                </div>
                <div class="card-body">

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Error Message --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Share Form --}}
                    <form action="{{ route('folders.storeShare', $folder) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select User</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">Choose a user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="permission" class="form-label">Permission</label>
                            <select name="permission" id="permission" class="form-select" required>
                                <option value="view">View Only</option>
                                <option value="edit">Edit</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Share Folder</button>
                    </form>

                    <hr>

                    {{-- Shared With List --}}
                    <h5>Shared With</h5>
                    <ul class="list-group">
                        @forelse($shares as $share)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $share->user->name }}</strong><br>
                                    <small>{{ $share->user->email }}</small>
                                    <br>
                                    <small>Permission: {{ ucfirst($share->permission) }}</small>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">No shares yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
