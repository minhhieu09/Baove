<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class VNPayController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function atm()
    {
        return view('atm.vnpay');
    }

    public function createPayment(Request $request)
    {
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
        $dataRequest = $request->all();
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = route(RESULT_PAYMENT_VNPAY);
        $vnp_TmnCode = "0MXE8R5O";
        $vnp_HashSecret = "MPWTZMJNAFXHKCJUQBHQUSUESFOISYFD";

        $vnp_TxnRef = $dataRequest['order_id']; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = $dataRequest['order_desc'];
        $vnp_OrderType = $dataRequest['order_type'];
        $vnp_Amount = $dataRequest['amount'] * 100;
        $vnp_Locale = $dataRequest['language'];
        $vnp_BankCode = $dataRequest['bank_code'];
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
//Add Params of 2.0.1 Version
        $vnp_ExpireDate = $dataRequest['txtexpire'];
//Billing
        $vnp_Bill_Mobile = $dataRequest['txt_billing_mobile'];
        $vnp_Bill_Email = $dataRequest['txt_billing_email'];
        $fullName = trim($dataRequest['txt_billing_fullname']);
        if (isset($fullName) && trim($fullName) != '') {
            $name = explode(' ', $fullName);
            $vnp_Bill_FirstName = array_shift($name);
            $vnp_Bill_LastName = array_pop($name);
        }
        $vnp_Bill_Address = $dataRequest['txt_inv_addr1'];
        $vnp_Bill_City = $dataRequest['txt_bill_city'];
        $vnp_Bill_Country = $dataRequest['txt_bill_country'];
        $vnp_Bill_State = $dataRequest['txt_bill_state'];
// Invoice
        $vnp_Inv_Phone = $dataRequest['txt_inv_mobile'];
        $vnp_Inv_Email = $dataRequest['txt_inv_email'];
        $vnp_Inv_Customer = $dataRequest['txt_inv_customer'];
        $vnp_Inv_Address = $dataRequest['txt_inv_addr1'];
        $vnp_Inv_Company = $dataRequest['txt_inv_company'];
        $vnp_Inv_Taxcode = $dataRequest['txt_inv_taxcode'];
        $vnp_Inv_Type = $dataRequest['cbo_inv_type'];
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate,
            "vnp_Bill_Mobile" => $vnp_Bill_Mobile,
            "vnp_Bill_Email" => $vnp_Bill_Email,
            "vnp_Bill_FirstName" => $vnp_Bill_FirstName,
            "vnp_Bill_LastName" => $vnp_Bill_LastName,
            "vnp_Bill_Address" => $vnp_Bill_Address,
            "vnp_Bill_City" => $vnp_Bill_City,
            "vnp_Bill_Country" => $vnp_Bill_Country,
            "vnp_Inv_Phone" => $vnp_Inv_Phone,
            "vnp_Inv_Email" => $vnp_Inv_Email,
            "vnp_Inv_Customer" => $vnp_Inv_Customer,
            "vnp_Inv_Address" => $vnp_Inv_Address,
            "vnp_Inv_Company" => $vnp_Inv_Company,
            "vnp_Inv_Taxcode" => $vnp_Inv_Taxcode,
            "vnp_Inv_Type" => $vnp_Inv_Type
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
        , 'message' => 'success'
        , 'data' => $vnp_Url);
        return redirect()->to($returnData['data']);
    }

    public function result(Request $request)
    {
        $dataRequest = $request->all();

        if ($dataRequest['vnp_ResponseCode'] == '24') {
            return redirect()->to(route(STORE_CART));
        }

        try {
            $this->paymentService->updateWithCondition(['status' => 1], 'order_id', $dataRequest['vnp_TxnRef']);
            Session::pull('cart');
            return redirect()->to(route(STORE_CART))->with(['status' => 'success', 'message' => 'Thanh toán thành công']);
        } catch (\Exception $e) {
            Log::error('VnPay Fail: ' . $e->getMessage());
            abort(404);
        }
    }
}



