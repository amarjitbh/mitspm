<?php

namespace App\Http\Controllers\Auth;

use App\Countries;
use App\CountriesTimeZone;
use App\Http\Requests\RegisterUser;
use App\User;
use Symfony\Component\HttpFoundation\Request;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
     //   $this->middleware('guest');
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
            'country_id' => $data['countries'],
            'country_timezone_id' => $data['timezone'],
        ]);
    }
    public function register(){

        $data['countries'] = (new Countries())->get();
        return view('auth.register',$data);
    }

    public function getTimeZone(Request $request){

        $countryId = $request->input('country_id');
        //echo $countryId;die;
        $timeZone = (new CountriesTimeZone())->getTimezone($countryId);
        //pr($timeZone);
        if(!empty($timeZone)){
            return view('users.get-time-zone',compact('timeZone'));
        }else{

            echo '0';
        }
    }

    public function registerUser(RegisterUser $request){

        try {

            $name = $request->input('name');
            $password = $request->input('password');
            $email = $request->input('email');
            $countries = $request->input('countries');
            $timezone = $request->input('timezone');
            //echo $name;die;
            $ary = array(
                'name' => $name,
                'password' => $password,
                'email' => $email,
                'country_id' => $countries,
                'country_timezone_id' => $timezone,
                'created_at' => date('Y-m-d h:i:s'),
            );

            (new User())->insert($ary);

            $request->session()->flash('success', 'user successfully register');
            return redirect('login');
        }catch (\Exception $e){
            pr($e->getMessage());
            /*$request->session()->flash('success', $e->getMessage());
            return redirect('user-register');*/
        }
    }
}
