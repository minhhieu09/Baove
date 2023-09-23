<?php

namespace App\Services;

use App\Models\ProductComponentModel;

class ProductComponentService extends BaseService
{
    public function __construct(ProductComponentModel $productComponentModel)
    {
        $this->model = $productComponentModel;
    }

    public function deleteByCondition($condition)
    {
        return $this->model
                    ->where('product_id', $condition)
                    ->delete();
    }

    public function isExist(array $condition, $id = null)
    {
        return $this->model
                    ->where('product_id', $condition['product_id'])
                    ->where('memory', $condition['memory'])
                    ->where('color_id', $condition['color'])
                    ->where('id', '<>', $id)
                    ->first();
    }

    public function countProductId($productId)
    {
        return $this->model
                    ->where('product_id', $productId)
                    ->count();
    }

    public function getColor($condition)
    {
        return $this->model
                    ->where('product_id', $condition)
                    ->get();
    }

    public function getMemory($productId, $colorId)
    {
        return $this->model
                    ->where('product_id', $productId)
                    ->where('color_id', $colorId)
                    ->get();
    }
}
