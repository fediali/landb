@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200" id="main-order">
        <create-order :currency="'{{ get_application_currency()->symbol }}'" :zip_code_enabled="{{ (int)EcommerceHelper::isZipCodeEnabled() }}"></create-order>
    </div>
@stop

