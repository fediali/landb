<?php

namespace Botble\Textmessages\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class TextmessagesRequest extends Request
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
            'text'   => 'required',
            //'schedule_date'   => 'required',
            //'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
