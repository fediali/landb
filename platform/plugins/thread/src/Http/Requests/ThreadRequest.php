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
        return [
            'name'   => 'required|max:255',
            'designer_id'   => 'required|exists:users,id',
            //'vendor_id'   => 'required|exists:users,id',
            'season_id'   => 'required|exists:seasons,id',
            //'order_no'   => 'required',
            'order_status'   => 'required',
            'category_id'   => 'required|exists:categories,id',
            //'design_id'   => 'required|exists:printdesigns,id',
            //'pp_request'   => 'required',
            //'pp_sample'   => 'required',
            'shipping_method'   => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
