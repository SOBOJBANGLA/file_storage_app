@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Your Files</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createFolderModal">
        <i class="fas fa-folder-plus me-1"></i> Create Folder
    </button>
</div>

<!-- Alerts -->
@includeWhen(session('success'), 'components.alert', ['type' => 'success', 'message' => session('success')])
@includeWhen(session('error'), 'components.alert', ['type' => 'danger', 'message' => session('error')])

<!-- Upload Form -->
<form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="mb-4">
    @csrf
    <div class="mb-3">
        <label for="file" class="form-label">Choose File</label>
        <input type="file" name="file" id="file" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">
        <i class="fas fa-upload"></i> Upload
    </button>
</form>

<!-- Folders -->
<div class="mb-5">
    <h4><i class="fas fa-folder text-warning"></i> Folders</h4>
    <div class="row">
        @forelse ($folders as $folder)
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><i class="fas fa-folder text-warning me-2"></i>{{ $folder->name }}</h5>
                        <div class="mt-auto">
                            <a href="{{ route('folders.show', $folder) }}" class="btn btn-sm btn-outline-primary">Open</a>
                            <form action="{{ route('folders.destroy', $folder) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">No folders created yet.</p>
        @endforelse
    </div>
</div>

<!-- Own Files -->
<div class="mb-5">
    <h4><i class="fas fa-file-alt"></i> Your Files</h4>
    <ul class="list-group">
        @forelse ($ownFiles as $file)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $file->name }}</strong>
                    <br><small>{{ number_format($file->size / 1024, 2) }} KB | {{ $file->mime_type }}</small>
                </div>
                <div class="btn-group">
                    <a href="{{ route('files.share', $file) }}" class="btn btn-sm btn-info">Share</a>
                    <a href="{{ route('files.actions', $file) }}" class="btn btn-sm btn-warning">Actions</a>
                    <a href="{{ route('download', $file) }}" class="btn btn-sm btn-success">Download</a>
                    <form action="{{ route('delete', $file) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">No files uploaded yet.</li>
        @endforelse
    </ul>
</div>

<!-- Shared Files -->
<div>
    <h4><i class="fas fa-share-alt"></i> Shared With You</h4>
    <ul class="list-group">
        @forelse ($sharedFiles as $file)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $file->name }}</strong>
                    <br><small>Shared by {{ $file->user->name }} | {{ number_format($file->size / 1024, 2) }} KB | {{ $file->mime_type }}</small>
                </div>
                <a href="{{ route('download', $file) }}" class="btn btn-sm btn-success">Download</a>
            </li>
        @empty
            <li class="list-group-item text-muted">No files shared with you yet.</li>
        @endforelse
    </ul>
</div>

<!-- Modal: Create Folder -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('folders.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="createFolderLabel">Create New Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="folderName" class="form-label">Folder Name</label>
                    <input type="text" name="name" id="folderName" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" type="submit">Create Folder</button>
            </div>
        </form>
    </div>
</div>
@endsection
