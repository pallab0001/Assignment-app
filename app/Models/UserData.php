<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'email', 'photos'];

    // ⚡ ADD this line to fix the table name
    protected $table = 'user_datas';
}
