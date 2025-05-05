<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'path',
        'size',
        'mime_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function shares()
    {
        return $this->hasMany(FileShare::class, 'shareable_id')->where('shareable_type', self::class);
    }

    public function download()
    {
        if (!Storage::disk('public')->exists($this->path)) {
            throw new \Exception('File not found.');
        }

        return Response::download(
            Storage::disk('public')->path($this->path),
            $this->name
        );
    }
}
