<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $table = 'states';
    protected $fillable = [
        'state',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function lga()
    {
        return $this->hasMany(Lga::class);
    }
}
