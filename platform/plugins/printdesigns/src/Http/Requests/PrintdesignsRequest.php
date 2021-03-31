<?php

namespace Botble\Printdesigns\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PrintdesignsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'   => 'required|max:255',
            'designer_id'   => 'required|exists:users,id',
            'file'   => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];

        $id = Request::segment(4);

        if ($id > 0) {
            $rules['sku'] = "required|max:5|unique:printdesigns,sku,{$id}";
        } else {
            $rules['sku'] = 'required|max:5|unique:printdesigns,sku';
        }

        return $rules;
    }
}
