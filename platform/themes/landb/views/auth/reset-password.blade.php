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
            <h2 class="mt-5 mb-4 text-center signin-head">RESET PASSWORD</h2>
            <form class="" id="login-form" method="POST" action="{{ route('password.post.reset') }}">
                @csrf
                <input class="" type="hidden" name="token" id="" value="{{ $token }}"  placeholder=""/>
                <div class="row">
                    <div class="col-lg-12">
                        <p class="textbox-label">Email</p>
                        <input class="input-textbox" type="email" name="email" value="{{ old('email') }}" />
                        @if ($errors->has('email'))
                            <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-12">
                        <p class="textbox-label">New Password</p>
                        <input class="input-textbox" type="password" name="password" value="" />
                        @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-12">
                        <p class="textbox-label">Confirm New Password</p>
                        <input class="input-textbox" type="password" name="password_confirmation" value="" />
                        @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div class="col-lg-12 mt-5">
                        <input type="submit" form="login-form" class=" btn border-btn w-100" value="Reset Password">{{--<a href="#" class=" btn cart-btn w-100">Sign In</a>--}}
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
