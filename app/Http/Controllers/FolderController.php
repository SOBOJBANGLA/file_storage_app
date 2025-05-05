<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id'
        ]);

        $folder = Folder::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'parent_id' => $request->parent_id
        ]);

        return back()->with('success', 'Folder created successfully!');
    }

    public function show(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $subfolders = $folder->children;
        $files = $folder->files;
        $folders = Folder::where('user_id', Auth::id())
            ->where('id', '!=', $folder->id)
            ->get();

        return view('folders.show', compact('folder', 'subfolders', 'files', 'folders'));
    }

    public function destroy(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        try {
            $folder->delete();
            return back()->with('success', 'Folder deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete the folder.');
        }
    }

    public function share(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $users = \App\Models\User::where('id', '!=', Auth::id())->get();
        $shares = $folder->shares()->with('user')->get();

        return view('folders.share', compact('folder', 'users', 'shares'));
    }

    public function storeShare(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|in:view,edit'
        ]);

        $folder->shares()->create([
            'shared_with_user_id' => $request->user_id,
            'permission' => $request->permission,
            'shareable_type' => Folder::class
        ]);

        return back()->with('success', 'Folder shared successfully!');
    }

    public function unshare(Folder $folder, \App\Models\FileShare $share)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $share->delete();

        return back()->with('success', 'Folder unshared successfully!');
    }

    public function copy(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $folders = Folder::where('user_id', Auth::id())
            ->where('id', '!=', $folder->id)
            ->get();

        return view('folders.copy', compact('folder', 'folders'));
    }

    public function storeCopy(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'target_folder_id' => 'required|exists:folders,id'
        ]);

        $targetFolder = Folder::find($request->target_folder_id);

        if ($targetFolder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $newFolder = $folder->replicate();
        $newFolder->parent_id = $targetFolder->id;
        $newFolder->save();

        return back()->with('success', 'Folder copied successfully!');
    }

    public function move(Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $folders = Folder::where('user_id', Auth::id())
            ->where('id', '!=', $folder->id)
            ->get();

        return view('folders.move', compact('folder', 'folders'));
    }

    public function storeMove(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'new_folder_id' => 'required|exists:folders,id'
        ]);

        $newFolder = Folder::find($request->new_folder_id);

        if ($newFolder->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $folder->parent_id = $newFolder->id;
        $folder->save();

        return back()->with('success', 'Folder moved successfully!');
    }
}