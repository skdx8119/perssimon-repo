<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'avatar'=>['image', 'max:1024'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

         // userテーブルのデータ
         $attr =[
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        //avatarの保存
        if(request()->hasFile('avatar')) {
            $name = request()->file('avatar')->getClientOriginalName();
            $avatar = date('Ymd_His').'_'.$name;
            $path = request()->file('avatar')->storeAs('avatar', $avatar, 's3');
            //avatarのURLを生成
            $avatar_url = Storage::disk('s3')->url($path);
            //avatarのURLをデータに追加
            $attr['avatar']=$avatar_url;
        }
        $user=User::create($attr);

        event(new Registered($user));

        // 役割付与
        $user->roles()->attach(2);

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
