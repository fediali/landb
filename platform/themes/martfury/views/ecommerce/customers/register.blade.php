<div class="ps-my-account">
    <div class="container">
        <form class="ps-form--account ps-tab-root" method="POST" action="{{ route('customer.register.post') }}">
            @csrf
            <div class="ps-form__content">
                <h4>{{ __('Register An Account') }}</h4>
                <div class="form-group">
                    <input class="form-control" name="name" id="txt-name" type="text" value="{{ old('name') }}" placeholder="{{ __('Your Name') }}">
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <input class="form-control" name="email" id="txt-email" type="email" value="{{ old('email') }}" placeholder="{{ __('Your Email') }}">
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password" id="txt-password" placeholder="{{ __('Password') }}">
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" name="password_confirmation" id="txt-password-confirmation" placeholder="{{ __('Password') }}">
                    @if ($errors->has('password_confirmation'))
                        <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <div class="ps-checkbox">
                        <input class="form-check-input" type="checkbox" name="agree_terms_policy" id="terms-policy" value="1">
                        <label class="form-check-label" for="terms-policy"><span>{{ __('I agree to terms & Policy.') }}</span></label>
                    </div>
                </div>
                @if (setting('enable_captcha') && is_plugin_active('captcha'))
                    {!! Captcha::display() !!}
                @endif
                <div class="form-group submit">
                    <button class="ps-btn ps-btn--fullwidth" type="submit">{{ __('Sign up') }}</button>
                </div>

                <div class="form-group">
                    <p class="text-center">{{ __('Already have an account?') }} <a href="{{ route('customer.login') }}">{{ __('Log in') }}</a></p>
                </div>
            </div>
            <div class="ps-form__footer">
                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
            </div>
        </form>
    </div>
</div>

