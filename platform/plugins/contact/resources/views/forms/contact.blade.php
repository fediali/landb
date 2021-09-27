{{--{!! Form::open(['route' => 'public.send.contact', 'method' => 'POST', 'class' => 'contact-form']) !!}
    <div class="contact-form-row">
        <div class="contact-column-6">
            <div class="contact-form-group">
                <label for="contact_name" class="contact-label required">{{ __('Name') }}</label>
                <input type="text" class="contact-form-input" name="name" value="{{ old('name') }}" id="contact_name"
                       placeholder="{{ __('Name') }}">
            </div>
        </div>
        <div class="contact-column-6">
            <div class="contact-form-group">
                <label for="contact_email" class="contact-label required">{{ __('Email') }}</label>
                <input type="email" class="contact-form-input" name="email" value="{{ old('email') }}" id="contact_email"
                       placeholder="{{ __('Email') }}">
            </div>
        </div>
    </div>
    <div class="contact-form-row">
        <div class="contact-column-6">
            <div class="contact-form-group">
                <label for="contact_address" class="contact-label">{{ __('Address') }}</label>
                <input type="text" class="contact-form-input" name="address" value="{{ old('address') }}" id="contact_address"
                       placeholder="{{ __('Address') }}">
            </div>
        </div>
        <div class="contact-column-6">
            <div class="contact-form-group">
                <label for="contact_phone" class="contact-label">{{ __('Phone') }}</label>
                <input type="text" class="contact-form-input" name="phone" value="{{ old('phone') }}" id="contact_phone"
                       placeholder="{{ __('Phone') }}">
            </div>
        </div>
    </div>
    <div class="contact-form-row">
        <div class="contact-column-12">
            <div class="contact-form-group">
                <label for="contact_subject" class="contact-label">{{ __('Subject') }}</label>
                <input type="text" class="contact-form-input" name="subject" value="{{ old('subject') }}" id="contact_subject"
                       placeholder="{{ __('Subject') }}">
            </div>
        </div>
    </div>
    <div class="contact-form-row">
        <div class="contact-column-12">
            <div class="contact-form-group">
                <label for="contact_content" class="contact-label required">{{ __('Message') }}</label>
                <textarea name="content" id="contact_content" class="contact-form-input" rows="5" placeholder="{{ __('Message') }}">{{ old('content') }}</textarea>
            </div>
        </div>
    </div>

    @if (setting('enable_captcha') && is_plugin_active('captcha'))
        <div class="contact-form-row">
            <div class="contact-column-12">
                <div class="contact-form-group">
                    {!! Captcha::display() !!}
                </div>
            </div>
        </div>
    @endif

    <div class="contact-form-group"><p>{!! clean(__('The field with (<span style="color:#FF0000;">*</span>) is required.')) !!}</p></div>

    <div class="contact-form-group">
        <button type="submit" class="contact-button">{{ __('Send') }}</button>
    </div>

    <div class="contact-form-group">
        <div class="contact-message contact-success-message" style="display: none"></div>
        <div class="contact-message contact-error-message" style="display: none"></div>
    </div>
{!! Form::close() !!}--}}

{!! Form::open(['route' => 'public.send.contact', 'method' => 'POST', 'class' => 'contact-form']) !!}
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
            <div class="pl-3 pr-3">
            <h2 class="mt-5 mb-4 text-center signin-head">CONTACT</h2>
            <div class="row">
                <div class="col-lg-12">
                    <p class="textbox-label">{{ __('Name') }}</p>
                    <input type="text" class="input-textbox" name="name" value="{{ old('name') }}" id="contact_name"
                           placeholder="{{ __('Name') }}">
                    {!! Form::error('name', $errors) !!}
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Email</p>
                    <input type="email" class="input-textbox" name="email" value="{{ old('email') }}" id="contact_email"
                           placeholder="{{ __('Email') }}">
                    {!! Form::error('email', $errors) !!}
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Address</p>
                    <input type="text" class="input-textbox" name="address" value="{{ old('address') }}" id="contact_address"
                           placeholder="{{ __('Address') }}">
                    {!! Form::error('address', $errors) !!}
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Phone</p>
                    <input type="text" class="input-textbox" name="phone" value="{{ old('phone') }}" id="contact_phone"
                           placeholder="{{ __('Phone') }}">
                    {!! Form::error('phone', $errors) !!}
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Subject</p>
                    <input type="text" class="input-textbox" name="subject" value="{{ old('subject') }}" id="contact_subject"
                           placeholder="{{ __('Subject') }}">
                    {!! Form::error('subject', $errors) !!}
                </div>
                <div class="col-lg-12">
                    <p class="textbox-label">Message</p>
                    <textarea name="content" id="contact_content" class="input-textbox" rows="5" placeholder="{{ __('Message') }}">{{ old('content') }}</textarea>
                    {!! Form::error('content', $errors) !!}
                </div>
                <div class="col-lg-12 mt-5">
                    <button type="submit" class=" btn cart-btn w-100">Submit</button>
                </div>
            </div>
            </div>
        </div>
        <div class="col-lg-3"></div>
    </div>
</section>

{!! Form::close() !!}
