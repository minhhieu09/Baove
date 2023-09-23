<?php

namespace App\Http\Controllers;

use App\Services\ProductColorService;
use App\Services\ProductComponentService;
use App\Services\ProductService;
use App\Services\ProductSpecialService;
use App\Services\ProductTypeService;
use Illuminate\Http\Request;

class ProductComponentController extends Controller
{
    protected $productService;
    protected $productTypeService;
    protected $productColorService;
    protected $productSpecialService;
    protected $productComponentService;

    public function __construct(
        ProductService $productService,
        ProductTypeService $productTypeService,
        ProductColorService $productColorService,
        ProductSpecialService $productSpecialService,
        ProductComponentService $productComponentService
    )
    {
        $this->productService = $productService;
        $this->productTypeService = $productTypeService;
        $this->productColorService = $productColorService;
        $this->productSpecialService = $productSpecialService;
        $this->productComponentService = $productComponentService;
    }

    public function create($id)
    {
        $assign['color'] = $this->productColorService->getAllByStatus();
        $assign['type'] = $this->productTypeService->getAllByStatus();
        $assign['special'] = $this->productSpecialService->getAllByStatus();
        $assign['memory'] = PRODUCT_MEMORY;
        $assign['product'] = $this->productService->findId($id);
        $assign['id'] = $id;

        return view('admin.product_component.create', $assign);
    }

    public function store(Request $request, $id)
    {
        $dataRequest = $request->all();
        $dataRequest['product_id'] = $id;

        if ($this->productComponentService->isExist($dataRequest)) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Thêm mới thất bại']);
        }

        $file = $request->file('image');
        $image = $file ? $file->getClientOriginalName() : null;
        $data = [
            'product_id' => $id,
            'memory' => $dataRequest['memory'],
            'color_id' => $dataRequest['color'],
            'amount' => $dataRequest['amount'],
            'price' => $dataRequest['price'],
            'image' => $image,
        ];
        $this->productComponentService->insert($data);
        return redirect()->back()->with(['status' => 'success', 'message' => 'Thêm mới thành công']);
    }
}
