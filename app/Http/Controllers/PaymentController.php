<?php

namespace App\Http\Controllers;

use App\Models\PaymentModel;
use App\Models\ProductComponentModel;
use App\Services\PaymentService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $stripeService;

    public function __construct(
        PaymentService          $paymentService,
        StripeService           $stripeService
    )
    {
        $this->paymentService           = $paymentService;
        $this->stripeService            = $stripeService;
    }
    //
    public function payment(Request $request){
        if (!Auth::guard('web')->check()){
            return redirect()->to(route(STORE_LOGIN));
        }

        $dataRequest = $request->all();

        if (empty($dataRequest['component'])){
            return redirect()->to(route(STORE));
        }

        $paymentInfo = [];

        foreach ($dataRequest['component'] as $key => $value){
            $component = ProductComponentModel::where('id', $value)->first();

            if($component->amount < $dataRequest['amount'][$key]){
                return back()->with([
                    'status' => 'fail',
                    'message' => 'Thanh toán thất bại do hết hàng này'
                ]);
            }
            $component->amount -= $dataRequest['amount'][$key];
            $component->save();
            $paymentInfo[] = array(
                'component' => $value,
                'amount' => $dataRequest['amount'][$key],
                'product_id' => $component->product->name,
                'memory' => $component->memory,
                'color' => $component->color->name,
                'price' => $component->price,
            );
        }
        $data = [
            'order_id'      => time(),
            'customer_id' => Auth::guard('web')->user()->id,
            'payment_info' => json_encode($paymentInfo),
            'total' => $dataRequest['total'],
        ];

       $this->paymentService->insert($data);

        if ($dataRequest['payment_type'] == 'stripe') {
            $payUrl = $this->stripeService->createPayment($data['order_id'], $dataRequest['total']);
        }

        return redirect()->to($payUrl);
    }
}
