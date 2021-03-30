<?php

namespace Botble\Vendorproducts\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class VendorproductsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'   => 'required',
            'quantity'   => 'required',
            'product_unit_id'   => 'required|exists:vendorproductunits,id',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
