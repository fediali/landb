<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page"><b>Login</b></li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
        <div class="col-lg-3"></div>
        <div class="col-lg-6">
            <h2 class="mt-5 mb-4 text-center signin-head">SIGN IN</h2>

            <form class="" id="login-form" method="POST" action="{{ route('public.login.post') }}">
                @csrf
            <div class="row">
                <div class="col-lg-12">
                    <p class="textbox-label">Email</p>
                    <input class="input-textbox" type="text" name="email" value="{{ old('email') }}" />
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                    <p class="textbox-label">Password</p>
                    <input class="input-textbox" type="password" name="password" />
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>
                <div class="col-lg-6 col-6 mt-3">
                    <input class="ml-2" type="checkbox" name="Remember" value="Remember">
                    <label class="mr-2" for="Remember"> Remember me</label>
                </div>
                <div class="col-lg-6 col-6 mt-3 text-right">
                    <a href="#" class="color-black"> Forgot Password?</a>
                </div>
                <div class="col-lg-12 mt-5">
                    <input type="submit" form="login-form" class=" btn cart-btn w-100" value="Sign In">{{--<a href="#" class=" btn cart-btn w-100">Sign In</a>--}}
                </div>
                <div class="col-lg-12 mt-3 mb-5">
                    <a href="{{ route('public.register') }}" class=" btn border-btn w-100">Register</a>
                </div>
            </div>
            </form>

        </div>
        <div class="col-lg-3"></div>
    </div>
</section>