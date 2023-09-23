<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductComponentModel extends Model
{
    use HasFactory;

    protected $table = 'product_component_models';

    public $fillable = [
        'product_id',
        'memory',
        'color_id',
        'amount',
        'price',
        'image',
    ];

    public function product()
    {
        return $this->belongsTo(ProductModel::class, 'product_id', 'id');
    }

    public function color()
    {
        return $this->hasOne(ProductColorModel::class, 'id', 'color_id');
    }
}
