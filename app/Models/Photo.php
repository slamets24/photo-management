<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_path',
        'event_id'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
