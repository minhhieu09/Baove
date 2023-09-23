<?php

namespace App\Http\Controllers;

use App\Services\ProductColorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductColorController extends Controller
{
    protected $productColorService;

    public function __construct(ProductColorService $productColorService)
    {
        $this->productColorService = $productColorService;
    }

    public function index()
    {
        $data = $this->productColorService->all();

        return view('admin.product_color.index', ['data' => $data]);
    }

    public function edit($id)
    {
        $row = $this->productColorService->findId($id);

        return view('admin.product_color.edit', ['data' => $row, 'id' => $id]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'color' => 'unique:product_color_models,name,' . $request->input('id')
        ]);

        if (!$validator->passes() && !$request->has('id')) {
            return response()->json(['status' => 0]);
        }

        if (!$validator->passes() && $request->has('id')) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Màu này đã tồn tại']);
        }

        $dataRequest = $request->all();

        $data = [
            'name'       => $dataRequest['color'],
            'color_code' => $dataRequest['color-code'],
            'status'     => isset($dataRequest['status']) ? $dataRequest['status'] : 1
        ];

        if (isset($dataRequest['id'])) {
            $this->productColorService->update($data, $dataRequest['id']);
            return redirect()->back()->with(['status' => 'success', 'message' => 'Thay đổi thành công']);
        }

        $row = $this->productColorService->insert($data);

        if ($row) {
            return response()->json(['status' => 1 ,'data' => $row]);
        }

        return response()->json(['status' => 0]);
    }

    public function delete($id)
    {
        $color = $this->productColorService->findId($id);
        if ($color->component) {
            return redirect()->back()->with(['status' => 'fail', 'message' => 'Xoá thất bại']);
        }

        $this->productColorService->delete($id);
        return redirect()->back()->with(['status' => 'success', 'message' => 'Xoá thành công']);
    }
}
