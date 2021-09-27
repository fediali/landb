<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page"><b>Forget Password</b></li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <h2 class="mt-5 mb-4 text-center signin-head">FORGET PASSWORD</h2>
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </div>
            <form class="" id="login-form" method="POST" action="{{ route('public.post-forget-password') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <p class="textbox-label">Email</p>
                        <input class="input-textbox" type="text" name="email" value="{{ old('email') }}" />
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-12 mt-5">
                        <input type="submit" form="login-form" class=" btn border-btn w-100" value="Send Password Reset Link">{{--<a href="#" class=" btn cart-btn w-100">Sign In</a>--}}
                    </div>
                    <div class="col-lg-12 mt-3 mb-5">
                        <a href="{{ route('customer.login') }}" class=" btn border-btn w-100">Login</a>
                    </div>
                </div>
            </form>
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

        </div>
        <div class="col-lg-4"></div>
    </div>
</section>