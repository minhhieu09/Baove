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

class AdminController extends Controller
{
    protected $productService;
    protected $productTypeService;
    protected $productColorService;
    protected $productSpecialService;
    protected $productComponentService;
    protected $paymentService;
    protected $customerService;

    public function __construct(
        ProductService $productService,
        ProductTypeService $productTypeService,
        ProductColorService $productColorService,
        ProductSpecialService $productSpecialService,
        ProductComponentService $productComponentService,
        PaymentService $paymentService,
        CustomerService $customerService
    )
    {
        $this->productService           = $productService;
        $this->productTypeService       = $productTypeService;
        $this->productColorService      = $productColorService;
        $this->productSpecialService    = $productSpecialService;
        $this->productComponentService  = $productComponentService;
        $this->paymentService           = $paymentService;
        $this->customerService          = $customerService;
    }

    public function dashboard()
    {
        $assign['customers'] = $this->customerService->allAvailable();

        return view('admin.product.dashboard', $assign);
    }

    public function index()
    {
//        $products = $this->productService->all()->load('component.color');
        $products = ProductModel::with('component.color')->paginate(PAGINATE_DEFAULT);

        return view('admin.product.index', ['products' => $products]);
    }

    public function create()
    {
        $assign['color'] = $this->productColorService->getAllByStatus();
        $assign['type'] = $this->productTypeService->getAllByStatus();
        $assign['special'] = $this->productSpecialService->getAllByStatus();
        $assign['memory'] = PRODUCT_MEMORY;

        return view('admin.product.create', $assign);
    }

    public function edit($id)
    {
        $assign['color'] = $this->productColorService->getAllByStatus();
        $assign['type'] = $this->productTypeService->getAllByStatus();
        $assign['special'] = $this->productSpecialService->getAllByStatus();
        $assign['memory'] = PRODUCT_MEMORY;
        $assign['productComponent'] = $this->productComponentService->findId($id);
        $assign['product'] = $assign['productComponent']->product;
        $assign['id'] = $id;

        return view('admin.product.edit', $assign);
    }

    public function store(ProductRequest $request)
    {
        $dataRequest = $request->all();
        //Combination to 1 array
        $arr = array();
        for ($i = 0; $i < count($dataRequest['memory']); $i++) {
            $file = $request->file('image')[$i] ?? null;
            $h = [
                'id' => $i,
                'memory' => $dataRequest['memory'][$i],
                'color' => $dataRequest['color'][$i],
                'amount' => $dataRequest['amount'][$i],
                'price' => $dataRequest['price'][$i],
                'image' => $file ? $file->getClientOriginalName() : null
            ];
            array_push($arr, $h);
        }

        //Unique collection
        $collect = collect($arr);
        for ($i = 0; $i < count($dataRequest['memory']); $i++) {
            $query = $collect->where('memory', $dataRequest['memory'][$i])
                             ->where('color', $dataRequest['color'][$i]);

            if ($query->count() >= 2) {
                $id = $query->first()['id'];
                $collect->forget($id);
            }
        }

        try {
            $dataProduct = [
                'name' => $dataRequest['name'],
                'type' => $dataRequest['type'],
                'description' => $dataRequest['description'],
                'status' => isset($dataRequest['status']) ? 1 : 0
            ];

            if (!isset($dataRequest['id'])) {
                //Insert product
                $product = $this->productService->insert($dataProduct);
            } else {
                //Update product
                $productId = $this->productComponentService->findId($dataRequest['id'])->product->id;
                $product = $this->productService->update($dataProduct, $productId);
            }

            if (!empty($dataRequest['special'])) {
                $product->special()->sync($dataRequest['special']);
            }

            //Insert Product Component
            $dataProductComponent = array();

            foreach ($collect as $data) {
                $row = [
                    'product_id' => $product->id,
                    'memory' => $data['memory'],
                    'color_id' => $data['color'],
                    'amount' => $data['amount'],
                    'price' => $data['price'],
                    'image' => $data['image'],
                    'created_at' => Carbon::now()
                ];

                array_push($dataProductComponent, $row);
            }

            if (isset($dataRequest['id'])) {
                $dataProductComponent = [
                    'memory'    => $dataRequest['memory'][0],
                    'color_id'  => $dataRequest['color'][0],
                    'amount'    => $dataRequest['amount'][0],
                    'price'     => $dataRequest['price'][0],
                    'image'     => $dataRequest['image'][0],
                ];
                if (empty($dataRequest['image'][0])) {
                    unset($dataProductComponent['image']);
                }

                //Check exist product component before update
                $check = [
                    'product_id'    => $productId,
                    'memory'        => $dataRequest['memory'][0],
                    'color'         => $dataRequest['color'][0],
                ];

                if ($this->productComponentService->isExist($check, $dataRequest['id'])) {
                    return redirect()->back()->with(['status' => 'fail', 'message' => 'Thay đổi thất bại']);
                }

                $this->productComponentService->update($dataProductComponent, $dataRequest['id']);
                return redirect()->back()->with(['status' => 'success', 'message' => 'Thay đổi thành công']);
            } else {
                $this->productComponentService->insertMulti($dataProductComponent);
            }

            return redirect()->back()->with(['status' => 'success', 'message' => 'Thêm mới thành công']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Thêm mới thất bại']);
        }
    }

    public function delete($id)
    {
        $component = $this->productComponentService->findId($id);
        $product = $component->product;

        if ($this->productComponentService->countProductId($product->id) == 1) {
            $this->productService->delete($product->id);
            $product->special()->where('product_id', $product->id)->detach();
        }

        $this->productComponentService->delete($id);
        return redirect()->back()->with(['status' => 'success', 'message' => 'Xoá thành công']);
    }

    public function selectSpecial(Request $request)
    {
        $productComponent = $this->productComponentService->findId($request->input('id'));
        $product = $productComponent->product;
        $productSpecial = $product->special->pluck('id')->toArray();

        return response()->json($productSpecial);
    }

    public function login(Request $request)
    {
        $data = $request->only(['email', 'password']);

        if (Auth::guard('admin')->attempt($data)) {
            return redirect()->to(route(ADMIN_PRODUCT_INDEX));
        }

        return view('admin.auth.login');
    }

    public function getProductAjax()
    {
        $types = $this->productTypeService->allAvailable();
        $product = [];
        foreach ($types as $type) {
            $product[] = ProductModel::where('type', $type->id)->count();
        }

        $types = $types->pluck('name');
        return response()->json(['type' => $types, 'product' => $product]);
    }
}
