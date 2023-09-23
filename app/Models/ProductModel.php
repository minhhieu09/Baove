<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'product_models';

    protected $fillable = [
        'name',
        'type',
        'description',
        'status'
    ];

    public function productType()
    {
        return $this->hasOne(ProductTypeModel::class, 'id', 'type');
    }

    public function component()
    {
        return $this->hasMany(ProductComponentModel::class, 'product_id', 'id');
    }

    public function special()
    {
        return $this->belongsToMany(SpecialModel::class, 'special_product_models', 'product_id', 'special_id');
    }
}
