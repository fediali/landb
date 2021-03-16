<?php

namespace Botble\Threadorders\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ThreadordersRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => 'required|max:255',
            //'vendor_id'   => 'required|exists:users,id',
            //'regular_category_id' => 'required|exists:ec_product_categories,id',
            //'plus_category_id'    => 'nullable|exists:ec_product_categories,id',
            'pp_sample'   => 'required',
            'shipping_method'   => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
