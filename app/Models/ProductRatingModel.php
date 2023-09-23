<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRatingModel extends Model
{
    use HasFactory;

    protected $table = 'product_rating';

    protected $fillable = [
        'customer_id',
        'product_id',
        'rate',
        'status',
        'comment',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
}
