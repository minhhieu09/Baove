<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialProductModel extends Model
{
    use HasFactory;

    protected $table = 'special_product_models';

    public $fillable = [
        'special_id',
        'product_id'
    ];
}
