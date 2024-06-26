<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status', 'deadline', 'priority', 'user_id'];
    protected $nullable = ['user_id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
