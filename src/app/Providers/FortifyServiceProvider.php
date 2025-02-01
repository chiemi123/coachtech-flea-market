<?php

namespace App\Providers;


use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;



class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);


        // 会員登録画面
        Fortify::registerView(function () {
            return view('auth.register');


            Fortify::redirects('register', '/mypage/profile'); // プロフィール設定画面にリダイレクト
        });


        Fortify::authenticateThrough(function (Request $request) {
            
            return array_filter([
                // ログイン時のレート制限チェック
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,

                // カスタムバリデーションアクション
                \App\Actions\ValidateCustomLoginRequest::class,

                // 認証の試行
                AttemptToAuthenticate::class,

                // 認証成功時のセッション準備
                PrepareAuthenticatedSession::class,
            ]);
        });



        // ログイン画面
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::redirects('login', '/'); // ログイン後は商品一覧画面へリダイレクト

        Fortify::redirects('failed-login', '/login'); // ログイン失敗時のリダイレクト先をURLで明示的に指定

        // ログイン時のレートリミット
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
