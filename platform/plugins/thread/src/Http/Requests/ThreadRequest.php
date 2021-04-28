<?php

namespace Botble\Thread\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ThreadRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = Request::segment(4);

        $rules = [
            'name'   => 'required|max:255',
            'designer_id'   => 'required|exists:users,id',
            //'vendor_id'   => 'required|exists:users,id',
            'season_id'   => 'required|exists:seasons,id',
            //'order_no'   => 'required',
            //'order_status'   => 'required',
            'regular_category_id' => 'required|exists:ec_product_categories,id',
            'regular_product_unit_id' => 'nullable|exists:vendorproductunits,id',
            'regular_per_piece_qty' => 'nullable|min:0',
            'plus_category_id'    => 'nullable|exists:ec_product_categories,id',
            'plus_product_unit_id' => 'nullable|exists:vendorproductunits,id',
            'plus_per_piece_qty' => 'min:0',
            //'design_id'   => 'required|exists:printdesigns,id',
            //'pp_request'   => 'required',
            //'pp_sample'   => 'required',
            'shipping_method'   => 'required',
            'vendor_product_id' => 'nullable|exists:vendorproducts,id',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];

        if ($id > 0) {
            $rules['reg_sku'] = "nullable|unique:categories_threads,sku,{$id}";
            $rules['plus_sku'] = "nullable|unique:categories_threads,sku,{$id}";
        } else {
            $rules['reg_sku'] = 'nullable|unique:categories_threads,sku';
            $rules['plus_sku'] = 'nullable|unique:categories_threads,sku';
        }

        return $rules;
    }
}
