<?php

namespace Botble\Threadvariationsamples\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ThreadvariationsamplesRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'thread_id'  => 'required',
            'thread_variation_id'  => 'required',
            'assign_date'  => 'required',
            'photographer_id'  => 'required',
        ];
    }
}
