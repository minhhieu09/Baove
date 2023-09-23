<?php

namespace App\Services;

use App\Models\ProductModel;

class ProductService extends BaseService
{
    public function __construct(ProductModel $productModel)
    {
        $this->model = $productModel;
    }

    public function getSameProduct($productId, $type)
    {
        return $this->model
                    ->where('id', '!=', $productId)
                    ->where('type', $type)
                    ->where('status', 1)
                    ->take(4)
                    ->get();
    }

    public function filterProduct($productIds, $typeId)
    {
        return $this->model
                    ->whereIn('id', $productIds)
                    ->whereHas('productType', function ($q) use ($typeId){
                        if (empty($typeId)) {
                            $query =  'id != 0';
                        } else {
                            $query = 'id = ' . $typeId;
                        }
                        return $q->whereRaw($query);
                    })
                    ->get();
    }
}
