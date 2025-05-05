@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2 class="fw-bold">{{ $folder->name }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createSubfolderModal">
                    Create Subfolder
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                    Upload File
                </button>
                <a href="{{ route('folders.share', $folder) }}" class="btn btn-info">Share</a>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#copyFolderModal">
                    Copy
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#moveFolderModal">
                    Move
                </button>
            </div>
        </div>
    </div>

    {{-- Error & Success Messages --}}
    @if(session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    {{-- Create Subfolder Modal --}}
    <div class="modal fade" id="createSubfolderModal" tabindex="-1" aria-labelledby="createSubfolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createSubfolderModalLabel">Create Subfolder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('folders.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $folder->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folderName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Folder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Upload File Modal --}}
    <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadFileModalLabel">Upload File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="folder_id" value="{{ $folder->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Choose File</label>
                            <input type="file" name="file" id="file" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Copy Folder Modal --}}
    <div class="modal fade" id="copyFolderModal" tabindex="-1" aria-labelledby="copyFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="copyFolderModalLabel">Copy Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('folders.copy', $folder) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="target_folder_id" class="form-label">Select Target Folder</label>
                            <select name="target_folder_id" id="target_folder_id" class="form-select" required>
                                <option value="">Choose a folder...</option>
                                @foreach($folders as $f)
                                    @if($f->id !== $folder->id)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Copy Folder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Move Folder Modal --}}
    <div class="modal fade" id="moveFolderModal" tabindex="-1" aria-labelledby="moveFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveFolderModalLabel">Move Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('folders.move', $folder) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_folder_id" class="form-label">Select New Folder</label>
                            <select name="new_folder_id" id="new_folder_id" class="form-select" required>
                                <option value="">Choose a folder...</option>
                                @foreach($folders as $f)
                                    @if($f->id !== $folder->id)
                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Move Folder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Subfolders List --}}
    <div class="mb-4">
        <h4><i class="fas fa-folder text-warning"></i>Subfolders</h4>
        <div class="row">
            @forelse ($subfolders as $subfolder)
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-folder text-warning me-2"></i>{{ $subfolder->name }}</h5>
                            <div class="btn-group">
                                <a href="{{ route('folders.show', $subfolder) }}" class="btn btn-sm btn-primary">Open</a>
                                <form action="{{ route('folders.destroy', $subfolder) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No subfolders available.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Files List --}}
    <div>
        <h4>Files</h4>
        <ul class="list-group">
            @forelse ($files as $file)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $file->name }}</strong>
                        <br>
                        <small>{{ number_format($file->size / 1024, 2) }} KB | {{ $file->mime_type }}</small>
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
                <li class="list-group-item">No files in this folder.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
