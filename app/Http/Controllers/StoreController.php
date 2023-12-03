<?php

namespace App\Http\Controllers;

use App\Models\PaymentModel;
use App\Models\ProductComponentModel;
use App\Models\ProductRatingModel;
use App\Models\SpecialModel;
use App\Models\SpecialProductModel;
use App\Services\CustomerService;
use App\Services\PaymentService;
use App\Services\ProductColorService;
use App\Services\ProductComponentService;
use App\Services\ProductSpecialService;
use App\Services\ProductTypeService;
use App\Services\StoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\ProductService;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;

class StoreController extends Controller
{
    protected $productService;
    protected $storeService;
    protected $customerService;
    protected $productSpecialService;
    protected $productComponentService;
    protected $productColorService;
    protected $paymentService;
    protected $productTypeService;

    public function __construct(
        ProductService          $productService,
        StoreService            $storeService,
        CustomerService         $customerService,
        ProductSpecialService   $productSpecialService,
        ProductComponentService $productComponentService,
        ProductColorService     $productColorService,
        PaymentService          $paymentService,
        ProductTypeService      $productTypeService
    )
    {
        $this->productService           = $productService;
        $this->storeService             = $storeService;
        $this->customerService          = $customerService;
        $this->productSpecialService    = $productSpecialService;
        $this->productComponentService  = $productComponentService;
        $this->productColorService      = $productColorService;
        $this->paymentService           = $paymentService;
        $this->productTypeService       = $productTypeService;
    }

    public function index()
    {
//        dd($this->productService->insert(1));
        $assign['specials'] = $this->productSpecialService->allAvailable()->load('product.component.color');
        return view('store.index', $assign);
    }

    public function cart()
    {
        $session = Session::has('cart') ? Session::get('cart') : null;

        return view('store.cart', ['data' => $session]);
    }

    public function addCart(Request $request)
    {
//        dd(Session::get('cart'));
        $dataRequest = $request->all();
        $product = $this->productService->findId($dataRequest['id']);
        $component = $this->productComponentService->findId($dataRequest['component']);
        if($component['amount'] == 0 ) return response()->json();
        $data = [
            'id'        => $component->id,
            'name'      => $product->name,
            'amount'    => $dataRequest['amount'],
            'color'     => $component->color->name,
            'price'     => $component->price,
            'memory'    => $component->memory,
            'img'       => $component->image,
            'time'      => strtotime(now())
        ];

        $this->storeService->addCart($data, $component->id);

        return response()->json(Session::get('cart'));
    }

    public function getCartSession()
    {
        if (Session::has('cart')) {
            return response()->json(Session::get('cart'));
        }

        return response()->json();
    }

    public function removeCart()
    {
        Session::forget('cart');

        return redirect()->to(route(STORE_CART));
    }

    public function deleteCart($id)
    {
        $session = Session::get('cart');
        unset($session[$id]);
        Session::put('cart', $session);

        return redirect()->to(route(STORE_CART));
    }

    public function detail($id)
    {
        $assign['product'] = $this->productService->findId($id);
        $assign['rating'] = ProductRatingModel::where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        $assign['component'] = $assign['product']->component->first();
        $name = $assign['product']->name;
        $assign['sameProduct'] = $this->productService->getSameProduct($id, $assign['product']->type);

        $data = $this->productComponentService->getColor($assign['product']->id);
        $colorIds = array_unique($data->pluck('color_id')->toArray());
        $assign['color'] = $this->productColorService->getColorByIds($colorIds);

        Breadcrumbs::register('continent', function ($breadcrumbs) use ($name){
            $breadcrumbs->push('Trang Chủ', route(STORE));
            $breadcrumbs->push($name, route(STORE_PRODUCT_DETAIL));
        });

        return view('store.detail', $assign);
    }

    public function getMemory(Request $request)
    {
        $dataRequest = $request->all();
        $memory = $this->productComponentService->getMemory($dataRequest['id'], $dataRequest['color']);

        return response()->json(['data' => $memory]);
    }

    public function listCategory(Request $request)
    {
        $dataRequest = $request->all();
        $assign['specials'] = $this->productSpecialService->allAvailable()->load('product.component.color');
        $assign['productType'] = $this->productTypeService->allAvailable();
        if (!empty($dataRequest['product-special'])) {
            $productIds = SpecialProductModel::where('special_id', $dataRequest['product-special'])->pluck('product_id')->toArray();
        } else {
            $productIds = SpecialProductModel::groupBy('product_id')->pluck('product_id')->toArray();
        }

        $type = !empty($dataRequest['product-type']) ? $dataRequest['product-type'] : null;
        $assign['products'] = $this->productService->filterProduct($productIds, $type);
        if (!empty($request->input('name'))) {
            $name = $request->input('name');
            $assign['products'] = $assign['products']->filter(function ($item) use ($name) {
                return str_contains($item['name'], $name);
            });
        }

        return view('store.list_category', $assign);
    }

    public function getProductType()
    {
        $types = $this->productTypeService->allAvailable();
        return response()->json(['types' => $types]);
    }

    public function productRating(Request $request)
    {
        $dataRequest = $request->all();
        $customerId = Auth::guard('web')->user()->id ?? 'guess';
        $productId = $dataRequest['product_id'];
        $comment = $dataRequest['comment'];
        $rating = $dataRequest['rating'];

        ProductRatingModel::create([
            'customer_id' => $customerId,
            'product_id' => $productId,
            'rate' => $rating,
            'comment' => $comment
        ]);

        return redirect()->back()->with(['status' => 'success', 'message' => 'Bình luận thành công']);
    }

    public function paymentComplete(Request $request){
        $completePayment = Session::get('cart');
        $customer = Auth::guard('web')->user();
        $total = 0;
        $paymentInfo = [];
        foreach($completePayment as $item) {
            $paymentInfo[] = array(
                'id' => $item['id'],
                'name' => $item['name'],
                'amount' => $item['amount'],
                'color' => $item['color'],
                'price' => $item['price'],
                'memory'=> $item['memory'],
            );
            $total += $item['price'] * $item['amount'];
            $productComponent = ProductComponentModel::find($item['id']);
            ProductComponentModel::find($item['id'])->update([
                'amount' => $productComponent->amount > $item['amount'] ? $productComponent->amount - $item['amount'] : 0,
            ]);
            if ($productComponent['amount'] == 0 ) return redirect()->back()->with(['status' => 'fail', 'message' => 'Sản phẩm đã hết']);
        }
        $data = [
            'order_id' => time(),
            'customer_id' => $customer['id'],
            'payment_type' => 'cash',
            'total' => $total,
            'status' => 1,
            'payment_info' => json_encode($paymentInfo),
            'created_at' => Carbon::now(),
            'update_at' => Carbon::now(),
        ];
        PaymentModel::create($data);
        Session::forget('cart');
        return redirect()->back()->with(['status' => 'success', 'message' => 'Thanh toán thành công']);
    }
}
