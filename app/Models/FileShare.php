<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FileShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'shareable_id',
        'shareable_type',
        'shared_with_user_id',
        'permission'
    ];

    /**
     * Get the file that is shared.
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Get the user the file is shared with.
     */
    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    /**
     * Get the user relationship (alias for sharedWith).
     */
    public function user()
    {
        return $this->sharedWith();
    }

    public function shareable()
    {
        return $this->morphTo();
    }
}
