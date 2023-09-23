<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\ProductModel;
use App\Services\CustomerService;
use App\Services\PaymentService;
use App\Services\ProductColorService;
use App\Services\ProductComponentService;
use App\Services\ProductSpecialService;
use App\Services\ProductTypeService;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{

    protected $paymentService;

    public function __construct(
        PaymentService $paymentService
    )
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $assign['invoices'] = $this->paymentService->paginate(10);

        return view('admin.invoice.index', $assign);
    }

    public function detail($orderId)
    {
        $assign['invoice'] = $this->paymentService->find('order_id', '=', $orderId)->first();
        $assign['orderId'] = $orderId;
        $assign['payments'] = json_decode($assign['invoice']->payment_info);

        return view('admin.invoice.edit', $assign);
    }

    public function update(Request $request)
    {
        $order_id = $request->input('order_id');
        $payment = $this->paymentService->find('order_id', '=', $order_id)->firstOrFail();
        $payment->delivery = $request->input('delivery');
        $payment->save();

        return back()->with(['status' => 'success', 'message' => 'Cập thật thành công']);
    }
}
