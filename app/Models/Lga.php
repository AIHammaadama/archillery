<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    use HasFactory;
    protected $table = 'lgas';
    protected $fillable = [
        'state_id',
        'lga',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
