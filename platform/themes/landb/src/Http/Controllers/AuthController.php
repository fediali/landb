<?php

namespace Theme\Landb\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use SeoHelper;
use Symfony\Component\HttpFoundation\Response;
use Theme;
use URL;
/*use Botble\Theme\Http\Controllers\PublicController;*/

class AuthController extends Controller
{
  use AuthenticatesUsers, LogoutGuardTrait;

  /**
   * Where to redirect users after login / registration.
   *
   * @var string
   */
  /*public $redirectTo;*/

  /**
   * Create a new controller instance.
   */
  public function __construct()
  {
    $this->middleware('customer.guest', ['except' => 'logout']);
    session(['url.intended' => URL::previous()]);
    $this->redirectTo = session()->get('url.intended');
  }

  public function showLoginForm()
  {
    SeoHelper::setTitle(__('Login'));

    Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Login'), route('customer.login'));

    return Theme::scope('auth.login', [], 'plugins/ecommerce::themes.customers.login')->render();
  }

  public function showRegisterForm()
  {
    SeoHelper::setTitle(__('Register'));

    Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Login'), route('customer.login'));

    return Theme::scope('auth.register', [], 'plugins/ecommerce::themes.customers.register')->render();
  }

  /**
   * Get the guard to be used during authentication.
   *
   * @return StatefulGuard
   */
  protected function guard()
  {
    return auth('customer');
  }

  /**
   * @param Request $request
   * @return Response|void
   * @throws ValidationException
   * @throws ValidationException
   */
  public function login(Request $request)
  {
    $this->validateLogin($request);

    // If the class is using the ThrottlesLogins trait, we can automatically throttle
    // the login attempts for this application. We'll key this by the username and
    // the IP address of the client making these requests into this application.
    if ($this->hasTooManyLoginAttempts($request)) {
      $this->fireLockoutEvent($request);

      $this->sendLockoutResponse($request);
    }

    if ($this->attemptLogin($request)) {

      return $this->sendLoginResponse($request);
    }

    // If the login attempt was unsuccessful we will increment the number of attempts
    // to login and redirect the user back to the login form. Of course, when this
    // user surpasses their maximum number of attempts they will get locked out.
    $this->incrementLoginAttempts($request);

    return $this->sendFailedLoginResponse($request);
  }

  /**
   * Log the user out of the application.
   *
   * @param Request $request
   * @return RedirectResponse
   */
  public function logout(Request $request)
  {
    $activeGuards = 0;
    $this->guard()->logout();

    foreach (config('auth.guards', []) as $guard => $guardConfig) {
      if ($guardConfig['driver'] !== 'session') {
        continue;
      }
      if ($this->isActiveGuard($request, $guard)) {
        $activeGuards++;
      }
    }

    if (!$activeGuards) {
      $request->session()->flush();
      $request->session()->regenerate();
    }

    return $this->loggedOut($request) ?: redirect('/');
  }

  public function process_signup(Request $request)
  {
    $request->validate([
        'name' => 'required',
        'email' => 'required',
        'password' => 'required'
    ]);

    $user = User::create([
        'name' => trim($request->input('name')),
        'email' => strtolower($request->input('email')),
        'password' => bcrypt($request->input('password')),
    ]);

    session()->flash('message', 'Your account is created');

    return redirect()->route('login');
  }

  protected function authenticated(Request $request, $user)
  {
    $user->update(['last_visit' => Carbon::now()]);
  }
}
