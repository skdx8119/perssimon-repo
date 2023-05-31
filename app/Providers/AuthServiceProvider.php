<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        App\Models\Post::class=>App\Policies\PostPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        Gate::define('admin', function($user) {
            foreach($user->roles as $role){
                if($role->name=='admin') {
                    return true;
                }
            }
            return false;
        });

        Gate::define('free', function($user) {
            foreach($user->roles as $role){
                if($role->name=='free') {
                    return true;
                }
            }
            return false;
        });

        Gate::define('create-post', function ($user) {
            // 'free' ロールがない場合、ユーザーが投稿を作成できると判断します。
            return !Gate::allows('free', $user);
        });
    }
}
