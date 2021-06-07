<?php

namespace Botble\Producttimeline\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ProducttimelineRequest extends Request
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
            'date'   => 'required',
            'schedule_date'   => 'required',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
