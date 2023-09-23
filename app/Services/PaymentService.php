<?php

namespace App\Services;

use App\Models\PaymentModel;

class PaymentService extends BaseService
{
    public function __construct(PaymentModel $paymentModel)
    {
        $this->model = $paymentModel;
    }

    public function getEarningOfMonth($month)
    {
        return $this->model
                    ->where('status', 1)
                    ->whereMonth('created_at', $month)
                    ->get();
    }
}
