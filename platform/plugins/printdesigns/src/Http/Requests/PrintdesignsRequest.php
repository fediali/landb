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
        return [
            'name'   => 'required|max:255',
            'designer_id'   => 'required|exists:users,id',
            'sku'   => 'required|max:255',
            'file'   => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
