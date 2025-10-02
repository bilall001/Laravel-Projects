<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectAsset extends Model
{
    use HasFactory;
     protected $fillable = ['disk','path','original_name','uploaded_by'];

    public function project() {
        return $this->belongsTo(Project::class);
    }

    // Helper for URL
    public function getUrlAttribute(): string
    {
        // works with the 'public' disk + storage:link
        return Storage::url($this->path);
    }

    /** Delete the underlying file when the DB row is deleted */
    protected static function booted(): void
    {
        static::deleting(function (ProjectAsset $asset) {
            // ignore errors if file is already gone
            try {
                Storage::disk($asset->disk ?? 'public')->delete($asset->path);
            } catch (\Throwable $e) {
                // swallow
            }
        });
    }
}
