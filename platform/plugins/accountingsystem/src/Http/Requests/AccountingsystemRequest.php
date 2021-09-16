<?php

namespace Botble\Accountingsystem\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AccountingsystemRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'money'   => 'required',
            'description'   => 'required',
            'amount'   => 'required',
        ];
    }
}
