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
        $id = Request::segment(5);
        return [
            'username'   => "required|max:30|min:4|unique:users,username,{$id}",
            'first_name' => 'required|max:60|min:2',
            'last_name'  => 'required|max:60|min:2',
            'email'      => "required|max:60|min:6|email|unique:users,email,{$id}",
            'other_emails.*' => 'email',
            'name_initials' => "required|min:2|max:3|unique:users,name_initials,{$id}",
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
