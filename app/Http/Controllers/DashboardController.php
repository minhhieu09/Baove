<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $customerService;
    protected $paymentService;

    public function __construct(
        CustomerService $customerService,
        PaymentService $paymentService
    )
    {
        $this->paymentService = $paymentService;
        $this->customerService = $customerService;
    }

    public function countDeliverySuccess()
    {
        $deliveries = $this->paymentService->find('delivery', '=', 1)->count();
        return response()->json(['data' => $deliveries]);
    }

    public function countCustomer()
    {
        $customers = $this->customerService->all()->count();
        return response()->json(['data' => $customers]);
    }

    public function countOrder()
    {
        $orders = $this->paymentService->all()->count();
        return response()->json(['data' => $orders]);
    }
}
