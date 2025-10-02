<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskImage extends Model
{
    use HasFactory;
     protected $fillable = [
        'task_id',
        'path',
        'mime',
        'size',
        'alt_text',
        'width',
        'height',
        'uploaded_by',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader()
    {
        return $this->belongsTo(AddUser::class, 'uploaded_by');
    }
}
