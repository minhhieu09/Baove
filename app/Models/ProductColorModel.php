<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColorModel extends Model
{
    use HasFactory;

    protected $table = 'product_color_models';

    public $fillable = [
        'name',
        'color_code'
    ];

    public function component()
    {
        return $this->belongsTo(ProductComponentModel::class, 'id', 'color_id');
    }
}
