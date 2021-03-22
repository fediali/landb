<?php

namespace Botble\ACL\Http\Requests;

use Botble\Support\Http\Requests\Request;

class UpdateProfileRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'   => 'required|max:30|min:4',
            'first_name' => 'required|max:60|min:2',
            'last_name'  => 'required|max:60|min:2',
            'email'      => 'required|max:60|min:6|email',
            'other_emails.*' => 'email'
        ];
    }

    protected function prepareForValidation()
    {
        if (!is_array($this->other_emails)) {
            //here email we are receiving as comma separated so we make it array
            $this->merge(['other_emails' => explode(',', rtrim($this->other_emails, ','))]);
        }
    }

}
