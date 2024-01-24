<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                ->withSuccess('Signed in');
        }

        return redirect("login")->withSuccess('Login details are not valid');
    }

    public function registration()
    {
        return view('auth.register');
    }

    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = $this->create($data);

        return redirect("login")->withSuccess('Please login.');
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function dashboard()
    {
        if (Auth::check()) {
            $crud = DB::table('crud')->where('isdeleted',0)->get();
            return view('auth.dashboard',compact('crud'));
        }
        return redirect("login")->withSuccess('You are not allowed to access');
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('login');
    }

    public function savecrud(Request $request){

        // dd($request);

        if (!empty($request->input('editcrud'))) {
        foreach ($request->input('editcrud') as $index => $data) {
                if($data['id'] != null || $data['id'] != ''){
                    DB::table('crud')
                        ->where('id', $data['id'])
                        ->update($data);
                    } else {
                        // Insert a new record and exclude the 'id' field

                    unset($data['id']); // Remove the 'id' field
                    DB::table('crud')->insert($data);
                    }
        }
        }

        return response()->json(array('status'=> 'OK'));

    }
}