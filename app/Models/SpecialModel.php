<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialModel extends Model
{
    use HasFactory;

    protected $table = 'special_models';

    public $fillable = [
        'name',
        'status'
    ];

    public function product()
    {
        return $this->belongsToMany(ProductModel::class, 'special_product_models', 'special_id', 'product_id');
    }
}
