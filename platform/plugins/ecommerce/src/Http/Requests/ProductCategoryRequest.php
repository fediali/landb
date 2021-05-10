<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ProductCategoryRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'            => 'required',
            'order'           => 'required|integer|min:0',
            'per_piece_qty'   => 'min:0',
            //'product_unit_id' => 'exists:vendorproductunits,id',
            'sku_initial'     => 'required|min:1|max:3',
        ];
    }
}
