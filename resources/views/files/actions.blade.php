@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2 class="mb-4">File Actions: {{ $file->name }}</h2>
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

    {{-- Copy Form --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Copy File</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('files.copy', $file) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="target_folder_id" class="form-label">Select Target Folder</label>
                    <select name="target_folder_id" id="target_folder_id" class="form-select" required>
                        <option value="">Choose a folder...</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Copy</button>
            </form>
        </div>
    </div>

    {{-- Move Form --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Move File</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('files.move', $file) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="new_folder_id" class="form-label">Select New Folder</label>
                    <select name="new_folder_id" id="new_folder_id" class="form-select" required>
                        <option value="">Choose a folder...</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Move</button>
            </form>
        </div>
    </div>
</div>
@endsection
