<?php

namespace App\Services;


use App\Models\User;

class CustomerService extends BaseService
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }
}
