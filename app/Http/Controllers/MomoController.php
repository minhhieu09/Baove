<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use App\Traits\MomoTrait;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MomoController extends Controller
{
    use MomoTrait;

    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('atm.atm_momo');
    }

    public function atm(Request $request)
    {
        $payment = $request->session()->get('payment');
        $dataRequest = $request->all();

        //Save payment to db
        $paymentInfo = [];
        foreach ($payment[0]['component'] as $key => $value) {
            $paymentInfo[] = [
                'component' => $value,
                'amount'    => $payment[0]['amount'][$key]
            ];
        }

        $data = [
            'order_id'      => time(),
            'customer_id'   => Auth::guard('web')->user()->id,
            'payment_type'  => 'momo',
            'total'         => $payment[0]['total'],
            'payment_info'  => json_encode($paymentInfo)
        ];
        $this->paymentService->insert($data);

        $partnerCode = MOMO_PARTNER_CODE;
        $accessKey = MOMO_ACCESS_KEY;
        $secretKey = MOMO_SECRET_KEY;
        $orderInfo = $dataRequest['orderInfo'];
        $amount = $payment[0]['total'];
        $orderId = time()."";
        $returnUrl = route(RESULT_PAYMENT_MOMO);
        $notifyUrl = MOMO_NOTIFY_URL;
        $bankCode = MOMO_BANK_CODE;
        $requestType = "payWithMoMoATM";
        $requestId = time()."";
        $extraData = "";
        $endpoint = "https://test-payment.momo.vn/gw_payment/transactionProcessor";

        $rawHash = "partnerCode=".$partnerCode."&accessKey=".$accessKey."&requestId=".$requestId."&bankCode=".$bankCode."&amount=".$amount."&orderId=".$orderId."&orderInfo=".$orderInfo."&returnUrl=".$returnUrl."&notifyUrl=".$notifyUrl."&extraData=".$extraData."&requestType=".$requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data =  array('partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $returnUrl,
            'bankCode' => $bankCode,
            'notifyUrl' => $notifyUrl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature);

        //send api to momo
        $result = $this->execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result,true);

        return redirect($jsonResult['payUrl']);
    }

    public function result(Request $request)
    {
        try {
            $data = $request->all();
            if ($data['errorCode'] ?? NULL == '42') {
                return redirect()->to(route(STORE_CART));
            }

            if (!empty($data['orderId'])) {
                $this->paymentService->updateWithCondition(['status' => 1], 'order_id', $data['orderId']);
            }
            Session::pull('cart');

            return redirect()->to(route(STORE_CART))->with(['status' => 'success', 'message' => 'Thanh toán thành công']);
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
