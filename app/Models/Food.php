<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'customer_id',
        'turn_id',
        'created_at',
        'updated_at',
    ];

    public function turn() {
        return $this->belongsTo('App\Models\Turn');
    }
    public function customer() {
        return $this->belongsTo('App\Models\Customer');
    }
}
