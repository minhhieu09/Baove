<?php

namespace App\Services;

use App\Models\ProductColorModel;

class ProductColorService extends BaseService
{
    public function __construct(ProductColorModel $productColorModel)
    {
        $this->model = $productColorModel;
    }

    public function getColorByIds(array $ids)
    {
        return $this->model
                    ->whereIn('id', $ids)
                    ->get();
    }
}
