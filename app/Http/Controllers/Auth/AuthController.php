<?php

namespace App\Http\Controllers\Auth;

use App\User,Auth;
use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    //protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function login(Request $request)
    {
        $this->validate($request,[
            'email' => 'required|email', 'password' => 'required',
        ]);

        $credentials = array(
            'email' => $request->email,
            'password' => $request->password,
            'status' => 1
        );
        if (Auth::validate(['email' => $request->email, 'password' => $request->password, 'status' => 0])) {
            return back()->withErrors(['email' => 'You are not active']);
        }
        else if (Auth::attempt($credentials, $request->has('remember')))
        {
            if(Auth::user()->user_roles=='admin')
            {
                return redirect('/admin');
            }

            else if(Auth::user()->user_roles=='author')
            {
                return redirect('/author');
            }

            else if(Auth::user()->user_roles=='editor')
            {
                return redirect('/editor');
            }
            else
            {
                return redirect('/user');
            }
        }

        else
        {
            echo 'i am here';exit;
            return back()->withErrors(['email' => 'These credentials do not match our records']);
        }
    }


}
