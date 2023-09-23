<?php

namespace App\Http\Controllers;

use App\Events\RegisterEvent;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Services\AddressService;
use App\Services\CustomerService;
use App\Services\PaymentService;
use App\Services\ProductComponentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public $customerService;
    public $addressService;
    public $paymentService;
    public $productComponentService;

    public function __construct(
        CustomerService $customerService,
        AddressService $addressService,
        PaymentService $paymentService,
        ProductComponentService $productComponentService
    )
    {
        $this->customerService = $customerService;
        $this->addressService = $addressService;
        $this->paymentService = $paymentService;
        $this->productComponentService = $productComponentService;
    }

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);

        $customer = User::where('email', $data['email'])->first();

        //Check exist customer
        if (empty($customer)) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Đăng nhập thất bại']);
        }

        //Check active account
        if ($customer->status == 0) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Hãy kích hoạt tài khoản']);
        }

        if (Auth::guard('web')->attempt($data)) {
            return redirect()->to(route(STORE));
        }

        return redirect()->back()->with(['status' => 'fail', 'message' => 'Đăng nhập thất bại']);
    }

    public function active($email, $token)
    {
        $customer = User::where('email', $email)->first();
        if ($customer->remember_token == $token) {
            $customer->status = 1;
            $customer->save();
            return redirect()->to(route(STORE_LOGIN));
        }

        abort(404);
    }

    public function register(Request $request)
    {
        $dataRequest = $request->all();
        $validator = Validator::make($request->all(), [
            'email' => 'unique:users,email,' . $request->input('id') ?? null,
        ]);

        if (!$validator->passes() || $dataRequest['password'] != $dataRequest['repassword']) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Đăng kí thất bại']);
        }

        $data = [
            'name'              => $request->input('name'),
            'email'             => $request->input('email'),
            'password'          => Hash::make($request->input('password')),
            'remember_token'    => time()
        ];

        if (User::create($data)) {
            RegisterEvent::dispatch($data['email'], $data['remember_token']);
            return redirect()->back()->with(['status' => 'success', 'message' => 'Đăng kí thành công']);
        }

        return redirect()->back()->with(['status' => 'fail', 'message' => 'Đăng kí thất bại']);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        Session::forget('cart');
        return redirect()->to(route(STORE));
    }

    public function forgotPassword(Request $request)
    {
        $email = $request->input('email');
        $token = time();
        $customer = User::where('email', $email)->first();

        if (!empty($customer)) {
            $this->customerService->update(['forgot_password' => $token], $customer->id);
        }

        Mail::to($email)->send(new ResetPasswordMail($email, $token));
        return redirect()->back()->with(['status' => 'success', 'message' => 'Đã gửi mã xác thực đến email']);
    }

    public function formResetPassword($email, $token)
    {
        return view('store.auth.reset_password', ['email' => $email, 'token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $dataRequest = $request->all();
        if ($dataRequest['password'] != $dataRequest['repassword']) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Cập nhật thất bại']);
        }

        $customer = User::where('email', $dataRequest['email'])->firstOrFail();
        $data = ['password' => Hash::make($dataRequest['password'])];

        if ($customer->forgot_password != $dataRequest['token']) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Cập nhật thất bại']);
        }

        if ($this->customerService->update($data, $customer->id)) {
            return redirect()->back()->with(['status' => 'success', 'message' => 'Cập nhật thành công']);
        }

        return redirect()->back()->with(['status' => 'fail', 'message' => 'Cập nhật thất bại']);
    }

    public function info()
    {
        $assign['cities'] = $this->addressService->getCity();
        $assign['customer'] = Auth::guard('web')->user();
        if (!empty($assign['customer']->city_id)) {
            $assign['districts'] = $this->addressService->find('city_id', '=', $assign['customer']->city_id);
        }

        return view('customer.info', $assign);
    }

    public function getDistrict(Request $request)
    {
        $cityId = $request->input('city_id');
        $districts = $this->addressService->find('city_id', '=', $cityId);

        return response()->json(['data' => $districts]);
    }

    public function saveInfo(Request $request)
    {
        try {
            $dataRequest = $request->all();
            $data = [
                'first_name'    => $dataRequest['first_name'],
                'name'          => $dataRequest['last_name'],
                'phone'         => $dataRequest['phone'],
                'city_id'       => $dataRequest['city'],
                'district_id'   => $dataRequest['district'],
                'address'       => $dataRequest['address']
            ];

            $this->customerService->update($data, Auth::guard('web')->user()->id);
            return redirect()->back()->with(['status' => 'success', 'message' => CHANGE_SUCCESS]);
        } catch (\Exception $e) {
            return redirect()->back()->with(['status' => 'fail', 'message' => CHANGE_FAIL]);
        }
    }

    public function bill()
    {
        $user = Auth::guard('web')->user()->id;
        $assign['payments'] = $this->paymentService->find('customer_id', '=', $user);

        return view('store.payment', $assign);
    }

    public function getBillInfo(Request $request)
    {
        $paymentInfo = $this->paymentService->find('order_id', '=',$request->input('id'))->first();
        $products = json_decode($paymentInfo->payment_info);
        $info = [];
        foreach ($products as $product) {
//            $component = $this->productComponentService->findId($product->component);
            $info[] = [
                'order_id'      => $paymentInfo->order_id,
                'product_name'  => $product->product_name,
                'memory'        => $product->memory,
                'color'         => $product->color,
                'amount'        => $product->amount,
                'price'         => $product->price,
                'product_total' => $product->amount * $product->price,
                'total'         => $paymentInfo->total
            ];
        }

        return response()->json(['data' => $info]);
    }
}
