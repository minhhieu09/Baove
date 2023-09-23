<?php

namespace App\Http\Controllers;

use App\Services\ProductSpecialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductSpecialController extends Controller
{
    protected $productSpecialService;

    public function __construct(ProductSpecialService $productSpecialService)
    {
        $this->productSpecialService = $productSpecialService;
    }

    public function index()
    {
        $data = $this->productSpecialService->all();

        return view('admin.product_special.index', ['data' => $data]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'unique:special_models,name,' . $request->input('id')
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
            $this->productSpecialService->update($data, $dataRequest['id']);
            return redirect()->back()->with(['status' => 'success', 'message' => 'Thay đổi thành công']);
        }

        $row = $this->productSpecialService->insert($data);

        if ($row) {
            return response()->json(['status' => 1 ,'data' => $row]);
        }

        return response()->json(['status' => 0]);
    }

    public function edit($id)
    {
        $row = $this->productSpecialService->findId($id);

        return view('admin.product_special.edit', ['data' => $row, 'id' => $id]);
    }

    public function delete($id)
    {
        $special = $this->productSpecialService->findId($id);
        $special->product()->where('special_id', $special->id)->detach();

        $this->productSpecialService->delete($id);
        return redirect()->back()->with(['status' => 'success', 'message' => 'Xoá thành công']);
    }
}
