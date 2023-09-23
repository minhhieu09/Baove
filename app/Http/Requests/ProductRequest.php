<?php

namespace App\Http\Requests;

use App\Services\ProductComponentService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProductRequest extends FormRequest
{
    protected $productComponentService;

    public function __construct(ProductComponentService $productComponentService)
    {
        $this->productComponentService = $productComponentService;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $productId = '';
        if ($request->has('id')) {
            $component = $this->productComponentService->findId($request->input('id'));
            $productId = $component ? $component->product->id : '';
        }

        return [
            'name'  => 'required|unique:product_models,name,' . $productId
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Sản phẩm này đã tồn tại'
        ];
    }
}
