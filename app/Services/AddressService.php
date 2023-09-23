<?php

namespace App\Services;

use App\Models\AddressModel;

class AddressService extends BaseService
{
    public function __construct(AddressModel $addressModel)
    {
        $this->model = $addressModel;
    }

    public function getCity()
    {
        return $this->model
                    ->select('city_id', 'city_name')
                    ->orderBy('city_id')
                    ->groupBy('city_id')
                    ->get();
    }
}
