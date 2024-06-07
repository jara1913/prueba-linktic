<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = 'tasks';

    /**
     * Fillable fields
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date'
    ];
}
