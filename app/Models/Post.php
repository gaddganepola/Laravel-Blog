<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;
    use HasFactory, Notifiable;
    //
    protected $fillable = [
        'user_id',
        'title',
        'body',
    ];

    public function toSearchableArray()
    {
        //Which cloumns need to search through 
        return [
            'title' => $this->title,
            'body' => $this->body
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
