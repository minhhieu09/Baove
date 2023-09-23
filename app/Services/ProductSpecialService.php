<?php

namespace App\Services;

use App\Models\SpecialModel;

class ProductSpecialService extends BaseService
{
    public function __construct(SpecialModel $specialModel)
    {
        $this->model = $specialModel;
    }
}
