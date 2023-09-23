<?php

namespace App\Services;

use App\Models\ProductTypeModel;
use Illuminate\Support\Facades\Session;

class ProductTypeService extends BaseService
{
    public function __construct(ProductTypeModel $productTypeModel)
    {
        $this->model = $productTypeModel;
    }

}
