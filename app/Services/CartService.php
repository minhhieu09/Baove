<?php

namespace App\Services;

use App\Models\CartModel;

class CartService extends BaseService
{
    public function __construct(CartModel $cartModel)
    {
        $this->model = $cartModel;
    }
}
