<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StripeController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function success(Request $request)
    {
        $dataRequest = $request->all();

        try {
            $this->paymentService->updateWithCondition(['status' => 1], 'order_id', $dataRequest['order_id']);
            Session::pull('cart');
            return redirect()->to(route(STORE_CART))->with(['status' => 'success', 'message' => 'Thanh toán thành công']);
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
