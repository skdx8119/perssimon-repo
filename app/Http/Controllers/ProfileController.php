<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\Role;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // アバター画像の保存
        if($request->hasFile('avatar')) {
            // 古いアバター削除コード
            $user=User::find(Auth::id());
            if($user->avatar!=='user_default.jpg'){
                Storage::disk('s3')->delete('avatar/'.$user->avatar);
            }

            $path = Storage::disk('s3')->putFile('avatar', $request->file('avatar'), 'public');
            $avatar=basename($path);
            $request->user()->avatar = $avatar;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function index() {
        $users = User::all();
        return view('profile.index', compact('users'));
    }

    public function adedit(User $user){
        $admin=true;
        $roles=Role::all();

        return view('profile.edit',[
            'user'=>$user,
            'admin'=>$admin,
            'roles'=>$roles
        ]);
    }

    public function adupdate(Request $request): RedirectResponse
{
    $inputs=$request->validate([
        'name' => ['string', 'max:255'],
        'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($request->user)],
        'avatar'=> ['image', 'max:1024'],
        'user'=> ['required']
    ]);

    $user=User::find($request->user);

    // アバター画像の保存
    if(request()->hasFile('avatar')) {
        // 古いアバター削除用コード
        if ($user->avatar!=='user_default.jpg') {
            Storage::disk('s3')->delete('avatar/'.$user->avatar);
        }
        $path = Storage::disk('s3')->putFile('avatar', $request->file('avatar'), 'public');
        $avatar=basename($path);
        $user->avatar = $avatar;
    }

    $user->name=$inputs['name'];
    $user->email=$inputs['email'];
    $user->save();

    return Redirect::route('profile.adedit', compact('user'))->with('status', 'profile-updated');
}

public function addestroy(User $user){
    if ($user->avatar!=='user_default.jpg') {
        Storage::disk('s3')->delete('avatar/'.$user->avatar);
    }
    $user->roles()->detach();
    $user->delete();
    return back()->with('message','ユーザーを削除しました');
}
}
