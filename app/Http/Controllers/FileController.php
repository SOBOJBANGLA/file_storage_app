<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Models\FileShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function index()
    {
        $ownFiles = File::where('user_id', Auth::id())
            ->whereNull('folder_id')
            ->get();

        $sharedFiles = File::whereHas('shares', function ($query) {
            $query->where('shared_with_user_id', Auth::id());
        })->get();

        $folders = Folder::where('user_id', Auth::id())
            ->whereNull('parent_id')
            ->get();

        return view('files.index', compact('ownFiles', 'sharedFiles', 'folders'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048000', // 10MB max
            'folder_id' => 'nullable|exists:folders,id'
        ]);

        $file = $request->file('file');
        $path = $file->store('files', 'public');

        File::create([
            'user_id' => Auth::id(),
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'folder_id' => $request->folder_id
        ]);

        return back()->with('success', 'File uploaded successfully!');
    }

    public function download(File $file)
    {
        if ($file->user_id !== Auth::id() && !$file->shares()->where('shared_with_user_id', Auth::id())->exists()) {
            abort(403, 'Unauthorized');
        }

        try {
            return $file->download();
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function delete(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        try {
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }
            $file->delete();
            return back()->with('success', 'File deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete the file.');
        }
    }

    public function share(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $users = \App\Models\User::where('id', '!=', Auth::id())->get();
        $shares = $file->shares()->with('user')->get();
        
        return view('files.share', compact('file', 'users', 'shares'));
    }

    public function storeShare(Request $request, File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|in:view,edit'
        ]);

        $file->shares()->create([
            'shared_with_user_id' => $request->user_id,
            'permission' => $request->permission,
            'shareable_type' => File::class
        ]);

        return back()->with('success', 'File shared successfully!');
    }

    public function unshare(File $file, FileShare $share)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $share->delete();

        return back()->with('success', 'File unshared successfully!');
    }

    public function actions(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $folders = Folder::where('user_id', Auth::id())->get();
        return view('files.actions', compact('file', 'folders'));
    }

    public function copy(Request $request, File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'target_folder_id' => 'required|exists:folders,id'
        ]);

        $targetFolder = Folder::find($request->target_folder_id);

        if ($targetFolder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $newFile = $file->replicate();
        $newFile->folder_id = $targetFolder->id;
        $newFile->save();

        return back()->with('success', 'File copied successfully!');
    }

    public function move(Request $request, File $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'new_folder_id' => 'required|exists:folders,id'
        ]);

        $newFolder = Folder::find($request->new_folder_id);

        if ($newFolder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $file->folder_id = $newFolder->id;
        $file->save();

        return back()->with('success', 'File moved successfully!');
    }
}
