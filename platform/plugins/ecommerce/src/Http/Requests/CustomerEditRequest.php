<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class CustomerEditRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name'  => 'required|max:120|min:2',
            'email' => 'required|max:60|min:6|email|unique:ec_customers,email,' . $this->route('customer'),
            'status' => 'required',
            'first_name'     => 'required|max:255',
            'last_name'     => 'required|max:255',
            'business_phone'     => 'required|max:12',
            'company'     => 'required|max:255',
            "customer_type"    => "required|array|min:1",
            "sales_tax_id"    => "required|max:15",
        ];

        if ($this->input('is_change_password') == 1) {
            $rules['password'] = 'required|min:6';
            $rules['password_confirmation'] = 'required|same:password';
        }

        return $rules;
    }
}
