<?php

namespace App\Http\Controllers;

use App\Services\ProductTypeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductTypeController extends Controller
{
    protected $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    public function index()
    {
        $data = $this->productTypeService->all();

        return view('admin.product_type.index', ['data' => $data]);
    }

    public function edit($id)
    {
        $data = $this->productTypeService->findId($id);

        return view('admin.product_type.edit', ['data' => $data, 'id' => $id]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'unique:product_type_models,name,' . $request->input('id')
        ]);

        if (!$validator->passes() && !$request->has('id')) {
            return response()->json(['status' => 0]);
        }

        if (!$validator->passes() && $request->has('id')) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Tên này đã tồn tại']);
        }

        $dataRequest = $request->all();

        $data = [
            'name'       => $dataRequest['name'],
            'status'     => isset($dataRequest['status']) ? $dataRequest['status'] : 1
        ];

        if (isset($dataRequest['id'])) {
            $this->productTypeService->update($data, $dataRequest['id']);
            return redirect()->back()->with(['status' => 'success', 'message' => 'Thay đổi thành công']);
        }

        $row = $this->productTypeService->insert($data);

        if ($row) {
            return response()->json(['status' => 1 ,'data' => $row]);
        }

        return response()->json(['status' => 0]);
    }

    public function delete($id)
    {
        $type = $this->productTypeService->findId($id);

        if ($type->product) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Xoá thất bại']);
        }

        $this->productTypeService->delete($id);
        return redirect()->back()->with(['status' => 'success', 'message' => 'Xoá thành công']);
    }
}
